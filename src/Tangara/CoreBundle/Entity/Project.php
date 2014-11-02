<?php

namespace Tangara\CoreBundle\Entity;

use Tangara\CoreBundle\Entity\File;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use \DateTime;
/**
 * Project
 * @ORM\Entity(repositoryClass="Tangara\CoreBundle\Entity\ProjectRepository")
 * @ORM\Table(name="projects")
 */
class Project {

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\OneToOne(targetEntity="User")
     */
    private $owner;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $width;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $font;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created;
    
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $instructions;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $published;
    
    /**
     * @var File
     * 
     * @ORM\OneToOne(targetEntity="File")
     */
    private $launcher;
    
    
    /**
     * @ORM\OneToMany(targetEntity="File", mappedBy="project")
     */
    private $files;

    
    public function __construct() {
        $this->created = new DateTime('NOW');
        $this->referenceHeight = 768;
        $this->referenceWidth = 1024;
        $this->referenceFont = "Arial";
        $this->published = false;
        $this->files = new ArrayCollection();
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
     * @return Project
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
     * Set referenceWidth
     *
     * @param integer $referenceWidth
     * @return Project
     */
    public function setReferenceWidth($referenceWidth)
    {
        $this->referenceWidth = $referenceWidth;

        return $this;
    }

    /**
     * Get referenceWidth
     *
     * @return integer 
     */
    public function getReferenceWidth()
    {
        return $this->referenceWidth;
    }

    /**
     * Set referenceHeight
     *
     * @param integer $referenceHeight
     * @return Project
     */
    public function setReferenceHeight($referenceHeight)
    {
        $this->referenceHeight = $referenceHeight;

        return $this;
    }

    /**
     * Get referenceHeight
     *
     * @return integer 
     */
    public function getReferenceHeight()
    {
        return $this->referenceHeight;
    }

    /**
     * Set referenceFont
     *
     * @param string $referenceFont
     * @return Project
     */
    public function setReferenceFont($referenceFont)
    {
        $this->referenceFont = $referenceFont;

        return $this;
    }

    /**
     * Get referenceFont
     *
     * @return string 
     */
    public function getReferenceFont()
    {
        return $this->referenceFont;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Project
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set owner
     *
     * @param \Tangara\CoreBundle\Entity\User $owner
     * @return Project
     */
    public function setOwner(\Tangara\CoreBundle\Entity\User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return \Tangara\CoreBundle\Entity\User 
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set width
     *
     * @param integer $width
     * @return Project
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param integer $height
     * @return Project
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return integer 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set font
     *
     * @param string $font
     * @return Project
     */
    public function setFont($font)
    {
        $this->font = $font;

        return $this;
    }

    /**
     * Get font
     *
     * @return string 
     */
    public function getFont()
    {
        return $this->font;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Project
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
     * Set instructions
     *
     * @param string $instructions
     * @return Project
     */
    public function setInstructions($instructions)
    {
        $this->instructions = $instructions;

        return $this;
    }

    /**
     * Get instructions
     *
     * @return string 
     */
    public function getInstructions()
    {
        return $this->instructions;
    }

    /**
     * Set published
     *
     * @param boolean $published
     * @return Project
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean 
     */
    public function getPublished()
    {
        return $this->published;
    }

    /**
     * Set launcher
     *
     * @param \Tangara\CoreBundle\Entity\File $launcher
     * @return Project
     */
    public function setLauncher(\Tangara\CoreBundle\Entity\File $launcher = null)
    {
        $this->launcher = $launcher;

        return $this;
    }

    /**
     * Get launcher
     *
     * @return \Tangara\CoreBundle\Entity\File 
     */
    public function getLauncher()
    {
        return $this->launcher;
    }

    /**
     * Add files
     *
     * @param \Tangara\CoreBundle\Entity\File $files
     * @return Project
     */
    public function addFile(\Tangara\CoreBundle\Entity\File $files)
    {
        $this->files[] = $files;

        return $this;
    }

    /**
     * Remove files
     *
     * @param \Tangara\CoreBundle\Entity\File $files
     */
    public function removeFile(\Tangara\CoreBundle\Entity\File $files)
    {
        $this->files->removeElement($files);
    }

    /**
     * Get files
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFiles()
    {
        return $this->files;
    }
}
