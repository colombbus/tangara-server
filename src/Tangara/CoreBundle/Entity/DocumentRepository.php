<?php

namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;


class DocumentRepository extends EntityRepository {
    
    public function getAllProjectDocuments($name){
        
        $query = $this->createQueryBuilder('a')
                ->join('a.ownerProject', 'b')
                ->where('b.name = :name')
                ->setParameter('name', $name);
        
        $Docs = $query->getQuery()->getResult();
        
        return $Docs;
    }
    
    public function getAllProjectPrograms($projectId) {
        $query = $this->createQueryBuilder('a')
                ->where('a.ownerProject = :projectId')
                ->andWhere('a.program = true')
                ->setParameter('projectId', $projectId);
        
        $programs = $query->getQuery()->getResult();
        
        return $programs;
        
    }
    
    public function getAllProjectResources($projectId) {
        $query = $this->createQueryBuilder('a')
                ->where('a.ownerProject = :projectId')
                ->andWhere('a.program = false')
                ->setParameter('projectId', $projectId);
        
        $resources = $query->getQuery()->getResult();
        
        return $resources;
        
    }

    public function getProjectProgram($projectId, $name) {
        $query = $this->createQueryBuilder('a')
                ->where('a.ownerProject = :projectId')
                ->andWhere('a.program = true')
                ->andWhere('a.path = :name')
                ->setParameters(array('projectId'=> $projectId, 'name'=> $name));
        try {
            $program = $query->getQuery()->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $program = false;
        }
        
        return $program;
    }

    public function getProjectResource($projectId, $name) {
        $query = $this->createQueryBuilder('a')
                ->where('a.ownerProject = :projectId')
                ->andWhere('a.program = false')
                ->andWhere('a.path = :name')
                ->setParameters(array('projectId'=> $projectId, 'name'=> $name));
        try {
            $resource = $query->getQuery()->getSingleResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $resource = false;
        }
        
        return $resource;
    }

    
}
