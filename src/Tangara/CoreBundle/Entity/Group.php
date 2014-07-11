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
     * Constructor
     */
    public function __construct()
    {
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
    
    //return la liste des groupes dont l'user n'est pas membre
    public function allNoGroup($allgroups, $user_groups) {

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

}
