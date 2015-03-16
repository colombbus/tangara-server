<?php
namespace Tangara\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Entity\User;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;

class ProjectRepository extends EntityRepository {
       
    public function searchProject($string) {
        
        $qb = $this->createQueryBuilder('u')
                    ->select('u')
                    ->where('u.name like :name')
                    ->orderBy('u.id')
                    ->setParameter('name','%'.$string.'%');
        return $qb->getQuery()->getResult();
        
    }
    
    public function getPublishedProjects(){
        $query = $this->createQueryBuilder('p')
                ->where('p.published = true')
                ->orderBy('p.created', 'DESC');
        
        $projects = $query->getQuery()->getResult();
        
        return $projects;

    }
    
    public function getOwnedProjects(User $user) {
        $query = $this->createQueryBuilder('p')
                ->where('p.owner = :owner')
                ->orderBy('p.created', 'DESC')
                ->setParameter('owner', $user);
        
        $projects = $query->getQuery()->getResult();
        
        return $projects;
    }
    
    public function getReadonlyProjects(User $user){
        $query = $this->createQueryBuilder('p')
                ->where('p.readonly = true')
                ->andwhere('p.owner != :owner')
                ->orderBy('p.created', 'DESC')
                ->setParameter('owner', $user);
        
        $projects = $query->getQuery()->getResult();
        
        return $projects;
    }
    
}