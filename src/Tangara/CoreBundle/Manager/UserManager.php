<?php

use FOS\UserBundle\Entity\UserManager as FOSUserManager;

class UserManager extends FOSUserManager {
    
    public function __construct(EncoderFactoryInterface $encoderFactory, CanonicalizerInterface $usernameCanonicalizer, CanonicalizerInterface $emailCanonicalizer)
    {
        parent::__construct($encoderFactory, $usernameCanonicalizer, $emailCanonicalizer);
    }
    
}
