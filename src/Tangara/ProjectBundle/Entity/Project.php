<?php

namespace Tangara\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tangara\ProjectBundle\Entity\ProjectRepository")
 */
class Project
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
     * @ORM\Column(name="Logo", type="string", length=255)
     */
    private $logo;
    
     /**
     * @var string
     *
     * @ORM\Column(name="Name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ProjectManager", type="string", length=255)
     */
    private $projectManager;

    /**
     * @var string
     *
     * @ORM\Column(name="ProjectOwnerGroup", type="string", length=255)
     */
    private $projectOwnerGroup;

    /**
     * @var array
     *
     * @ORM\Column(name="Contributors", type="array")
     */
    private $contributors;

    /**
     * @var array
     *
     * @ORM\Column(name="FilesRights", type="array")
     */
    private $filesRights;

    /**
     * @var array
     *
     * @ORM\Column(name="Files", type="array")
     */
    private $files;

    /**
     * @var integer
     *
     * @ORM\Column(name="ReferenceWidth", type="integer")
     */
    private $referenceWidth;

    /**
     * @var integer
     *
     * @ORM\Column(name="ReferenceHeight", type="integer")
     */
    private $referenceHeight;

    /**
     * @var string
     *
     * @ORM\Column(name="ReferenceFont", type="string", length=255)
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
        $this->date = new \DateTime();
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
     * Set projectManager
     *
     * @param string $projectManager
     * @return Project
     */
    public function setProjectManager($projectManager)
    {
        $this->projectManager = $projectManager;

        return $this;
    }

    /**
     * Get projectManager
     *
     * @return string 
     */
    public function getProjectManager()
    {
        return $this->projectManager;
    }

    /**
     * Set projectOwnerGroup
     *
     * @param string $projectOwnerGroup
     * @return Project
     */
    public function setProjectOwnerGroup($projectOwnerGroup)
    {
        $this->projectOwnerGroup = $projectOwnerGroup;

        return $this;
    }

    /**
     * Get projectOwnerGroup
     *
     * @return string 
     */
    public function getProjectOwnerGroup()
    {
        return $this->projectOwnerGroup;
    }

    /**
     * Set contributors
     *
     * @param array $contributors
     * @return Project
     */
    public function setContributors($contributors)
    {
        $this->contributors = $contributors;

        return $this;
    }

    /**
     * Get contributors
     *
     * @return array 
     */
    public function getContributors()
    {
        return $this->contributors;
    }

    /**
     * Set filesRights
     *
     * @param array $filesRights
     * @return Project
     */
    public function setFilesRights($filesRights)
    {
        $this->filesRights = $filesRights;

        return $this;
    }

    /**
     * Get filesRights
     *
     * @return array 
     */
    public function getFilesRights()
    {
        return $this->filesRights;
    }

    /**
     * Set files
     *
     * @param array $files
     * @return Project
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * Get files
     *
     * @return array 
     */
    public function getFiles()
    {
        return $this->files;
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
     * Set logo
     *
     * @param string $logo
     * @return Project
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
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
}
