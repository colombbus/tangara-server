<?php

namespace Tangara\AdministrationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Essai
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tangara\AdministrationBundle\Entity\EssaiRepository")
 */
class Essai
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
     * @var array
     *
     * @ORM\Column(name="liste_fichiers", type="array")
     */
    private $listeFichiers;

    /**
     * @var array
     *
     * @ORM\Column(name="liste_simple", type="simple_array")
     */
    private $listeSimple;

    /**
     * @var array
     *
     * @ORM\Column(name="liste_json", type="json_array")
     */
    private $listeJson;

    /**
     * @var boolean
     *
     * @ORM\Column(name="connected", type="boolean")
     */
    private $connected;

    /**
     * @var integer
     *
     * @ORM\Column(name="nb_member", type="integer")
     */
    private $nbMember;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_connexion", type="datetime")
     */
    private $dateConnexion;

    /**
     * @var string
     *
     * @ORM\Column(name="file", type="blob")
     */
    private $file;


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
     * Set listeFichiers
     *
     * @param array $listeFichiers
     * @return Essai
     */
    public function setListeFichiers($listeFichiers)
    {
        $this->listeFichiers = $listeFichiers;

        return $this;
    }

    /**
     * Get listeFichiers
     *
     * @return array 
     */
    public function getListeFichiers()
    {
        return $this->listeFichiers;
    }

    /**
     * Set listeSimple
     *
     * @param array $listeSimple
     * @return Essai
     */
    public function setListeSimple($listeSimple)
    {
        $this->listeSimple = $listeSimple;

        return $this;
    }

    /**
     * Get listeSimple
     *
     * @return array 
     */
    public function getListeSimple()
    {
        return $this->listeSimple;
    }

    /**
     * Set listeJson
     *
     * @param array $listeJson
     * @return Essai
     */
    public function setListeJson($listeJson)
    {
        $this->listeJson = $listeJson;

        return $this;
    }

    /**
     * Get listeJson
     *
     * @return array 
     */
    public function getListeJson()
    {
        return $this->listeJson;
    }

    /**
     * Set connected
     *
     * @param boolean $connected
     * @return Essai
     */
    public function setConnected($connected)
    {
        $this->connected = $connected;

        return $this;
    }

    /**
     * Get connected
     *
     * @return boolean 
     */
    public function getConnected()
    {
        return $this->connected;
    }

    /**
     * Set nbMember
     *
     * @param integer $nbMember
     * @return Essai
     */
    public function setNbMember($nbMember)
    {
        $this->nbMember = $nbMember;

        return $this;
    }

    /**
     * Get nbMember
     *
     * @return integer 
     */
    public function getNbMember()
    {
        return $this->nbMember;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Essai
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Essai
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateConnexion
     *
     * @param \DateTime $dateConnexion
     * @return Essai
     */
    public function setDateConnexion($dateConnexion)
    {
        $this->dateConnexion = $dateConnexion;

        return $this;
    }

    /**
     * Get dateConnexion
     *
     * @return \DateTime 
     */
    public function getDateConnexion()
    {
        return $this->dateConnexion;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return Essai
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string 
     */
    public function getFile()
    {
        return $this->file;
    }
}
