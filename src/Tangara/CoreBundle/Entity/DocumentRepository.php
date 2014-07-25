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
    
    
    
}
