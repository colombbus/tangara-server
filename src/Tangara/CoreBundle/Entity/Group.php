<?php

namespace Tangara\CoreBundle\Entity;

use FOS\UserBundle\Entity\Group as BaseGroup;
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
     * @ORM\ManyToMany(targetEntity="Tangara\CoreBundle\Entity\Project")
     * @ORM\JoinTable(name="projectsInGroup",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")}
     * )
     */
    protected $projectsInGroup;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Tangara\CoreBundle\Entity\Project", mappedBy="group")
     */
    private $projects;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="Tangara\CoreBundle\Entity\User", mappedBy="groups")
     */
    private $users;
    
    /**
     *
     * @ORM\ManytoOne(targetEntity="Tangara\CoreBundle\Entity\User", inversedBy="groupLeader")
     */
    private $groupsLeader;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct($name = null, $roles = array());
        $this->projects = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add projects
     *
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @return Group
     */
    public function addProjects(\Tangara\CoreBundle\Entity\Project $project)
    {
        $this->projects[] = $project;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \Tangara\CoreBundle\Entity\Project $project
     */
    public function removeProjects(\Tangara\CoreBundle\Entity\Project $project) {
        $this->projects->removeElement($project);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects() {
        return $this->projects;
    }

    
    /**
     * Add users
     *
     * @param Tangara\CoreBundle\Entity\User $users
     */
    public function addUsers(\Tangara\CoreBundle\Entity\User $users) {
        $this->users[] = $users;
    }

    /**
     * Remove users
     *
     * @param Tangara\CoreBundle\Entity\User $users
     */
    public function removeUsers(\Tangara\CoreBundle\Entity\User $users) { 
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getUsers() { 
        return $this->users;
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
     * @param \Tangara\CoreBundle\Entity\Project $projectsInGroup
     * @return Group
     */
    public function addProjectsInGroup(\Tangara\CoreBundle\Entity\Project $projectsInGroup)
    {
        $this->projectsInGroup[] = $projectsInGroup;

        return $this;
    }

    /**
     * Remove projectsInGroup
     *
     * @param \Tangara\CoreBundle\Entity\Project $projectsInGroup
     */
    public function removeProjectsInGroup(\Tangara\CoreBundle\Entity\Project $projectsInGroup)
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

    /**
     * Add projects
     *
     * @param \Tangara\CoreBundle\Entity\Project $projects
     * @return Group
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
     * Add users
     *
     * @param \Tangara\CoreBundle\Entity\User $users
     * @return Group
     */
    public function addUser(\Tangara\CoreBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Tangara\CoreBundle\Entity\User $users
     */
    public function removeUser(\Tangara\CoreBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Set groupsLeader
     *
     * @param \Tangara\CoreBundle\Entity\User $groupsLeader
     * @return Group
     */
    public function setGroupsLeader(\Tangara\CoreBundle\Entity\User $groupsLeader = null)
    {
        $this->groupsLeader = $groupsLeader;

        return $this;
    }

    /**
     * Get groupsLeader
     *
     * @return \Tangara\CoreBundle\Entity\User 
     */
    public function getGroupsLeader()
    {
        return $this->groupsLeader;
    }
    
    
    
    //----------------------------Other Methods--------------------------------

    
    //return la liste des groupes dont l'user n'est pas membre
    public function groupsWithoutMe($allgroups, $user_groups) {
        
        $tmp = null;
        
        foreach ($allgroups as $key) {
            $dif = true;
            foreach ($user_groups as $key2) {
                if ($key->getName() == $key2->getName()) {
                    $dif = false;
                    break;
                }
            }
            if ($dif == true) {
                $tmp[] = $key;
            }
        }
        
        return $tmp;
    }
    
    //verifie si il y a des projets en en cours dans le group
    public function isProjects() {

        foreach ($this->projects as $key) {
            if($key->getId()){
                return true; //un projet
            }
        }
        
        return false; //pas de projet
    }
    
    
    
}
