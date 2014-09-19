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
 * Description of DocumentManager
 *
 * @author Régis
 */

namespace Tangara\CoreBundle\Manager;

use Doctrine\ORM\EntityManager;
use Tangara\CoreBundle\Manager\BaseManager;
use Tangara\CoreBundle\Entity\Document;
use Symfony\Component\Filesystem\Filesystem;

class DocumentManager extends BaseManager {

    protected $em;
    protected $pm;
    private $directory;

    public function __construct(EntityManager $em, ProjectManager $pm) {
        $this->em = $em;
        $this->pm = $pm;
        $this->directory = '/home/tangara';
    }

    public function loadDocument($documentId) {
        return $this->getRepository()
                        ->findOneBy(array('id' => $documentId));
    }

    /**
     * Save Document entity
     *
     * @param Document $document
     */
    public function saveDocument(Document $document) {
        $this->persistAndFlush($document);
    }

    public function getUploadDirectory() {
        return $this->directory;
    }

    public function setUploadDirectory($directory) {
        $this->directory = $directory;
        return $this;
    }

    public function getRepository() {
        return $this->em->getRepository('TangaraCoreBundle:Document');
    }
    
    public function remove(Document $file) {
        // Remove database entry
        $this->em->remove($file);
        $this->em->flush();

        // Remove document from project
        $project = $file->getProject();
        $this->pm->removeFile($project, $file);

        // Remove files
        $projectPath = $this->pm->getProjectPath($project);
        $fs = new Filesystem();
        if ($file->getProgram()) {
            // File is a program
            $codePath = $projectPath. "/".$file->getPath()."_code";
            $statementsPath = $projectPath . "/".$file->getPath()."_statements";
            if ($fs->exists($codePath)) {
                $fs->remove($codePath);
            }
            if ($fs->exists($statementsPath)) {
                $fs->remove($statementsPath);
            }
        } else {
            // File is not a program
            $filePath = $projectPath. "/".$file->getPath();
            if ($fs->exists($filePath)) {
                $fs->remove($filePath);
            }
        }
    }
        
    public function updateProgram(Document $program, $code, $statements) {
        // find corresponding project
        $project = $program->getProject();
        $projectPath = $this->pm->getProjectPath($project);
        
        $codePath = $projectPath . "/".$program->getPath()."_code";
        $statementsPath = $projectPath . "/".$program->getPath()."_statements";

        file_put_contents($codePath, $code, LOCK_EX);
        file_put_contents($statementsPath, $statements, LOCK_EX);
        
        // update project
        $this->pm->updateFile($project, $program);
    }
}
