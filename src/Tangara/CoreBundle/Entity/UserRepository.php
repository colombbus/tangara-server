<?php
namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository {
    
    public function getSearchQuery($string) {
        $qb = $this->createQueryBuilder('u')
                    ->where('u.username like :string')
                    ->orderBy('u.id')
                    ->setParameter('string','%'.$string.'%');
        return $qb->getQuery();
    }
    
}