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
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Entity\Document;
use Tangara\CoreBundle\Entity\User as User;

class DocumentManager extends BaseManager {

    protected $em;
    private $directory;

    public function __construct(EntityManager $em) {
        $this->em = $em;
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
}
