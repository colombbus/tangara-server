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
use Tangara\CoreBundle\Manager\BaseManager;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Entity\Document;
use Tangara\CoreBundle\Entity\User as User;

class ProjectManager extends BaseManager {

    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
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

    public function isAuthorized(Project $project, User $testedUser) {
        $groupProject = $project->getGroup();
        $userProject = $project->getUser();

        if ($groupProject) {
            // group project
            $member = $groupProject->getUsers();
            foreach ($member as $t) {
                if ($t->getId() == $testedUser->getId())
                    return true;
            }
            return false;
        } else {
            // user project
            $uid = $userProject->getId();
            
            if ($uid == $testedUser->getId())
                return true;
            else
                return false;
        }

    }
    
    public function isProjectFile(Project $project, $filename) {
        $documentList = $this->em->getRepository('TangaraCoreBundle:Document')
                ->findByOwnerProject($project->getId());
        
        foreach ($documentList as $d) {
            if ($d->getPath() == $filename)
                return true;
        }
        return false;
       
    }

    public function getRepository() {
        return $this->em->getRepository('TangaraCoreBundle:Project');
    }

}
