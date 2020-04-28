<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FicheRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Fiche
{
    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var  bool Défini si la fiche est professionel
     * @ORM\Column(type="boolean")
     */
    private $Professionel;

    /**
     * @var string Défini le Nom professionnel | null dans les autres cas
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $libelle;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotNull(message="Veuillez renseigner l'Adresse.")
     */
    private $adresse;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $tels;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $emails;

    /**
     * @var datetime
     * @ORM\Column(type="datetime")
     */
    private $creerle;

    /**
     * @var datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $modifierle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="fiches")
     */
    private $user;





/* --- DELCARATION DES SETTERS ET GETTES DES PROPRIETES --- */



    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return  bool Défini si la fiche est professionel
     */
    public function isProfessionel()
    {
        return $this->Professionel;
    }

    /**
     * @param  bool Défini si la fiche est professionel $Professionel
     *
     * @return self
     */
    public function setProfessionel($Professionel)
    {
        $this->Professionel = $Professionel;

        return $this;
    }

    /**
     * @return string Défini le Nom professionnel | null dans les autres cas
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param string Défini le Nom professionnel | null dans les autres cas $libelle
     *
     * @return self
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     *
     * @return self
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     *
     * @return self
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param string $adresse
     *
     * @return self
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * @return string
     */
    public function getTels()
    {
        return $this->tels;
    }

    /**
     * @param string $tels
     *
     * @return self
     */
    public function setTels($tels)
    {
        $this->tels = $tels;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @param string $emails
     *
     * @return self
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * @return datetime
     */
    public function getCreerle()
    {
        return $this->creerle;
    }

    /**
     * @param datetime $creerle
     *
     * @return self
     */
    public function setCreerle(datetime $creerle)
    {
        $this->creerle = $creerle;

        return $this;
    }

    /**
     * @return datetime
     */
    public function getModifierle()
    {
        return $this->modifierle;
    }

    /**
     * @param datetime $modifierle
     *
     * @return self
     */
    public function setModifierle(datetime $modifierle)
    {
        $this->modifierle = $modifierle;

        return $this;
    }  






/* --- DELCARATION DES FONCTIONS --- */

    
    public function __construct()
    {
        $this->creerle = new \DateTime();
    }

    public function getNomComplet(): string
    {
        return $this->Nom . ' ' . $this->Prenom ;
    }

    /**
     * Défini la Date à chaque modification de la fiche.
     * 
     * @ORM\PreUpdate
     */
    public function UpdateModifierle()
    {
        $this->modifierle = new \DateTime();
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }




}
