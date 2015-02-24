<?php
namespace Tangara\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Tangara\CoreBundle\Entity\Project;
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
//        
//        $query = $em->createQuery('SELECT u FROM MyProject\Model\User u WHERE u.age > 20');
//        return $query->getResult();
        
    }
}