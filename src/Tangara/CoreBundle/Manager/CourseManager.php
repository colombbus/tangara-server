<?php

/*
 * Copyright (C) 2014 Régis
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Description of ProjectManager
 *
 * @author Régis
 */

namespace Tangara\CoreBundle\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\Orm\NoResultException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\SecurityContext;
use Tangara\CoreBundle\Entity\File;
use Tangara\CoreBundle\Entity\Log;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Entity\Course;
use Tangara\CoreBundle\Entity\User;
use Tangara\CoreBundle\Manager\BaseManager;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;


class CourseManager extends BaseManager {

    protected $em;
    protected $user;
    protected $context;
    protected $acl;

    
    public function __construct(EntityManager $em, SecurityContext $context, $acl) {
        $this->em = $em;
        $this->context = $context;
        $token = $context->getToken();
        if (isset($token)) {
            $this->user = $token->getUser();
        }
        $this->acl = $acl;
    }

    public function loadCourse($courseId) {
        return $this->getRepository()
                        ->findOneBy(array('id' => $courseId));
    }

    /**
     * Save Course entity
     *
     * @param Course $course
     */
    public function saveCourse(Course $course) {
        $this->persistAndFlush($course);
    }
    
    public function mayView(Course $course) {
        return true;
    }
    
    public function mayExecute(Course $course) {
        return true;
    }
    
    public function maySelect(Course $course) {
        return $this->context->isGranted('ROLE_ADMIN');
    }
    
    public function mayContribute(Course $course) {
        return $this->context->isGranted('ROLE_ADMIN');
    }

    public function mayEdit(Course $course) {
        return $this->context->isGranted('ROLE_ADMIN');
    }
    
    public function getRepository() {
        return $this->em->getRepository('TangaraCoreBundle:Course');
    }
    
    public function getSteps(Course $course) {
        $query = $this->em->createQuery(
            'SELECT s
             FROM TangaraCoreBundle:Step s JOIN s.courses cs JOIN cs.course c
             WHERE c.id = :id
             ORDER BY cs.position ASC')
            ->setParameter('id', $course->getId());
        return $query->getResult();        
    }
}
