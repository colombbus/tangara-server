<?php
namespace Tangara\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class ProfileRepository extends EntityRepository {
    
    public function searchData($string) {
        $qb = $this->createQueryBuilder('u')
                    ->select('u')
                    ->where('u.username like :string')
                    ->orderBy('u.id')
                    ->setParameter('string','%'.$string.'%');
        return $qb->getQuery()->getArrayResult();
    }
    
    public function autocompleteData($string) {
        $qb = $this->createQueryBuilder('u')
                    ->select('u')
                    ->where('u.username like :string')
                    ->orderBy('u.id')
                    ->setParameter('string','%'.$string.'%');
        $tab =  $qb->getQuery()->getArrayResult();
        $array = array();
		foreach($tab as $data)
		{
			$array[] = $data['username'];
		}
		return $array;
    }
    
    
    
}