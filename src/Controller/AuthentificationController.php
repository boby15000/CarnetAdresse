<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\Authentification;
use App\Service\Email\MailJet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



class AuthentificationController extends AbstractController
{
    
    // Défini le résultat du service "Authentification"
    private $result = null;


 /**
     * Page d'accueil.
     * 
     * @Route("/", name="home")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function home()
    {
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
        { return $this->redirectToRoute('Contact.Show'); }

        // Récupère le dernier Identifiant saisi par l'utilisateur.
        $lastUsername = $AuthenticationUtils->getLastUsername();
        return $this->render('authentification/login.html.twig', ['last_Username' => $lastUsername, 'error' => $error]);

    }



/**************************************************************************************************************************/




    /**
     * Inscription d'un nouvel utilisateur.
     * 
     * @Route("/Inscription", name="Inscription")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Inscription(Request $request, Authentification $authentification)
    {
        $user = new User(); // initiale un nouvel utilisateur 
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $em = $this->GetDoctrine()->GetManager(); // Attribut un "ObjectManager".
            $em->persist($user);
            // Encode le mot de passe
            $user->setPassword($authentification->EncodePassword($user, $user->getPassword() )); 
            
            // Utilisation du service "Authentification" pour envoyer le mail à l'utilisateur.
            $this->result = $authentification->EmailActivate($clefPublic);
            
            return $this->render('authentification/login.html.twig', ['result' => $this->result]);
        }
        

        // Retourne la page d'inscription.
        return $this->render('authentification/inscription.html.twig',['form' => $form->createView()]);

    }






/**************************************************************************************************************************/


   /**
     * Activation d'un nouvel utilisateur.
     * 
     * @Route("/Activation/{clefPublic}", name="Activation")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Activation($clefPublic, Authentification $authentification)
    {
        // Utilisation du service "Authentification" pour activer le compte de l'utilisateur.
        $this->result = $authentification->Activate($clefPublic);

        // Retourne la page login.
        return $this->render('authentification/login.html.twig',['result' => $this->result]);

    }





/**************************************************************************************************************************/



    /**
     * Inscription d'un nouvel utilisateur.
     * 
     * @Route("/ForgotPassword", name="ForgotPassword")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function ForgotPassword(Request $request)
    {
        // On récupére le csrf.
        $csrf_token = $request->request->get('csrf_token');

        // Controle si le formulaire est émis.
        if ( $request->getMethod() === $request::METHOD_POST && $this->isCsrfTokenValid('ForgotPassword', $csrf_token) ) 
        {
            // On récupére l'utilisateur via l'email.
            $user = $this->GetDoctrine()->getRepository(User::class)->FindOneByMail($request->request->get('Email'));
            
            if ( $user === null )
            { 
                $this->result = array('success' => false, 'message' => "Cette addresse email est inconnu." ); 
                return $this->render('authentification/forgotpassword.html.twig',['result' => $this->result]);
            }

            //Génére la clef public pour l'activiation du compte.
            $key = $user->GenerateKeyPublic();
            // Génére l'url
            $urlPageActivation = $this->generateUrl('Activation', ['clefpublic' => $key], UrlGeneratorInterface::ABSOLUTE_URL);
            // Envoye le mail pour l'activation.
            $mailJet
                ->addMessage( 
                    (new Message)
                        ->to($user->getEmail())
                        ->subject('Invitation de ' . $user->getNomComplet())
                        ->html($this->render('authentification/email/activation.message.html.twig', ['Url' => $urlPageActivation]))
                );

            // Controle que le mail est bien envoyé.   
            if ( $mailJet->send() )
            { $this->result = array('success' => true, 'message' => "Un mail vient de voir être envoyé." ); }
            else
            { $this->result = array('success' => false, 'message' => "Une erreur est survenu lors de la réinitialisation du mot de passe." ); }
            

        }

        // Retourne la page ForgotPassword.
        return $this->render('authentification/forgotpassword.html.twig',['result' => $this->result]);

    }





/**************************************************************************************************************************/



    /**
     * Changement du mot de passe de l'utilisateur.
     * 
     * @Route("/ForgotPassword/{clefPublic}", name="ChangePassword")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function ChangePassword($clefPublic)
    {

        // On récupére le csrf.
        $csrf_token = $request->request->get('csrf_token');

        // Controle si le formulaire est émis.
        if ( $request->getMethod() === $request::METHOD_POST && $this->isCsrfTokenValid('ChangePassword', $csrf_token) ) 
        {
            // On récupére l'utilisateur via l'email.
            $user = $this->GetDoctrine()->getRepository(User::class)->FindOneByMail($request->request->get('Email'));
            
            if ( $user === null )
            { 
                $this->result = array('success' => false, 'message' => "Cette addresse email est inconnu." ); 
                return $this->render('authentification/forgotpassword.html.twig',['result' => $this->result]);
            }


            // Encode le mot de passe
            $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword() )); 
            // Enregistre les modifications
            $em = $this->GetDoctrine()->GetManager();
            $em->persist($user);
            $em->flush();

        }



        // Retourne la page ForgotPassword.
        return $this->render('authentification/changepassword.html.twig',['result' => $this->result]);

    }


}





