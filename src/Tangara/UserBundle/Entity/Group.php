<?php

namespace Tangara\UserBundle\Entity;

use FOS\UserBundle\Model\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_group")
 */
class Group extends BaseGroup {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="privateGroup", type="boolean", nullable=true)
     */
    private $privateGroup;
    
    /**
     * @ORM\ManyToMany(targetEntity="Tangara\ProjectBundle\Entity\Project")
     * @ORM\JoinTable(name="projectsInGroup",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")}
     * )
     */
    protected $projectsInGroup;
    
    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set privateGroup
     *
     * @param boolean $privateGroup
     * @return Group
     */
    public function setPrivateGroup($privateGroup)
    {
        $this->privateGroup = $privateGroup;

        return $this;
    }

    /**
     * Get privateGroup
     *
     * @return boolean 
     */
    public function getPrivateGroup()
    {
        return $this->privateGroup;
    }

    /**
     * Add projectsInGroup
     *
     * @param \Tangara\ProjectBundle\Entity\Project $projectsInGroup
     * @return Group
     */
    public function addProjectsInGroup(\Tangara\ProjectBundle\Entity\Project $projectsInGroup)
    {
        $this->projectsInGroup[] = $projectsInGroup;

        return $this;
    }

    /**
     * Remove projectsInGroup
     *
     * @param \Tangara\ProjectBundle\Entity\Project $projectsInGroup
     */
    public function removeProjectsInGroup(\Tangara\ProjectBundle\Entity\Project $projectsInGroup)
    {
        $this->projectsInGroup->removeElement($projectsInGroup);
    }

    /**
     * Get projectsInGroup
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjectsInGroup()
    {
        return $this->projectsInGroup;
    }
}
