<?php

namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Step
 * @ORM\Entity(repositoryClass="Tangara\CoreBundle\Entity\StepRepository")
 * @ORM\Table(name="steps")
 */
class Step {
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255 ,nullable=false)
     */
    private $name;
    
    
    /**
     * @var string
     *
     * @ORM\Column(type="string",nullable=false)
     */
    private $title;
    
    
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    
    /**
     * @ORM\OneToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;
    
    /**
     * @ORM\OneToMany(targetEntity="CourseStep", mappedBy="step")
     **/
    private $courses;
    
    public function __construct() {
        $this->paths = new ArrayCollection();
    }    
    

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
     * Set project
     *
     * @param \Tangara\CoreBundle\Entity\Project $project
     *
     * @return Step
     */
    public function setProject(\Tangara\CoreBundle\Entity\Project $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Tangara\CoreBundle\Entity\Project
     */
    public function getProject()
    {
        return $this->project;
    }


    

    /**
     * Add course
     *
     * @param \Tangara\CoreBundle\Entity\CourseStep $course
     *
     * @return Step
     */
    public function addCourse(\Tangara\CoreBundle\Entity\CourseStep $course)
    {
        $this->courses[] = $course;

        return $this;
    }

    /**
     * Remove course
     *
     * @param \Tangara\CoreBundle\Entity\CourseStep $course
     */
    public function removeCourse(\Tangara\CoreBundle\Entity\CourseStep $course)
    {
        $this->courses->removeElement($course);
    }

    /**
     * Get courses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Step
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Step
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Step
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
