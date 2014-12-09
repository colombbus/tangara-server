<?php

namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;


class FileRepository extends EntityRepository {
    
    public function getAllProjectFiles($name){
        
        $query = $this->createQueryBuilder('a')
                ->join('a.project', 'b')
                ->where('b.name = :name')
                ->setParameter('name', $name);
        
        $Docs = $query->getQuery()->getResult();
        
        return $Docs;
    }
    
    public function getAllProjectPrograms($project) {
        $query = $this->createQueryBuilder('a')
                ->where('a.project = :project')
                ->andWhere('a.program = true')
                ->setParameter('project', $project);
        
        $programs = $query->getQuery()->getResult();
        
        return $programs;
        
    }
    
    public function getAllProjectResources($project) {
        $query = $this->createQueryBuilder('a')
                ->where('a.project = :project')
                ->andWhere('a.program = false')
                ->andWhere('a.deleted = false')
                ->setParameter('project', $project);
        
        $resources = $query->getQuery()->getResult();
        
        return $resources;
        
    }

    public function getProjectProgram($project, $name) {
        $query = $this->createQueryBuilder('a')
                ->where('a.project = :project')
                ->andWhere('a.program = true')
                ->andWhere('a.name = :name')
                ->setParameters(array('project'=> $project, 'name'=> $name));
        try {
            $program = $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            $program = false;
        }
        
        return $program;
    }

    public function getProjectResource($project, $name) {
        $query = $this->createQueryBuilder('a')
                ->where('a.project = :project')
                ->andWhere('a.program = false')
                ->andWhere('a.name = :name')
                ->setParameters(array('project'=> $project, 'name'=> $name));
        try {
            $resource = $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            $resource = false;
        }
        
        return $resource;
    }

    
}
