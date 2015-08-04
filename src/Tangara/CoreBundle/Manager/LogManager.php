<?php

namespace Tangara\CoreBundle\Manager;

use Tangara\CoreBundle\Manager\BaseManager;
use Doctrine\ORM\EntityManager;

class LogManager extends BaseManager {
    
    protected $em;    
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function getRepository() {
        return $this->em->getRepository('TangaraCoreBundle:Log');
    }
    
    public function removeProjectEntries($project) {
        $entries = $this->getRepository()->getProjectLogEntries($project);
        foreach ($entries as $entry) {
            $this->em->remove($entry);
        }
        $this->em->flush();
    }
}
