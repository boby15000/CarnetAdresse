<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InvitationRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(fields= {"invite", "user"}, message="Vous avez déjà invité  '{{ value }}' à remplir ses informations.")
 */
class Invitation 
{
    const DUREE_AVANT_RELANCE_ADMIN = 1 ;// en jours
    const DUREE_AVANT_RELANCE = 7; // en jours
    
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
     * @Assert\Email(message="{{ value }} n'est pas valide.")
     */
    private $invite;

    /**
     * @var string
     */
    private $message;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $comfirmer;

     /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $stop;    

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
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $clefpublic;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="invitations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    public $tag;


    /* --- DECLARATION DES PROPRIETES --- */

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
    public function getInvite()
    {
        return $this->invite;
    }

    /**
     * @param string $invite
     *
     * @return self
     */
    public function setInvite($invite)
    {
        $this->invite = $invite;

        return $this;
    }

    /**
     * @return bool
     */
    public function isComfirmer()
    {
        return $this->comfirmer;
    }

    /**
     * @param bool $comfirmer
     *
     * @return self
     */
    public function setComfirmer($comfirmer)
    {
        $this->comfirmer = $comfirmer;

        return $this;
    }

    /**
     * @return bool
     */
    public function isStop()
    {
        return $this->stop;
    }

    /**
     * @param bool $stop
     *
     * @return self
     */
    public function setStop($stop)
    {
        $this->stop = $stop;

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
        
        return ($this->modifierle === null) ? $this->creerle : $this->modifierle ;
    }

    /**
     * @param datetime $modifierle
     *
     * @return self
     */
    public function setModifierle(\datetime $modifierle)
    {
        $this->modifierle = $modifierle;

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


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     *
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }



    /* --- DECLARATION DES FONCTIONS --- */



    public function __construct()
    {
        $this->comfirmer = false;
        $this->creerle = new \Datetime();
        $this->stop = false;
    }


    /**
     * Défini la Date à chaque modification.
     * 
     * @ORM\PreUpdate
     */
    public function UpdateModifierle()
    {
        $this->modifierle = new \DateTime();
    }


    /**
     * @ORM\PrePersist
     */
    public function GenerateKeyPublic(): ?string
    {
        $this->clefpublic = \hash("sha256",$this->invite);
        return $this->clefpublic ;
    }


    public function getStatus(): string
    {

        //return ( $this->comfirmer && !$this->stop ) || 

        if ( $this->comfirmer && !$this->stop )
        { return 'Validé'; }
        elseif ( !$this->comfirmer && $this->stop )
        { return 'Bloqué'; }
        elseif ( !$this->comfirmer && !$this->stop )
        { return 'En Attente'; }
        else
        { return 'erreur'; }    

    }


    public function Relance()
    {
        $Aujourdhui = new \DateTime();
        $interval = $this->getModifierle()->diff($Aujourdhui);
        
        return ( ( !$this->comfirmer && !$this->stop ) && 
            ( ( $this->user->getRoles() == ['ROLE_ADMIN'] && $interval->format('%d') > self::DUREE_AVANT_RELANCE_ADMIN ) || 
                $interval->format('%d') > self::DUREE_AVANT_RELANCE ) ) ;
    }


}
