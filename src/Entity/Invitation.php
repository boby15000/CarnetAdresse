<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert ;


/**
 * @ORM\Entity(repositoryClass="App\Repository\InvitationRepository")
 */
class Invitation
{
    

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\Regex(
     *     pattern="%^(([^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4})(;?))+%",
     *     match=true,
     *     message="L'email du/des destinaires ne correspond pas"
     * )
     */
    private $Destinataires;
  
    /**
     * @var Indique si l'invité à répondu à l'invitation
     * @ORM\Column(type="boolean")
     */
    private $Rempli;

    /**
     * @var Indique si l'invité à bloquer l'invitation
     * @ORM\Column(type="boolean")
     */
    private $Bloquer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $CreatedAt;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $KeyPublic;


    

/* ------ FONCTION DES SETTERS ET GETTERS ------ */


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

    public function getRempli(): bool
    {
        return $this->Rempli;
    }

    public function setRempli(bool $Rempli): self
    {
        $this->Rempli = $Rempli;

        return $this;
    }  

    public function getBloquer(): bool
    {
        return $this->Bloquer;
    }

    public function setBloquer(bool $Bloquer): self
    {
        $this->Bloquer = $Bloquer;

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


    public function getKeyPublic(): ?string
    {
        return $this->KeyPublic;
    }

    public function setKeyPublic(string $KeyPublic): self
    {
        $this->KeyPublic = $KeyPublic;

        return $this;
    }



/* ------ FONCTION DIVERSES ------ */



    public function __construct()
    {
        $this->CreatedAt = new \DateTime();
        $this->Rempli = false;
        $this->Bloquer = false;
    }


    public function getArrayDestinataires(): ?array
    {
        if ( $this->Destinataires === null )
        { return null ;}

        return \array_filter(\explode (";", $this->Destinataires));
    }



}
