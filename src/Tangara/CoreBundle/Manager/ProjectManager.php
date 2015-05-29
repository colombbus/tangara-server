<?php

/*
 * Copyright (C) 2014 Régis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of ProjectManager
 *
 * @author Régis
 */

namespace Tangara\CoreBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\Orm\NoResultException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\SecurityContext;
use Tangara\CoreBundle\Entity\File;
use Tangara\CoreBundle\Entity\Log;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Entity\User;
use Tangara\CoreBundle\Manager\BaseManager;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;


class ProjectManager extends BaseManager {

    protected $em;
    protected $projectsDirectory;
    protected $user;
    protected $context;
    protected $acl;

    
    public function __construct(EntityManager $em, $path, SecurityContext $context, $acl) {
        $this->em = $em;
        $this->projectsDirectory = $path;
        $this->context = $context;
        $token = $context->getToken();
        if (isset($token)) {
            $this->user = $token->getUser();
        }
        $this->acl = $acl;
    }

    public function loadProject($projectId) {
        return $this->getRepository()
                        ->findOneBy(array('id' => $projectId));
    }

    /**
     * Save Project entity
     *
     * @param Project $project
     */
    public function saveProject(Project $project) {
        $this->persistAndFlush($project);
    }
    
    public function setHome(Project $project, User $user) {
        $project->setOwner($user);
        $user->setHome($project);
        $this->em->persist($project);
        $this->em->persist($user);
        $this->em->flush();
        $objectIdentity = ObjectIdentity::fromDomainObject($project);
        $entry = $this->acl->createAcl($objectIdentity);
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        $entry->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        $this->acl->updateAcl($entry);
    }

    public function isAuthorized(Project $project, User $user) {
        // For now, we just check that project is user's home
        // TODO: use ACL
        return ($project->getId() == $user->getHome()->getId() || $project->getReadOnly());
    }

    public function mayView(Project $project) {
        return $this->context->isGranted('ROLE_ADMIN') || $this->context->isGranted('VIEW', $project) || $project->getReadOnly();
    }
    
    public function mayExecute(Project $project) {
        return $this->context->isGranted('ROLE_ADMIN') || $this->context->isGranted('VIEW', $project) || $project->getReadOnly() || $project->getPublished();
    }
    
    public function mayContribute(Project $project) {
        return $this->context->isGranted('ROLE_ADMIN') || $this->context->isGranted('CREATE', $project) || $project->getReadOnly();
    }

    public function mayEdit(Project $project) {
        return $this->context->isGranted('ROLE_ADMIN') || $this->context->isGranted('EDIT', $project);
    }
    
    public function isOwner(Project $project) {
        return $this->context->isGranted('OWNER', $project);
    }
    
    public function isHomeProject(Project $project, User $user) {
        return $user->getHome() === $project;
    }

    public function isPublic(Project $project) {
        return ($project->getPublished());
    }

    public function isProjectFile(Project $project, $fileName, $program=false) {
        return ($this->getProjectFile($project, $fileName, $program)!==false);
    }
    
    public function getProjectFile(Project $project, $fileName, $program=false) {
        $qb = $this->em->getRepository('TangaraCoreBundle:File')->createQueryBuilder('a')
                ->where('a.project = :project')
                ->andWhere('a.name = :name')
                ->andWhere('a.program = :program')
                ->setParameters(array('project' => $project, 'program' => $program, 'name' => $fileName));
        $query = $qb->getQuery();
        try {
            $file = $query->getSingleResult();
        } catch (NoResultException $e) {
            return false;
        }
        return $file;
    }
    
    public function getProjectPath(Project $project) {
        $projectPath = $this->projectsDirectory . '/' . $project->getId();
        $fs = new Filesystem();
        // If path does not exist, create it
        if (!$fs->exists($projectPath)) {
            $fs->mkdir($projectPath);
        }
        return $projectPath;
    }
    
    public function getNewStorageName(Project $project) {
        $projectPath = $this->getProjectPath($project);
        while (true) {
            $storage = uniqid('tgr_');
            if (!file_exists($projectPath . $storage)) {
                break;
            }
        }
        return $storage;
    }
    
    public function createFile(Project $project, $fileName, $program=false) {
        // Check that fileName does not exist already
        $file = $this->getProjectFile($project, $fileName, $program);
        if ($file) {
            // fileName exists: check that file is deleted
            if (!$file->getDeleted()) {
                // file  is not deleted: return false
                return false;
            }
            // reuse previously deleted file
            $file->setVersion($file->getVersion()+1);
        }  else {
            // Create file
            $file = new File();
        }
        
        $file->setProject($project);
        $file->setName($fileName);
        $file->setProgram($program);
        $file->setDeleted(false);
        
        // create storage file
        $storage = $this->getNewStorageName($project);
        $file->setStorageName($storage);
        $this->em->persist($file);
        
        // Update log
        $entry = new Log();
        $entry->setProject($project);
        $entry->setOperation("create");
        $entry->setData(json_encode(array('name'=>$file->getName(), 'program'=>$program)));
        $entry->setUser($this->user);
        $this->em->persist($entry);
        
        $this->em->flush();
        
        return $file;
    }
    
    public function removeFile(Project $project, File $file) {
        // Update log
        $entry = new Log();
        $entry->setProject($project);
        $entry->setOperation("remove");
        $entry->setData(json_encode(array('name'=>$file->getName(), 'program'=>$file->getProgram())));
        $entry->setUser($this->user);
        $this->em->persist($entry);
        $this->em->flush();
    }
    
    public function updateFile(Project $project, File $file) {
        // Update log
        $entry = new Log();
        $entry->setProject($project);
        $entry->setOperation("update");
        $entry->setData(json_encode(array('name'=>$file->getName(), 'program'=>$file->getProgram())));
        $entry->setUser($this->user);
        $this->em->persist($entry);
        $this->em->flush();
    }
    
    public function getRepository() {
        return $this->em->getRepository('TangaraCoreBundle:Project');
    }
    
    public function getAllResources(Project $project) {
        return $this->em->getRepository('TangaraCoreBundle:File')->getAllProjectResources($project);        
    }
    
    public function getAllPrograms(Project $project) {
        return $this->em->getRepository('TangaraCoreBundle:File')->getAllProjectPrograms($project);        
    }

}
