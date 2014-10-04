<?php

namespace Tangara\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Log
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tangara\CoreBundle\Entity\LogRepository")
 */
class Log
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
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable = false)
     */
    private $project;


    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable = true)
     */
    private $user;

    
    /**
     * @var datetime $timestamp
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $timestamp;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $operation;
    
    /**
     * @var string
     * 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $data;
    
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
     * Get timestamp
     *
     * @return \DateTime 
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Set project
     *
     * @param \Tangara\CoreBundle\Entity\Project $project
     * @return Log
     */
    public function setProject(\Tangara\CoreBundle\Entity\Project $project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Tangara\CoreBundle\Entity\Project 
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set timestamp
     *
     * @param \DateTime $timestamp
     * @return Log
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * Set operation
     *
     * @param string $operation
     * @return Log
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return string 
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set data
     *
     * @param string $data
     * @return Log
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set user
     *
     * @param \Tangara\CoreBundle\Entity\User $user
     * @return Log
     */
    public function setUser(\Tangara\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Tangara\CoreBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
