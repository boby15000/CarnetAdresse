<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    
    const EMAIL_ADMIN = "boby15000@hotmail.com";
    Const PERIODE_KEY_PUBLIC = 7 ; // période en jour de la validité de la Clef publique.



    /* --- DECLARATION DES PROPRIETES --- */


    /**
     * @var int
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre Nom.")
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Le Nom doit contenir {{ limit }} caractères minimum",
     *      allowEmptyString = false)
     */
    private $nom;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre Prénom.")
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Le Prénom doit contenir {{ limit }} caractères minimum",
     *      allowEmptyString = false)
     */
    private $prenom;

    /**
     * @var string
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Veuillez renseigner votre l'email.")
     * @Assert\Email(message="L'email '{{ value }}' n'est pas valide.")
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre mot de passe.")
     * @Assert\Length(
     *      min = 5,
     *      minMessage = "Votre mot de passe doit contenir {{ limit }} caractères minimum",
     *      allowEmptyString = false)
     * @Assert\NotCompromisedPassword(message="Ce mot de passe a été divulgué lors d'une violation de données, il ne doit pas être utilisé. Veuillez utiliser un autre mot de passe")
     */
    private $motdepasse;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Fiche", mappedBy="user", orphanRemoval=true)
     */
    private $fiches;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $compteactif;   

    /**
     * @var datetime
     * @ORM\Column(type="datetime")
     */
    private $creerle;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clefpublic;



  







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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getMotdepasse()
    {
        return $this->motdepasse;
    }

    /**
     * @param string $motdepasse
     *
     * @return self
     */
    public function setMotdepasse($motdepasse)
    {
        $this->motdepasse = $motdepasse;

        return $this;
    }

    /**
     * @param array $roles
     *
     * @return self
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }


    /**
     * @return bool
     */
    public function isCompteactif()
    {
        return $this->compteactif;
    }

    /**
     * @param bool $compteactif
     *
     * @return self
     */
    public function setCompteactif($compteactif)
    {
        $this->compteactif = $compteactif;

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
    public function setCreerle(\datetime $creerle)
    {
        $this->creerle = $creerle;

        return $this;
    }

    /**
     * @return string
     */
    public function getClefpublic()
    {
        return $this->clefpublic;
    }

    /**
     * @param string $clefpublic
     *
     * @return self
     */
    public function setClefpublic($clefpublic)
    {
        $this->clefpublic = $clefpublic;

        return $this;
    }
    




/* --- DECLARATION DES FONCTIONS USER INTERFACE--- */


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return (array) $this->roles;
    }

    /**
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->motdepasse;
    }
    
    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }




    

/* --- DECLARATION DES FONCTIONS --- */




    public function __construct()
    {
        $this->roles[] = 'ROLE_USER';
        $this->compteactif = false;
        $this->creerle = new \Datetime();
        $this->fiches = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function GenerateKeyPublic(): ?string
    {
        $this->clefpublic = \hash("sha256",$this->email);
        return $this->clefpublic ;
    }


    public function ClearKeyPublic()
    {
        $this->clefpublic = null ;       
    }


    public function getNomComplet()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    /**
     * @return Collection|fiche[]
     */
    public function getFiches(): Collection
    {
        return $this->fiches;
    }

    public function addFich(fiche $fich): self
    {
        if (!$this->fiches->contains($fich)) {
            $this->fiches[] = $fich;
            $fich->setUser($this);
        }

        return $this;
    }

    public function removeFich(fiche $fich): self
    {
        if ($this->fiches->contains($fich)) {
            $this->fiches->removeElement($fich);
            // set the owning side to null (unless already changed)
            if ($fich->getUser() === $this) {
                $fich->setUser(null);
            }
        }

        return $this;
    }

    

   
}
