<?php

namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="files")
 * @ORM\Entity(repositoryClass="Tangara\CoreBundle\Entity\FileRepository")
 */
class File {

    /**
     * @var integer
     * 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $path;

    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    
    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable = false)
     */
    private $project;

    /**
     * @var boolean
     *
     * @ORM\Column(name="program", type="boolean", nullable=false)
     */
    private $program;
            

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
     * Set path
     *
     * @param string $path
     * @return File
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set program
     *
     * @param boolean $program
     * @return File
     */
    public function setProgram($program)
    {
        $this->program = $program;

        return $this;
    }

    /**
     * Get program
     *
     * @return boolean 
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * Set project
     *
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @return File
     */
    public function setProject(\Tangara\CoreBundle\Entity\Project $project)
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
     * Set type
     *
     * @param string $type
     * @return File
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}
