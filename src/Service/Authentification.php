<?php

namespace App\Service;

use App\Entity\Contact;
use App\Entity\User;
use App\Service\Email\MailJet;
use App\Service\Email\Message;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Twig\Environment;


/**
 * 
 */
class Authentification 
{
	
	private $doctrine;
	private $passwordEncoder;
	private $mailjet;
	private $router;
	private $twig;
	


	public function __construct(ManagerRegistry $doctrine, UserPasswordEncoderInterface $passwordEncoder,UrlGeneratorInterface $router, MailJet $mailjet, Environment $twig)
	{
		$this->doctrine = $doctrine;
		$this->passwordEncoder = $passwordEncoder;
		$this->mailjet = $mailjet;
		$this->router = $router;
		$this->twig = $twig;

	}



	/**
	 * Envoie l'email d'activation à l'utilisateur.
	 * 
	 * @param User $user Un Utilisateur
	 */
	public function EmailActivate(User $user): array
	{
		//Génére la clef public pour l'activiation du compte.
        $key = $user->GenerateKeyPublic();
		// Generer l'url de la page d'activiation
		$urlPageActivation = $this->router->generate('Activation', ['clefPublic'=>$key], UrlGeneratorInterface::ABSOLUTE_URL);
		// Envoyer le mail d'activation 
		$this->mailjet->addMessage( 
                    (new Message)
                        ->to($user->getEmail())
                        ->subject('Activation necessaire pour le compte de ' . $user->getNomComplet())
                        ->html($this->twig->render('authentification/email/activation.email.html.twig', ['Url' => $urlPageActivation]))
                );

		// Controle que le mail est bien envoyé.   
        if ( $this->mailjet->send() )
        { 
            // Enregistre les modifications uniquement si le mail est envoyé.
			$this->SaveBDD($user);
            return array('success' => true, 'message' => "Votre inscription est enregistré. Merci de l'activer via le mail que vous avez reçu." );
            
        }
        else
        { return array('success' => false, 'message' => "Une erreur est survenu lors de l'inscription. Veuillez la renouveller" ); }

	}





	/**
	 * Active le compte de l'utilisateur.
	 * 
	 * @param string $clefPublic clef public de l'utilisateur.
	 */
	public function Activate($clefPublic): array
	{
		// Récupérer l'utilisateur via la clef publique
		$user = $this->doctrine->getRepository(User::class)->FindOneByClefPublic($clefPublic);
		
		if ( $user === null )
        { return array('success' => false, 'message' => "Une erreur est survenu lors de l'activation : impossible de définir l'utilisateur." ); }

    	// Active le compte
    	$user->setActiver(true);
    	$user->ClearKeyPublic();
		// Enregistre les modification
		$this->SaveBDD($user);

		return array('success' => true, 'message' => "Votre compte est activé." );
	}





	/**
	 * Envoie l'email pour réinitialiser le mot de passe de l'utilisateur.
	 * 
	 * @param string $clefPublic clef public de l'utilisateur.
	 */
	public function EmailForgotPassword(?string $email): array
	{
		// Récupérer l'utilisateur via la clef publique
		$user = $this->doctrine->getRepository(User::class)->FindOneByMail($email);
		
		if ( $user === null )
        { return array('success' => false, 'message' => "Une erreur est survenu : impossible de définir l'utilisateur." ); }

    	//Génére la clef public pour l'activiation du compte.
        $key = $user->GenerateKeyPublic();
		// Generer l'url de la page d'activiation
		$urlPageActivation = $this->router->generate('ForgotPassword', ['clefPublic'=>$key], UrlGeneratorInterface::ABSOLUTE_URL);
		// Envoyer le mail d'activation 
		$this->mailjet->addMessage( 
                    (new Message)
                        ->to($user->getEmail())
                        ->subject('Réinitialisation du mot de passe')
                        ->html($this->twig->render('authentification/email/changepassword.email.html.twig', ['Url' => $urlPageActivation]))
                );
		
		// Controle que le mail est bien envoyé.   
        if ( $this->mailjet->send() )
        { 
            // Enregistre les modifications uniquement si le mail est envoyé.
			$this->SaveBDD($user);
            return array('success' => true, 'message' => "Un mail vient de vous être envoyé." );
            
        }
        else
        { return array('success' => false, 'message' => "Une erreur est survenu lors de la réinitialisation de votre mot de passe." ); }

	}
	



	/**
	 * Active le compte de l'utilisateur.
	 * 
	 * @param string $clefPublic clef public de l'utilisateur.
	 */
	public function ChangePassword(array $credentials): array
	{
		
		// Controle la validité des mots de passes
		if ( $credentials['password1'] !== $credentials['password2'] )
		{ return array('success' => false, 'message' => "Les mots de passe sont différents." ); }

		// Récupérer l'utilisateur via la clef publique
		$user = $this->doctrine->getRepository(User::class)->FindOneByClefPublic($credentials['clefpublic']);
		
		if ( $user === null )
        { return array('success' => false, 'message' => "Une erreur est survenu : impossible de définir l'utilisateur." ); }

    	// Encode le mot de passe
        $user->setPassword($this->EncodePassword($user, $credentials['password1'] ));
        $user->ClearKeyPublic();
		// Enregistre les modification
		$this->SaveBDD($user);

		return array('success' => true, 'message' => "Votre mot de passe est enregistré. Vous pouvez vous connecter." );
	}






	/**
	 * Encode le mot de passe selon l'encodeur
	 * 
	 * @param string $password [description]
	 */
	public function EncodePassword(User $user, string $password): string
	{
		return $this->passwordEncoder->encodePassword( $user, $user->getPassword() );
	}


	/**
	 * Enregistre les modifications dans la base de données.
	 * @param User $user Un Utilisateur
	 */
	public function SaveBDD(User $user):void
	{
		$em = $this->doctrine->GetManager();
        $em->persist($user);
        $em->flush();
	}


}