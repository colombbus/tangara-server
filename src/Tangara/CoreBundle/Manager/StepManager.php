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
use Symfony\Component\Security\Core\SecurityContext;
use Tangara\CoreBundle\Entity\Project;
use Tangara\CoreBundle\Entity\Step;
use Tangara\CoreBundle\Entity\Course;
use Tangara\CoreBundle\Entity\CourseStep;
use Tangara\CoreBundle\Manager\BaseManager;


class StepManager extends BaseManager {

    protected $em;
    protected $user;
    protected $context;
    protected $acl;
    protected $projectManager;

    
    public function __construct(EntityManager $em, SecurityContext $context, $acl, $pm) {
        $this->em = $em;
        $this->context = $context;
        $token = $context->getToken();
        if (isset($token)) {
            $this->user = $token->getUser();
        }
        $this->acl = $acl;
        $this->projectManager = $pm;
    }

    public function loadStep($stepId) {
        return $this->getRepository()
                        ->findOneBy(array('id' => $stepId));
    }

    /**
     * Save Course entity
     *
     * @param Course $course
     */
    public function saveStep(Step $step) {
        $test = $step->getProject();
        if (!$test) {
            $project = new Project();
            $project->setName($step->getName());
            $this->projectManager->saveProject($project);
            $this->projectManager->setOwner($project,$this->user, true);
            $step->setProject($project);
        }
        $this->persistAndFlush($step);        
    }
    
    public function addToCourse(Step $step, Course $course) {
        $query = $this->em->createQuery(
            'SELECT MAX(cs.position)
                FROM TangaraCoreBundle:CourseStep cs JOIN cs.course c
                 WHERE c.id = :id')
            ->setParameter('id', $course->getId());
        $nextPosition = $query->getSingleScalarResult()+1;
        $record = new CourseStep();
        $record->setStep($step);
        $record->setCourse($course);
        $record->setPosition($nextPosition);
        $this->persistAndFlush($record);
    }
    
    public function removeFromCourse(Step $step, Course $course) {
        $repository = $this->em->getRepository('TangaraCoreBundle:CourseStep');
        $entries = $repository->getCourseSteps($course, $step);
        foreach ($entries as $entry) {
            $this->em->remove($entry);
        }
        $this->em->flush();
    }

    public function delete(Step $step) {
        // remove coursesteps entries
        $courses = $step->getCourses();
        foreach ($courses as $course) {
            $this->removeFromCourse($step, $course->getCourse());
        }
        $project = $step->getProject();
        // remove database entry
        $this->em->remove($step);
        $this->em->flush();
        // remove associated project
        $this->projectManager->delete($project);
    }
    
    public function mayView(Step $step) {
        return true;
    }
    
    public function mayExecute(Step $step) {
        return true;
    }
    
    public function maySelect(Step $step) {
        return $this->context->isGranted('ROLE_ADMIN');
    }
    
    public function mayContribute(Step $step) {
        return $this->context->isGranted('ROLE_ADMIN');
    }

    public function mayEdit(Step $step) {
        return $this->context->isGranted('ROLE_ADMIN');
    }
    
    public function getRepository() {
        return $this->em->getRepository('TangaraCoreBundle:Step');
    }
    
}
