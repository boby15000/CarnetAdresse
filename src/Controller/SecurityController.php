<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\Email\Mailjet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;




class SecurityController extends AbstractController
{


	/**
     * Page d'accueil.
     * Execute les scripts de nettoyage et autres.
     * 
     * @Route("/", name="home")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function home()
    {
        // Supprime les comptes inactif supérieur à 1 jour.
        $this->GetDoctrine()->getRepository(User::class)->DeleteInactif();
        return $this->redirectToRoute('login');
    }



/**************************************************************************************************************************/


   /**
     * Fonction exécuté après l'authentification.
     * 
     * @Route("/login", name="login")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function login(AuthenticationUtils $AuthenticationUtils)
    {
        // Récupére les erreurs probable de l'authentification.
        $error = $AuthenticationUtils->getLastAuthenticationError();

        // Si authentifier, on redirige vers la liste des contacts.
        if ( $this->getUser() !== null ) 
        { return $this->redirectToRoute('VoirFiches'); }

        // Récupère le dernier Identifiant saisi par l'utilisateur.
        $lastUsername = $AuthenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_Username' => $lastUsername, 'error' => $error]);

    }



/**************************************************************************************************************************/




    /**
     * Inscription d'un nouvel utilisateur.
     * L'envoie du mail pour l'activation est réalisé après l'enregistrement
     * 
     * @Route("/Inscription", name="Inscription")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Inscription(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User(); // initiale un nouvel utilisateur.
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid()) 
        {
            // Encode le mot de passe.
            $user->setMotdepasse($passwordEncoder->EncodePassword($user, $user->getMotdepasse() )); 
            // Enregistre le nouvel utilisateur.
            $em = $this->GetDoctrine()->GetManager(); // Attribut un "ObjectManager".
          	$em->persist($user);
            $em->flush();

            $this->result = ( !$user->getId() ) ? ['success'=> false, 'message' => "Erreur lors de l'inscription."] : ['success'=> true, 'message' => "Votre inscription est enregistré. Merci d'activer votre compte."] ;

            return $this->render('security/login.html.twig', ['result' => $this->result]);
        }
        

        // Retourne la page d'inscription.
        return $this->render('security/inscription.html.twig',['form' => $form->createView()]);

    }




/**************************************************************************************************************************/


   /**
     * Activation d'un nouvel utilisateur.
     * 
     * @Route("/Activation-{clefPublic}", name="Activation")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Activation($clefPublic)
    {
        
    	// On récupére l'utilisateur
		$user = $this->GetDoctrine()->getRepository(User::class)->findOneBy(['clefpublic' => $clefPublic]);

		// Controle que l'utilisateur n'est pas null.
		if ( $user === null )
		{ 
			$this->result = ['success'=> false, 'message' => "Impossible d'identifier le compte de l'utilisateur."]; 
			return $this->render('security/login.html.twig',['result' => $this->result]);
		}

		// Active le compte.
		$user->setCompteactif(true);
		// Supprimer la clef.
		$user->setClefpublic(null);
		// Enregistre les modifications.
		$em = $this->GetDoctrine()->getManager();
		$em->flush();


        $this->result = ['success'=> true, 'message' => "Votre compte est activé."];

        // Retourne la page login.
        return $this->render('security/login.html.twig',['result' => $this->result]);

    }






/**************************************************************************************************************************/



    /**
     * Génére l'url et envoie le mail avec l'url pour changer le mot de passe.
     * 
     * @Route("/MotDePasseOublie", name="MotDePasseOublie")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function MotDePasseOublie(Request $request)
    {
    	// On récupére le csrf.
        $csrf_token = $request->request->get('csrf_token');

        // Controle si le formulaire est émis et que le csrf est valide.
        if ( $request->getMethod() === $request::METHOD_POST && $this->isCsrfTokenValid('MotDePasseOublie', $csrf_token) ) 
        { 
    		// On récupére l'utilisateur en fonction de l'email.
            $user = $this->GetDoctrine()->getRepository(User::class)->findOneBy(['email' => $request->request->get('email')]);

            // Controle que l'utilisateur n'est pas null.
            if ( $user === null )
            { 
                $this->result = ['success'=> false, 'message' => "Impossible d'identifier le compte de l'utilisateur."]; 
                return $this->render('security/motpasseoublie.html.twig',['result' => $this->result]);
            }

            // Génére la clef.
            $user->GenerateKeyPublic();
            // Enregistre les modifications, et envoie le mail grace aux evenements.
            $em = $this->GetDoctrine()->getManager();
            $em->flush();

            $this->result = ['success'=> true, 'message' => "Un email vient de vous être envoyé."]; 
            return $this->render('security/motpasseoublie.html.twig',['result' => $this->result]);
        }

    	// Retourne la page MotDePasseOublie.
        return $this->render('security/motpasseoublie.html.twig');
    }





/**************************************************************************************************************************/



    /**
     * Affiche le formulaire pour changer le mot de passe.
     * 
     * @Route("/MotPasseOublie-{clefPublic}", name="ChangerLeMotDePasse")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function ChangerLeMotDePasse($clefPublic, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // On récupére le csrf.
        $csrf_token = $request->request->get('csrf_token');

        // Controle si le formulaire est émis et que le csrf est valide.
        if ( $request->getMethod() === $request::METHOD_POST && $this->isCsrfTokenValid('changerlemotDepasse', $csrf_token) ) 
        {
            // Controle que le mot de passe n'est pas null.
            if ( $request->request->get('MotDePasse1') === null )
            { 
                $this->result = ['success'=> false, 'message' => "Le mot de passe ne peut être vide."]; 
                return $this->render('security/changerlemotDepasse.html.twig',['clefpublic' => $clefPublic, 'result' => $this->result]);
            }

            // Controle que les deux mots de passe sont identique.
            if ( $request->request->get('MotDePasse1') === $request->request->get('MotDePasse2') )
            { 
                $this->result = ['success'=> false, 'message' => "Les mots de passe sont différents."]; 
                return $this->render('security/changerlemotDepasse.html.twig',['clefpublic' => $clefPublic, 'result' => $this->result]);
            }

            // On récupére l'utilisateur en fonction de la Clef Publique.
            $user = $this->GetDoctrine()->getRepository(User::class)->findOneBy(['clefpublic' => $clefPublic]);

            // Controle que l'utilisateur n'est pas null.
            if ( $user === null )
            { 
                $this->result = ['success'=> false, 'message' => "Impossible d'identifier le compte de l'utilisateur."]; 
                return $this->render('security/changerlemotDepasse.html.twig',['clefpublic' => $clefPublic, 'result' => $this->result]);
            }


        // Change le mot de passe.
        $user->setMotdepasse($passwordEncoder->EncodePassword($user, $request->request->get('MotDePasse') ));
        // Supprimer la clef.
        $user->setClefpublic(null);
        // Enregistre les modifications.
        $em = $this->GetDoctrine()->getManager();
        $em->flush();


        $this->result = ['success'=> true, 'message' => "Votre nouveau mot de passe est enregistré. Vous pouvez vous connecter."];
        return $this->render('security/login.html.twig',['result' => $this->result]);

        }

        // Retourne la page MotDePasseOublie.
        return $this->render('security/changerlemotDepasse.html.twig',['clefpublic' => $clefPublic]);

    }












 /**
     * Affiche le formulaire pour changer le mot de passe.
     * 
     * @Route("/email", name="EmailTest")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function EmailTest(Mailjet $mailjet)
    {
        $send = 0;
      
        $message = $mailjet->NewMessage()
                        ->from("inscription@carnet-adresse.fr")
                        ->to("boby15000@hotmail.com")
                        ->subject("test d'envoie de mail")
                        ->html("test d'envoie");

        $send = $mailjet->AddMessage($message)->Send();

        dump($mailjet);

        return $this->render('test.html.twig',['send' => $send]);

    }

}