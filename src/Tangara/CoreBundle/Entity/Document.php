<?php

namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tangara\CoreBundle\Entity\User;
use Tangara\CoreBundle\Entity\Project;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Tangara\CoreBundle\Entity\DocumentRepository")
 */
class Document {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $path;
    
    private $uploadDir;

    protected function getUploadDir() {
        return $this->uploadDir;
    }
     /**
     * Set Upload Directory
     *
     * @param string $directory
     * @return Document
     */
    public function setUploadDir($directory) {
        $this->uploadDir = $directory;
        return $this;
    }

    /**
     *
     * @ORM\ManyToOne(targetEntity="Tangara\CoreBundle\Entity\Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable = false)
     */
    private $ownerProject;

    /**
     * @Assert\File(maxSize="6000000")
     * 
     */
    private $file;

    /**
     * @var boolean
     *
     * @ORM\Column(name="program", type="boolean", nullable=false)
     */
    private $program;
            
    public function upload() {
        if (null === $this->file) {
            return;
        }
        //TODO: Clean filename for security
        // « move » method has 2 arguments: target directory 
        // and target filename in new directory
        $this->file->move($this->getUploadDir(), $this->file->getClientOriginalName());

        // path: filename in directory
        $this->path = $this->file->getClientOriginalName();
        $this->file = null;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->setUploadDir('/home/tangara');
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
     * Set path
     *
     * @param string $path
     * @return Document
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
     * Set ownerProject
     *
     * @param \Tangara\CoreBundle\Entity\Project $ownerProject
     * @return Document
     */
    public function setOwnerProject(\Tangara\CoreBundle\Entity\Project $ownerProject)
    {
        $this->ownerProject = $ownerProject;

        return $this;
    }

    /**
     * Get ownerProject
     *
     * @return \Tangara\CoreBundle\Entity\Project 
     */
    public function getOwnerProject()
    {
        return $this->ownerProject;
    }

    /**
     * Set file
     * 
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @return Document
     */
    public function setFile(\Symfony\Component\HttpFoundation\File\UploadedFile $file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set program
     *
     * @param boolean $program
     * @return Document
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
}
