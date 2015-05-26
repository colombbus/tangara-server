<?php
namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;


/**
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="Tangara\CoreBundle\Entity\UserRepository")
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="Project")
     */
    private $home;
    
    
    /**
     * @var datetime
     *
     * @ORM\Column(name="polling", type="datetime", nullable=true)
     *
     */
    protected $polling;

    /**
     * Set home
     *
     * @param \Tangara\CoreBundle\Entity\Project $home
     * @return User
     */
    public function setHome(\Tangara\CoreBundle\Entity\Project $home = null)
    {
        $this->home = $home;

        return $this;
    }

    /**
     * Get home
     *
     * @return \Tangara\CoreBundle\Entity\Project 
     */
    public function getHome()
    {
        return $this->home;
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
     * Set polling
     *
     * @param \DateTime $polling
     * @return User
     */
    public function setPolling($polling)
    {
        $this->polling = $polling;

        return $this;
    }

    /**
     * Get polling
     *
     * @return \DateTime 
     */
    public function getPolling()
    {
        return $this->polling;
    }
}
