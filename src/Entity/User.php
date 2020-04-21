<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert ;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    
    const EMAIL_ADMIN = "boby15000@hotmail.com";
    Const PERIODE_KEY_PUBLIC = 7 ; // période en jour de la validité de la Clef publique.

    /*
     * @var UserPasswordEncoderInterface 
     */
    private $passwordEncoder;


    /* --- DECLARATION DES PROPRIETES --- */


    /**
     * @var string
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
    private $Nom;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez renseigner votre Prénom.")
     * @Assert\Length(
     *      min = 3,
     *      minMessage = "Le Prénom doit contenir {{ limit }} caractères minimum",
     *      allowEmptyString = false)
     */
    private $Prenom;

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
    private $password;

    /**
     * @var array
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var datetime
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     * @ORM\Column(type="string", length=100,)
     */
    private $keyPrivate;   


    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $keyPublic;

    /**
     * @var datetime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $keyPublicCreatedAt;



/* --- DELCARATION DES SETTERS ET GETTES DES PROPRIETES --- */

    
    public function getId(): ?int
    {
        return $this->id;
    }
    
    
    public function getNom(): ?string
    {
        return $this->Nom;
    }

    public function setNom(string $Nom): self
    {
        $this->Nom = $Nom;

        return $this;
    }


    public function getPrenom(): ?string
    {
        return $this->Prenom;
    }

    public function setPrenom(string $Prenom): self
    {
        $this->Prenom = $Prenom;

        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $this->passwordEncoder->encodePassword($this,$password);

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
       
        return $this;
    }


    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }


    public function getKeyPrivate(): ?string
    {
        return $this->keyPrivate;
    }

    public function setKeyPrivate(string $keyPrivate): self
    {
        $this->keyPrivate = $keyPrivate;

        return $this;
    }


    public function getKeyPublic(): ?string
    {
        return $this->keyPublic;
    }

    public function setkeyPublic(string $keyPublic): self
    {
        $this->keyPublic = $keyPublic;

        return $this;
    }

   
    public function getKeyPublicCreatedAt(): ?\DateTimeInterface
    {
        return $this->keyPublicCreatedAt;
    }

    public function setKeyPublicCreatedAt(?\DateTimeInterface $keyPublicCreatedAt): self
    {
        $this->keyPublicCreatedAt = $keyPublicCreatedAt;

        return $this;
    }


    

    /* --- DELCARATION DES FONCTIONS --- */

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->roles[] = 'ROLE_USER';
        $this->passwordEncoder = $passwordEncoder;
        $this->createdAt = new \Datetime();
        $this->keyPrivate = \hash("sha256",\mt_rand(1, 200000));
    }


    public function GenerateKeyPublic(): ?string
    {
        $this->keyPublic = \hash("sha256",\mt_rand(1, 200000));
        $this->keyPublicCreatedAt = new \Datetime();
        return $this->keyPublic;
    }

    public function ClearKeyPublic()
    {
        $this->keyPublic = null ;
        $this->keyPublicCreatedAt = null ;
       
    }


    public function getNomComplet()
    {
        return $this->Prenom . ' ' . $this->Nom;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
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

}
