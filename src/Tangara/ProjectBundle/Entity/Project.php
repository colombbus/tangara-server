<?php

namespace Tangara\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tangara\UserBundle\Entity\User;
use Tangara\ProjectBundle\Entity\Project;

/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tangara\ProjectBundle\Entity\ProjectRepository")
 */
class Project extends \Doctrine\ORM\EntityRepository
{
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
     * @ORM\Column(name="Name", type="string", length=255, nullable=true)
     */
    private $name;
    
    /**
     * @ORM\ManyToOne(targetEntity="Tangara\UserBundle\Entity\User")
     * @ORM\JoinTable(name="ProjectManager",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $projectManager;
    
    /**
     * @ORM\ManyToOne(targetEntity="Tangara\ProjectBundle\Entity\Project")
     * @ORM\JoinTable(name="project_files",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="file_id", referencedColumnName="id")}
     * )
     */
    protected $relativeProject;

    
    /**
     * @var boolean
     *
     * @ORM\Column(name="UserProject", type="boolean", nullable=true)
     */
    private $userProject;  

    /**
     * @ORM\ManyToOne(targetEntity="Tangara\UserBundle\Entity\Group")
     * @ORM\JoinTable(name="OwnerGroup",
     *      joinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    private $projectOwnerGroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="ReferenceWidth", type="integer", nullable=true)
     */
    private $referenceWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="ReferenceHeight", type="integer", nullable=true)
     */
    private $referenceHeight;

    /**
     * @var string
     *
     * @ORM\Column(name="ReferenceFont", type="string", length=255, nullable=true)
     */
    private $referenceFont;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateCreation", type="datetime")
     */
    private $dateCreation;

    /**
     * @var array
     *
     * @ORM\Column(name="Designers", type="array")
     */
    private $designers;

    /**
     * @var array
     *
     * @ORM\Column(name="DesignersButton", type="array")
     */
    private $designersButton;

    /**
     * @var array
     *
     * @ORM\Column(name="DesignersLogo", type="array")
     */
    private $designersLogo;

    /**
     * @var array
     *
     * @ORM\Column(name="DesignersCredits", type="array")
     */
    private $designersCredits;

    /**
     * @var array
     *
     * @ORM\Column(name="DesignersCharacters", type="array")
     */
    private $designersCharacters;

    /**
     * @var array
     *
     * @ORM\Column(name="DesignersBadGuys", type="array")
     */
    private $designersBadGuys;

    /**
     * @var array
     *
     * @ORM\Column(name="DesignersGraphicalElements", type="array")
     */
    private $designersGraphicalElements;

    /**
     * @var array
     *
     * @ORM\Column(name="DesignersDecors", type="array")
     */
    private $designersDecor;

    /**
     * @var array
     *
     * @ORM\Column(name="SoundDesigners", type="array")
     */
    private $soundDesigners;

    /**
     * @var array
     *
     * @ORM\Column(name="SoundRecorders", type="array")
     */
    private $soundRecorders;

    /**
     * @var array
     *
     * @ORM\Column(name="ScriptWriters", type="array")
     */
    private $scenario;
    
    public function __construct() {
        $this->dateCreation = new \DateTime('NOW');
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
     * Set userProject
     *
     * @param boolean $userProject
     * @return Project
     */
    public function setUserProject($userProject)
    {
        $this->userProject = $userProject;

        return $this;
    }

    /**
     * Get userProject
     *
     * @return boolean 
     */
    public function getUserProject()
    {
        return $this->userProject;
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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Project
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set designers
     *
     * @param array $designers
     * @return Project
     */
    public function setDesigners($designers)
    {
        $this->designers = $designers;

        return $this;
    }

    /**
     * Get designers
     *
     * @return array 
     */
    public function getDesigners()
    {
        return $this->designers;
    }

    /**
     * Set designersButton
     *
     * @param array $designersButton
     * @return Project
     */
    public function setDesignersButton($designersButton)
    {
        $this->designersButton = $designersButton;

        return $this;
    }

    /**
     * Get designersButton
     *
     * @return array 
     */
    public function getDesignersButton()
    {
        return $this->designersButton;
    }

    /**
     * Set designersLogo
     *
     * @param array $designersLogo
     * @return Project
     */
    public function setDesignersLogo($designersLogo)
    {
        $this->designersLogo = $designersLogo;

        return $this;
    }

    /**
     * Get designersLogo
     *
     * @return array 
     */
    public function getDesignersLogo()
    {
        return $this->designersLogo;
    }

    /**
     * Set designersCredits
     *
     * @param array $designersCredits
     * @return Project
     */
    public function setDesignersCredits($designersCredits)
    {
        $this->designersCredits = $designersCredits;

        return $this;
    }

    /**
     * Get designersCredits
     *
     * @return array 
     */
    public function getDesignersCredits()
    {
        return $this->designersCredits;
    }

    /**
     * Set designersCharacters
     *
     * @param array $designersCharacters
     * @return Project
     */
    public function setDesignersCharacters($designersCharacters)
    {
        $this->designersCharacters = $designersCharacters;

        return $this;
    }

    /**
     * Get designersCharacters
     *
     * @return array 
     */
    public function getDesignersCharacters()
    {
        return $this->designersCharacters;
    }

    /**
     * Set designersBadGuys
     *
     * @param array $designersBadGuys
     * @return Project
     */
    public function setDesignersBadGuys($designersBadGuys)
    {
        $this->designersBadGuys = $designersBadGuys;

        return $this;
    }

    /**
     * Get designersBadGuys
     *
     * @return array 
     */
    public function getDesignersBadGuys()
    {
        return $this->designersBadGuys;
    }

    /**
     * Set designersGraphicalElements
     *
     * @param array $designersGraphicalElements
     * @return Project
     */
    public function setDesignersGraphicalElements($designersGraphicalElements)
    {
        $this->designersGraphicalElements = $designersGraphicalElements;

        return $this;
    }

    /**
     * Get designersGraphicalElements
     *
     * @return array 
     */
    public function getDesignersGraphicalElements()
    {
        return $this->designersGraphicalElements;
    }

    /**
     * Set designersDecor
     *
     * @param array $designersDecor
     * @return Project
     */
    public function setDesignersDecor($designersDecor)
    {
        $this->designersDecor = $designersDecor;

        return $this;
    }

    /**
     * Get designersDecor
     *
     * @return array 
     */
    public function getDesignersDecor()
    {
        return $this->designersDecor;
    }

    /**
     * Set soundDesigners
     *
     * @param array $soundDesigners
     * @return Project
     */
    public function setSoundDesigners($soundDesigners)
    {
        $this->soundDesigners = $soundDesigners;

        return $this;
    }

    /**
     * Get soundDesigners
     *
     * @return array 
     */
    public function getSoundDesigners()
    {
        return $this->soundDesigners;
    }

    /**
     * Set soundRecorders
     *
     * @param array $soundRecorders
     * @return Project
     */
    public function setSoundRecorders($soundRecorders)
    {
        $this->soundRecorders = $soundRecorders;

        return $this;
    }

    /**
     * Get soundRecorders
     *
     * @return array 
     */
    public function getSoundRecorders()
    {
        return $this->soundRecorders;
    }

    /**
     * Set scenario
     *
     * @param array $scenario
     * @return Project
     */
    public function setScenario($scenario)
    {
        $this->scenario = $scenario;

        return $this;
    }

    /**
     * Get scenario
     *
     * @return array 
     */
    public function getScenario()
    {
        return $this->scenario;
    }

    /**
     * Set projectManager
     *
     * @param \Tangara\UserBundle\Entity\User $projectManager
     * @return Project
     */
    public function setProjectManager(\Tangara\UserBundle\Entity\User $projectManager = null)
    {
        $this->projectManager = $projectManager;

        return $this;
    }

    /**
     * Get projectManager
     *
     * @return \Tangara\UserBundle\Entity\User 
     */
    public function getProjectManager()
    {
        return $this->projectManager;
    }

    /**
     * Set relativeProject
     *
     * @param \Tangara\ProjectBundle\Entity\Project $relativeProject
     * @return Project
     */
    public function setRelativeProject(\Tangara\ProjectBundle\Entity\Project $relativeProject = null)
    {
        $this->relativeProject = $relativeProject;

        return $this;
    }

    /**
     * Get relativeProject
     *
     * @return \Tangara\ProjectBundle\Entity\Project 
     */
    public function getRelativeProject()
    {
        return $this->relativeProject;
    }

    /**
     * Set projectOwnerGroup
     *
     * @param \Tangara\UserBundle\Entity\Group $projectOwnerGroup
     * @return Project
     */
    public function setProjectOwnerGroup(\Tangara\UserBundle\Entity\Group $projectOwnerGroup = null)
    {
        $this->projectOwnerGroup = $projectOwnerGroup;

        return $this;
    }

    /**
     * Get projectOwnerGroup
     *
     * @return \Tangara\UserBundle\Entity\Group 
     */
    public function getProjectOwnerGroup()
    {
        return $this->projectOwnerGroup;
    }
}
