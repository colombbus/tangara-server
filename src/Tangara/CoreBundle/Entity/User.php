<?php
namespace Tangara\CoreBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Tangara\CoreBundle\Entity\Group;


/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Tangara\CoreBundle\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
     /**
     * @var string
     *
     * @ORM\Column(name="Country", type="string", length=255, nullable=true)
     */
    private $country;
    
     /**
     * @var string
     *
     * @ORM\Column(name="College", type="string", length=255, nullable=true)
     */
    private $college;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="DateCreation", type="datetime")
     */
    private $dateCreation;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Tangara\CoreBundle\Entity\Project", mappedBy="user")
     */
    private $projects;


    /**
     *
     * @ORM\OneToMany(targetEntity="Tangara\CoreBundle\Entity\Group", mappedBy="groupsLeader")
     */
    private $groupLeader;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        //$this->dateCreation = new \DateTime('now');
        $this->projects = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set country
     *
     * @param string $country
     * @return User
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Set college
     *
     * @param string $college
     * @return User
     */
    public function setCollege($college)
    {
        $this->college = $college;

        return $this;
    }

    /**
     * Get college
     *
     * @return string 
     */
    public function getCollege()
    {
        return $this->college;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return User
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
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }
    
    /**
     * Add project
     *
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @return User
     */
    public function addProjects(\Tangara\CoreBundle\Entity\Project $project)
    {
        $this->projects[] = $project;

        return $this;
    }

    /**
     * Remove project
     *
     * @param \Tangara\CoreBundle\Entity\Project $project
     */
    public function removeProjects(\Tangara\CoreBundle\Entity\Project $project)
    {
        $this->projects->removeElement($project);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
    }

    /**
     * Add projects
     *
     * @param \Tangara\CoreBundle\Entity\Project $projects
     * @return User
     */
    public function addProject(\Tangara\CoreBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \Tangara\CoreBundle\Entity\Project $projects
     */
    public function removeProject(\Tangara\CoreBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Add groups
     *
     * @param \Tangara\CoreBundle\Entity\Group $groups
     * @return User
     */
    public function addGroups(\Tangara\CoreBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Tangara\CoreBundle\Entity\Group $groups
     */
    public function removeGroups(\Tangara\CoreBundle\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Add groupLeader
     *
     * @param \Tangara\CoreBundle\Entity\Group $groupLeader
     * @return User
     */
    public function addGroupLeader(\Tangara\CoreBundle\Entity\Group $groupLeader)
    {
        $this->groupLeader[] = $groupLeader;

        return $this;
    }

    /**
     * Remove groupLeader
     *
     * @param \Tangara\CoreBundle\Entity\Group $groupLeader
     */
    public function removeGroupLeader(\Tangara\CoreBundle\Entity\Group $groupLeader)
    {
        $this->groupLeader->removeElement($groupLeader);
    }

    /**
     * Get groupLeader
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupLeader()
    {
        return $this->groupLeader;
    }
    //----------------------------Other Methods--------------------------------
    
    // Checks if group is ever exits
    public function isGroups() {

        foreach ($this->groups as $key) {
            if($key->getId()){
                return true; //un group
            }
        }
        
        return false; //pas de group
    }
    
}
