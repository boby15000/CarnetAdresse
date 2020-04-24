<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert ;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 */
class Contact
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Professionel;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull(message="Veuillez renseigner Le Nom.")
     */
    private $Nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Prenom;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotNull(message="Veuillez renseigner l'Adresse.")
     */
    private $Adresse;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotNull(message="Veuillez renseigner le numéro de téléphone.")
     */
    private $Tel;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Email;

    /**
     * @ORM\Column(type="datetime")
     */
    private $CreatedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $ModifAt;

    /**
     * @ORM\Column(type="string")
     */
    private $PrivateKey;

    public function __construct()
    {
        $this->CreatedAt = new \DateTime();
    }
  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfessionel(): ?bool
    {
        return $this->Professionel;
    }

    public function setProfessionel(bool $Professionel): self
    {
        $this->Professionel = $Professionel;

        return $this;
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

    public function setPrenom(?string $Prenom): self
    {
        $this->Prenom = $Prenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->Adresse;
    }

    public function setAdresse(string $Adresse): self
    {
        $this->Adresse = $Adresse;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->Tel;
    }

    public function setTel(string $Tel): self
    {
        $this->Tel = $Tel;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeInterface $CreatedAt): self
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    public function getModifAt(): ?\DateTimeInterface
    {
        return $this->ModifAt;
    }

    public function setModifAt(?\DateTimeInterface $ModifAt): self
    {
        $this->ModifAt = $ModifAt;

        return $this;
    }

    public function getPrivateKey(): ?string
    {
        return $this->PrivateKey;
    }

    public function setPrivateKey(string $PrivateKey): self
    {
        $this->PrivateKey = $PrivateKey;

        return $this;
    }


    public function getNomComplet(): string
    {
        return $this->Nom . ' ' . $this->Prenom ;
    }

}
