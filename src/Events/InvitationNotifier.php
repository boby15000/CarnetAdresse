<?php

namespace App\Events;


use App\Entity\Invitation;
use App\Service\Email\Mailjet;
use App\Service\Email\Message;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * Class Evenement Doctrine pour la Class USER.
 */
class InvitationNotifier
{
	
	const MESSAGE_PAR_DEFAUT = "Salut, peux-tu remplir mon carnet d'adresse. Merci";

	private $mailjet;
	private $router;
	private $twig;
	private $request;
	

	public function __construct(Mailjet $mailjet, UrlGeneratorInterface $router, Environment $twig, RequestStack $request_stack)
	{
		$this->mailjet = $mailjet;
		$this->router = $router;
		$this->twig = $twig;
		$this->request_stack = $request_stack;
	}


	/**
	 * Déclencher après un nouvel enregistrement.
	 * Envoie le mail d'invitation.
	 * 
	 * @param Invitation               $Invitation  [description]
	 * @param LifecycleEventArgs $event [description]
	 */
    public function SendMail(Invitation $invitation, ?LifecycleEventArgs $event)
    {
		//dump($invitation); dump($event); die;

		// Generer l'url de la page d'activiation
		$urlPage = $this->router->generate('Fiche.Invitation.Add', ['clefPublic'=> $invitation->getClefpublic()], UrlGeneratorInterface::ABSOLUTE_URL);
		$urlStop = $this->router->generate('Fiche.Invitation.Stop', ['clefPublic'=> $invitation->getClefpublic()], UrlGeneratorInterface::ABSOLUTE_URL);
		// Message par défaut.
		$message = ( empty($invitation->getMessage()) ) ? self::MESSAGE_PAR_DEFAUT : $invitation->getMessage() ;
		// Envoyer le mail d'activation 
		$message = $this->mailjet->NewMessage()
                        ->to($invitation->getInvite())
                        ->subject('Invitation de ' . $invitation->getUser()->getNomComplet())
                        ->html($this->twig->render('fiche\emails\invitation.email.html.twig', ['url' => $urlPage,'urlStop' => $urlStop, 'message' => $message ]));

		return $this->mailjet->AddMessage($message, true)->Send();

    }



}