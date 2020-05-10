<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangeMotDePasseType;
use App\Form\ContactType;
use App\Form\IdentificationType;
use App\Form\UserType;
use App\Service\Email\Mailjet;
use App\Service\Email\NewMessage;
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
     * Page Contact
     * 
     * @Route("/Contact", name="Contact")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Contact(Request $request, Mailjet $mailjet)
    {
        // On crée le formulaire de contact
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid()) 
        {
            
        // On récupére les données du formulaire et on initie Doctrine.
        $credentials = $form->getData();

        // Envoyer l'email  
        $message = $mailjet->NewMessage()
                    ->to($this->getParameter('Email_Webmaster'))
                    ->replyTo( \strtolower($credentials['de'])  ) 
                    ->subject('Email du site Carnet-Adresse.fr')
                    ->html($credentials['message']);


         if ( $mailjet->AddMessage($message)->Send() )
         { $this->addFlash('success',"L'email est bien envoyé.<br> Si vous avez noté votre email vous recevrez une réponse."); }
         else
         { $this->addFlash('warning',"Echec de l'envoie de l'email."); }  
            
            return $this->redirectToRoute('login');
        }
        
        // Retourne la page d'inscription.
        return $this->render('security/contact.html.twig', ['form' => $form->createView()]);
        
    }

/**************************************************************************************************************************/


   /**
     * Fonction exécuté après l'authentification.
     * 
     * @Route("/login", name="login")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function login(Request $request, AuthenticationUtils $AuthenticationUtils)
    {
    
        if ($this->getUser())
        {
            return $this->redirectToRoute('Fiche.voirTout', ['Letter'=> 'A']);
        }
       
        $error = $AuthenticationUtils->getLastAuthenticationError();

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

            if ( !$user->getId() && !$user->tag )
            { $this->addFlash('warning',"Erreur lors de l'inscription."); }
            else
            { $this->addFlash('success',"Votre inscription est enregistré.<br>Merci d'activer votre compte."); }  

       
            return $this->render('security/login.html.twig');
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
			$this->addFlash('warning',"Impossible d'identifier le compte de l'utilisateur."); 
			return $this->render('security/login.html.twig');
		}

		// Active le compte.
		$user->setCompteactif(true);
		// Supprimer la clef.
		$user->setClefpublic(null);
		// Enregistre les modifications.
		$em = $this->GetDoctrine()->getManager();
		$em->flush();

        $this->addFlash('success',"Votre compte est activé.<br> Vous pouvez vous connecter.");

        // Retourne la page login.
        return $this->render('security/login.html.twig');

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
                $this->addFlash('warning',"Impossible d'identifier le compte de l'utilisateur"); 
                return $this->render('security/motpasseoublie.html.twig');
            }

            // Génére la clef.
            $user->GenerateKeyPublic();
            // Enregistre les modifications, et envoie le mail grace aux evenements.
            $em = $this->GetDoctrine()->GetManager(); // Attribut un "ObjectManager".
            $em->persist($user);
            $em->flush();
dump($user);
            if ( !$user->tag )
            { $this->addFlash('warning',"Erreur lors de l'envoie du lien pour la modification du mot de passe'."); }
            else
            { $this->addFlash('success',"Un lien vient de vous être envoyé par email.<br>Celui-ci est valide 20 minutes"); }  

            return $this->render('security/motpasseoublie.html.twig');
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
       
        $form = $this->createForm(ChangeMotDePasseType::class);
        $form->handleRequest($request);
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid()) 
        {
            // On récupére les données du formulaire et on initie Doctrine.
            $credentials = $form->getData();
    
            // On récupére l'utilisateur en fonction de la Clef Publique.
            $user = $this->GetDoctrine()->getRepository(User::class)->findOneBy(['clefpublic' => $clefPublic]);

            // Controle que l'utilisateur n'est pas null.
            if ( $user === null )
            { 
                $this->addFlash('warning',"Impossible d'identifier le compte de l'utilisateur"); 
                return $this->render('security/changerlemotDepasse.html.twig',['form' => $form->createView()]);
            }

            // Change le mot de passe.
            $user->setMotdepasse($passwordEncoder->EncodePassword($user, $credentials['motdepasse'] ));
            // Supprimer la clef.
            $user->setClefpublic(null);
            // Enregistre les modifications.
            $em = $this->GetDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success',"Votre nouveau mot de passe est enregistré.<br>Vous pouvez vous connecter."); 
            return $this->render('security/login.html.twig');

        }


        // Retourne la page MotDePasseOublie.
        return $this->render('security/changerlemotdepasse.html.twig',['form' => $form->createView(), 'clefPublic' => $clefPublic]);

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