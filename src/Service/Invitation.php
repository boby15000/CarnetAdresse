<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\demandebyMail;
use App\Service\MailJet;
use App\Service\Message;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;


/**
 * 
 */
class Invitation 
{
	private $mailJet;
	private $renderer;
	private $router;

	public function __construct(Environment $renderer, MailJet $mailJet, UrlGeneratorInterface $router)
	{
		$this->renderer = $renderer;
		$this->MailJet = $mailJet;
		$this->router = $router;
	}

	/*
	 * 
	 */
	public function GetInvitation(User  $user, demandebyMail $demandebyMail, ?string $messagePerso)
	{
		$generateUrl = $this->router->generate('Contact.Invitation', [
            'key' => $user->GenerateKeyPublic(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        //"http://www.carnet-adresse.fr/Invitation-fdgnsjfvhnaquf51d6yg5d2g";

		$this->MailJet
                ->addMessage( 
                    (new Message)
                        ->from('boby15000@hotmail.com', 'Nicolas Fourgheon')
                        ->to($demandebyMail->getDestinataires())
                        ->subject('Invitation de ' . $user->getNomComplet())
                        ->html($this->renderer->render('Contact/Messages/message.html.twig', ['MessagePerso' => $messagePerso, 'GenerateUrl' => $generateUrl]))
                )
                ->send();

		/*
		$email = ( new Email() )
            -> from ( 'invitation@carnetadresse.fr' )
            -> to ( $demandebyMail->getDestinataires() )
            //->cc(' cc@example.com ')
            //->bcc(' bcc@example.com ')
            //->replyTo(' fabien@example.com ')
            ->priority(Email::PRIORITY_HIGH)
            ->subject( 'Invitation de ' . $user->getPrenom() . ' ' . $user->getNom() )
            ->html( $this->renderer->render('Contact/Messages/message.html.twig', ['MessagePerso' => $MessagePerso]));

        $this->mailer->send( $email );
       
		*/
	}


	public function GetResponse()
	{
		return null;
	}


}