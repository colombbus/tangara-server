<?php
namespace Tangara\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Tangara\UserBundle\Entity\Group;
use Tangara\ProjectBundle\Entity\Project;

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
     * @ORM\ManyToMany(targetEntity="Tangara\UserBundle\Entity\Group")
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
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->dateCreation = new \DateTime('now');
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

}
