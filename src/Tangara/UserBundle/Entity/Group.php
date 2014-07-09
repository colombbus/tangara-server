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
     * @ORM\ManyToMany(targetEntity="Tangara\TangaraBundle\Entity\Project")
     * @ORM\JoinTable(name="projectsInGroup",
     *      joinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="project_id", referencedColumnName="id")}
     * )
     */
    protected $projectsInGroup;
    
    /**
     *
     * @ORM\OneToMany(targetEntity="Tangara\TangaraBundle\Entity\Project", mappedBy="group")
     */
    private $project;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->project = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add project
     *
     * @param \Tangara\TangaraBundle\Entity\Project $project
     * @return Group
     */
    public function addProject(\Tangara\TangaraBundle\Entity\Project $project)
    {
        $this->project[] = $project;

        return $this;
    }

    /**
     * Remove project
     *
     * @param \Tangara\TangaraBundle\Entity\Project $project
     */
    public function removeProject(\Tangara\TangaraBundle\Entity\Project $project)
    {
        $this->project->removeElement($project);
    }

    /**
     * Get project
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProject()
    {
        return $this->project;
    }
    

}
