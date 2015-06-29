<?php

namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Course
 * @ORM\Entity(repositoryClass="Tangara\CoreBundle\Entity\CourseRepository")
 * @ORM\Table(name="courses")
 */
class Course
{
    /**
     * @var integer
     * 
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
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
     * @ORM\OneToMany(targetEntity="CourseStep", mappedBy="course")
     **/
    private $steps;
    
    public function __construct() {
        $this->steps = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return Path
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

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Course
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
     * @return Course
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
     * Add step
     *
     * @param \Tangara\CoreBundle\Entity\CourseStep $step
     *
     * @return Course
     */
    public function addStep(\Tangara\CoreBundle\Entity\CourseStep $step)
    {
        $this->steps[] = $step;

        return $this;
    }

    /**
     * Remove step
     *
     * @param \Tangara\CoreBundle\Entity\CourseStep $step
     */
    public function removeStep(\Tangara\CoreBundle\Entity\CourseStep $step)
    {
        $this->steps->removeElement($step);
    }

    /**
     * Get steps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSteps()
    {
        return $this->steps;
    }
}
