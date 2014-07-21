<?php

namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository;


class UserRepository extends EntityRepository {

    //check if the user exist
    public function isUserByName($name){
        
        $user = $this->findByUserName($name);
        
        if($user == null){
            return false;
        }
        return true;
    }

}