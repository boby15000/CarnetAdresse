<?php

namespace App\Events;

use App\Entity\User;
use App\Service\Email\Mailjet;
use App\Service\Email\Message;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * Class Evenement Doctrine pour la Class USER.
 */
class UserNotifier
{
	
	Const ROUTE_MOTDEPASSE = 'MotDePasseOublie';

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
	 * Envoie le mail d'activation à l'utilisateur.
	 * 
	 * @param User               $user  [description]
	 * @param LifecycleEventArgs $event [description]
	 */
    public function SendMailActivation(User $user, LifecycleEventArgs $event)
    {
		// Generer l'url de la page d'activiation
		$urlPage = $this->router->generate('Activation', ['clefPublic'=> $user->getClefpublic()], UrlGeneratorInterface::ABSOLUTE_URL);
		// Envoyer le mail d'activation 
		$message = $this->mailjet->NewMessage()
                        ->to($user->getEmail())
                        ->subject('Activation necessaire pour le compte de ' . $user->getNomComplet())
                        ->html($this->twig->render('security/email/activation.email.html.twig', ['user' => $user, 'url' => $urlPage]));

		//$this->mailjet->AddMessage($message)->Send();	
    }





    /**
	 * Déclencher après un nouvel enregistrement.
	 * Envoie le mail d'activation à l'utilisateur.
	 * 
	 * @param User               $user  [description]
	 * @param LifecycleEventArgs $event [description]
	 */
    public function SendMailMotDePasseOublie(User $user, LifecycleEventArgs $event)
    {
		
    	if (  $this->request_stack->getMasterRequest()->attributes->get('_route') === self::ROUTE_MOTDEPASSE )
    	{
			// Generer l'url de la page d'activiation
			$urlPage = $this->router->generate('Activation', ['clefPublic'=> $user->getClefpublic()], UrlGeneratorInterface::ABSOLUTE_URL);
			// Envoyer le mail d'activation 
			$message = $this->mailjet->NewMessage()
	                        ->to($user->getEmail())
	                        ->subject('Demande de réinitialisation du mot de passe.')
	                        ->html($this->twig->render('security/email/motpasseoublie.email.html.twig', ['url' => $urlPage]));

			//$this->mailjet->AddMessage($message)->Send();
		}
		

    }





}