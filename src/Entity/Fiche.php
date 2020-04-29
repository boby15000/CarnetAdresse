<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FicheRepository")
 * @ORM\HasLifecycleCallbacks()
 * @Assert\Expression(
 *     "this.getTelFixe() !== null or this.getTelPortable() !== null",
 *     message="Au moins un téléphone doit-être renseignés."
 * )
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
    private $professionel;

    /**
     * @var string Défini le Nom professionnel | null dans les autres cas
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Expression(
     *     "this.getLibelle() != null or !this.isProfessionel()",
     *     message="Le libellé est obligatoire pour les fiches professionnelles"
     * )
     */
    private $libelle;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Expression(
     *     "this.getNom() != null or this.isProfessionel()",
     *     message="Le Nom est obligatoire pour les fiches non professionnelles"
     * )
     */
    private $nom;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Expression(
     *     "this.getPrenom() != null or this.isProfessionel()",
     *     message="Le Prénom est obligatoire pour les fiches non professionnelles"
     * )
     */
    private $prenom;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\NotNull(message="Veuillez renseigner l'Adresse.")
     * @Assert\Length(
     *      min = 10,
     *      minMessage = "L'Adresse doit contenir {{ limit }} caractères minimum",
     *      allowEmptyString = false)
     */
    private $adresse;

    /**
     * @var datetime
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Expression(
     *     "this.getDateDeNaissance() != null or this.isProfessionel()",
     *     message="La Date de naissance doit être renseigné pour les fiches non professionnelles"
     * )
     */
    private $dateDeNaissance;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $telFixe;

    /**
     * @var string
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $telPortable;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner l'email.")
     * @Assert\Email(message="L'email '{{ value }}' n'est pas valide.")
     */
    private $email;

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
        return $this->professionel;
    }

    /**
     * @param  bool Défini si la fiche est professionel $Professionel
     *
     * @return self
     */
    public function setProfessionel($professionel)
    {
        $this->professionel = $professionel;
        return $this;
    }

    /**
     * @return string Défini le Nom professionnel | null dans les autres cas
     */
    public function getLibelle()
    {
        return \ucwords($this->libelle);
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
        return \ucwords($this->nom);
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
        return \ucwords($this->prenom);
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
        return \ucwords($this->adresse);
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
     * @return datetime
     */
    public function getDateDeNaissance()
    {
        return $this->dateDeNaissance;
    }

    /**
     * @param datetime $dateDeNaissance
     *
     * @return self
     */
    public function setDateDeNaissance(?\datetime $dateDeNaissance)
    {
        $this->dateDeNaissance = $dateDeNaissance;

        return $this;
    }

    /**
     * @return string
     */
    public function getTelFixe()
    {
        return $this->telFixe;
    }

    /**
     * @param string $telFixe
     *
     * @return self
     */
    public function setTelFixe($telFixe)
    {
        $this->telFixe = $telFixe;

        return $this;
    }

    /**
     * @return string
     */
    public function getTelPortable()
    {
        return $this->telPortable;
    }

    /**
     * @param string $telPortable
     *
     * @return self
     */
    public function setTelPortable($telPortable)
    {
        $this->telPortable = $telPortable;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $emails
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = \strtolower($email);

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
        
        return ($this->modifierle === null ) ? $this->creerle : $this->modifierle ;
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
        $this->professionel = false;
        $this->creerle = new \DateTime();
    }

    public function getNomComplet(): string
    {
        return \strtoupper($this->nom . ' ' . $this->prenom) ;
    }

    /**
     * Défini la Date à chaque modification de la fiche.
     * 
     * @ORM\PreUpdate
     */
    public function UpdateModifierle()
    {
        $this->modifierle = new \DateTime();
        $this->libelle = ( $this->professionel ) ? $this->libelle : null ;
        $this->dateDeNaissance = ( $this->professionel ) ? null : $this->dateDeNaissance ;

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
