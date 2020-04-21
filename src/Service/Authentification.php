<?php

namespace App\Service;

use App\Entity\Contact;
use App\Service\Email\MailJet;
use App\Service\Email\Message;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


/**
 * 
 */
class Authentification 
{
	
	private $doctrine;
	private $passwordEncoder;
	private $mailjet;
	


	public function __construct(ManagerRegistry $doctrine, UserPasswordEncoderInterface $passwordEncoder, MailJet $mailjet)
	{
		$this->doctrine = $doctrine;
		$this->passwordEncoder = $passwordEncoder;
		$this->mailjet = $mailjet;

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
		$urlPageActivation = $this->generateUrl('Activation', ['clefPublic'=>$key], UrlGeneratorInterface::ABSOLUTE_URL);
		// Envoyer le mail d'activation 
		$this->mailjet->addMessage( 
                    (new Message)
                        ->to($user->getEmail())
                        ->subject('Invitation de ' . $user->getNomComplet())
                        ->html($this->render('authentification/email/activation.message.html.twig', ['Url' => $urlPageActivation]))
                );
		
		// Controle que le mail est bien envoyé.   
        if ( $this->mailJet->send() )
        { 
            // Enregistre les modification
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
		// Si non null, mettre à True la variable Activer
		if ( $user === null )
        { return array('success' => false, 'message' => "Une erreur est survenu lors de l'activation : impossible de définir l'utilisateur." ); }

    	// Active le compte
    	$user->setActiver(true);
		// Enregistre les modification
		$this->SaveBDD($user);

		return array('success' => true, 'message' => "Votre compte est activé." );
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
	protected function SaveBDD(User $user):void
	{
		$em = $this->doctrine->GetManager();
        $em->persist($user);
        $em->flush();
	}


}