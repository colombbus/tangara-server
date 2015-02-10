<?php
namespace Tangara\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProfileRepository extends EntityRepository {
    
    public function searchData($string) {
        $qb = $this->createQueryBuilder('u')
                    ->select('u')
                    ->where('u.username like :string')
                    ->orderBy('u.id')
                    ->setParameter('string', $string);
        return $qb->getQuery()->getResult();
    }
    
}