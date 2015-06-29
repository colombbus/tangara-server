<?php

namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * CourseStep
 * @ORM\Entity(repositoryClass="Tangara\CoreBundle\Entity\CourseStepRepository")
 * @ORM\Table(name="course_steps")
 */
class CourseStep {
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=false)
     */
    private $position;
    
    /**
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="steps")
     **/
    private $course;

    /**
     * @ORM\ManyToOne(targetEntity="Step", inversedBy="courses")
     **/
    private $step;
    
    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set course
     *
     * @param \Tangara\CoreBundle\Entity\Course $course
     *
     * @return CourseStep
     */
    public function setCourse(\Tangara\CoreBundle\Entity\Course $course = null)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return \Tangara\CoreBundle\Entity\Course
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set step
     *
     * @param \Tangara\CoreBundle\Entity\Step $step
     *
     * @return CourseStep
     */
    public function setStep(\Tangara\CoreBundle\Entity\Step $step = null)
    {
        $this->step = $step;

        return $this;
    }

    /**
     * Get step
     *
     * @return \Tangara\CoreBundle\Entity\Step
     */
    public function getStep()
    {
        return $this->step;
    }


    /**
     * Set position
     *
     * @param integer $position
     *
     * @return CourseStep
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }
}
