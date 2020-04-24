<?php

namespace App\Service;

use App\Entity\Invitation;
use App\Entity\User;
use App\Service\Authentification;
use App\Service\Email\MailJet;
use App\Service\Email\Message;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;




/**
 * 
 */
class InvitationServ
{
	
	private $doctrine;
	private $mailjet;
	private $router;
	private $twig;
	private $authentification;
	


	public function __construct(ManagerRegistry $doctrine, UrlGeneratorInterface $router, MailJet $mailjet, Environment $twig, Authentification $authentification)
	{
		$this->doctrine = $doctrine;
		$this->mailjet = $mailjet;
		$this->router = $router;
		$this->twig = $twig;
		$this->authentification = $authentification;

	}





	/**
	 * Envoie le mail d'invitation
	 * 
	 * @param  Invitation $invitation [description]
	 * @param  User       $user       [description]
	 * @return array                 [description]
	 */
	public function getInvitation(Invitation $invitation, User $user, string $message): ?array
	{
		//Génére la clef public pour l'activiation du compte.
        $clefPublic = $user->GenerateKeyPublic();
		// Generer l'url de la page du formulaire
		$urlremplir = $this->router->generate('Contact.Invitation', ['clefPublic'=> $clefPublic], UrlGeneratorInterface::ABSOLUTE_URL);
		// Generer l'url de la page STOP
		$urlstop = $this->router->generate('Contact.InvitationStop', [], UrlGeneratorInterface::ABSOLUTE_URL);
		// Envoyer le mail d'activation 
		$this->mailjet->addMessage( 
                    (new Message)
                        ->toMulti($invitation->getArrayDestinataires())
                        ->subject('Invitation de ' . $user->getNomComplet())
                        ->html($this->twig->render('contact/emails/invitation.email.html.twig', ['urlremplir' => $urlremplir, 'urlstop' => $urlstop, 'message' => $message ]))
                );
		dump($this->mailjet);
		// Controle que le mail est bien envoyé.   
        if ( $this->mailjet->send() )
        { 
            // Enregistre les modifications uniquement si le mail est envoyé.
			$this->authentification->SaveBDD($user);
			$this->Save($invitation,  $clefPublic);
            return array('success' => true, 'message' => "Votre invitation est envoyé." );
            
        }
        else
        { return array('success' => false, 'message' => "Une erreur est survenu lors de la demande d'invitation. Veuillez la renouveller" ); }
	}







	/**
	 * Retourne l'utilisateur associé à la clef public.
	 * @param  [type] $clefPublic [description]
	 * @return [type]             [description]
	 */
	public function getUserViaKeyPublic($clefPublic): ?user
	{
		// Récupérer l'utilisateur via la clef publique
		$user = $this->doctrine->getRepository(User::class)->FindOneByClefPublic($clefPublic);

		if ( !$user )
        { 
        	$user->ClearKeyPublic() ;
        	$this->authentification->SaveBDD($user);
        }

        return $user;
	}





	public function Save(invitation $invitation, string  $clefPublic)
	{
		$em = $this->doctrine->getManager();
 
		switch ( \count( $invitation->getArrayDestinataires() ) ) 
		{
		    
		  	case 0:
		  		return null;
		        break;
		    case 1:
		    	$invitation->setKeyPublic($clefPublic);
		        $em->persist($invitation);
		        break;
		    default;
		        
		    	foreach ($invitation->getArrayDestinataires() as $key => $value) 
		    	{
		    		$uneInvitation = new Invitation();
		    		$uneInvitation->setDestinataires($value);
		    		$uneInvitation->setKeyPublic($clefPublic);
		    		$em->persist($uneInvitation);
		    	}
		        break;
		}

		$em->flush();

	}



}