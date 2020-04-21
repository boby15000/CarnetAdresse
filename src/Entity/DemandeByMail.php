<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DemandeByMailRepository")
 */
class DemandeByMail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $Destinataires;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $KeyPublic;

    /**
     * @ORM\Column(type="datetime")
     */
    private $CreatedAt;
    

    public function __construct()
    {
        $this->CreatedAt = new \DateTime();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestinataires(): ?string
    {
        return $this->Destinataires;
    }

    public function setDestinataires(string $Destinataires): self
    {
        $this->Destinataires = $Destinataires;

        return $this;
    }

    public function getKeyPublic(): ?string
    {
        return $this->KeyPublic;
    }

    public function setKeyPublic(string $KeyPublic): self
    {
        $this->KeyPublic = $KeyPublic;

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
}
