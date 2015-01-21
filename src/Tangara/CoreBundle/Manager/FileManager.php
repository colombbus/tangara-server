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
 * Description of FileManager
 *
 * @author Régis
 */

namespace Tangara\CoreBundle\Manager;

use Doctrine\ORM\EntityManager;
use Tangara\CoreBundle\Manager\BaseManager;
use Tangara\CoreBundle\Entity\File;
use Symfony\Component\Filesystem\Filesystem;

class FileManager extends BaseManager {

    protected $em;
    protected $pm;
    private $directory;
    private $allowedMimeTypes;
    private $maxResourceSize;

    public function __construct(EntityManager $em, ProjectManager $pm, $allowed, $size) {
        $this->em = $em;
        $this->pm = $pm;
        $this->directory = '/home/tangara';
        foreach ($allowed as $type=>$values) {
            foreach ($values as $value) {
                $this->allowedMimeTypes[$value] = $type;
            }
        }
        $this->maxResourceSize = $size;
    }

    public function loadFile($fileId) {
        return $this->getRepository()
                        ->findOneBy(array('id' => $fileId));
    }

    /**
     * Save File entity
     *
     * @param File $file
     */
    public function saveFile(File $file) {
        $this->persistAndFlush($file);
    }

    public function getUploadDirectory() {
        return $this->directory;
    }

    public function setUploadDirectory($directory) {
        $this->directory = $directory;
        return $this;
    }

    public function getRepository() {
        return $this->em->getRepository('TangaraCoreBundle:File');
    }
    
    public function getFilePath(File $file) {
        $name = $file->getStorageName();
        if (!isset($name)) {
            $name = $file->getName();
        }
        return $name;
    }
    
    public function remove(File $file, $anyway = false) {
        if ($anyway || $file->getProgram()) {
            // remove database entry
            $this->em->remove($file);
        } else {
            // set file as deleted
            $file->setDeleted(true);
        }
        $this->em->flush();

        // Remove file from project
        $project = $file->getProject();
        $this->pm->removeFile($project, $file);

        // Remove files
        $projectPath = $this->pm->getProjectPath($project);
        $fs = new Filesystem();
        $storageName = $this->getFilePath($file);
        if ($file->getProgram()) {
            // File is a program
            $codePath = $projectPath. "/".$storageName."_code";
            $statementsPath = $projectPath . "/".$storageName."_statements";
            if ($fs->exists($codePath)) {
                $fs->remove($codePath);
            }
            if ($fs->exists($statementsPath)) {
                $fs->remove($statementsPath);
            }
        } else {
            // File is not a program
            $filePath = $projectPath. "/".$storageName;
            if ($fs->exists($filePath)) {
                $fs->remove($filePath);
            }
        }
    }

    public function updateProgram(File $program, $code, $statements) {
        // find corresponding project
        $project = $program->getProject();
        $projectPath = $this->pm->getProjectPath($project);
        $storageName = $this->getFilePath($program);
        
        $codePath = $projectPath . "/".$storageName."_code";
        $statementsPath = $projectPath . "/".$storageName."_statements";

        if ($code){
            file_put_contents($codePath, $code, LOCK_EX);
        } else {
            touch($codePath);
        }
        if ($statements) {
            file_put_contents($statementsPath, $statements, LOCK_EX);
        } else {
            touch($satementsPath);
        }
        
        // update project
        $this->pm->updateFile($project, $program);
    }

    
    public function checkResource($file) {
        // Check upload
        if (!$file->isValid()) {
            return 'resource_not_uploaded';
        }
        
        // Check mime type
        $type = $file->getMimeType();
        if (!isset($type) || !isset($this->allowedMimeTypes[$type])) {
            return 'resource_mime_type_not_allowed';
        }
        
        // Check size
        if ($file->getClientSize()>$this->maxResourceSize) {
            return 'resource_file_too_large';
        }
        
        return true;
    }
    
    public function getResourceType($file) {
        $type = $file->getMimeType();
        if (isset($type) && isset($this->allowedMimeTypes[$type])) {
            return $this->allowedMimeTypes[$type];
        }
        return false;
    }

    public function getResourceTypeFromMIME($type) {
        if (isset($this->allowedMimeTypes[$type])) {
            return $this->allowedMimeTypes[$type];
        }
        return false;
    }
    
    
}
