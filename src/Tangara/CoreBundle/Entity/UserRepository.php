<?php

namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;


class UserRepository extends EntityRepository {

    public function getAllProcjects($pname) {
        /*
        $query = $this->getEntityManager()
                        ->createQuery('
            SELECT p, c FROM TangaraCoreBundle:User p
            JOIN p.projects c
            WHERE p.name = :pname'
                        )->setParameter('pname', $pname);

        try {
            return $query->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
        */
        
    }

}