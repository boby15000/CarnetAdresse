<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\InfosContacts;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthentificationController extends AbstractController
{
    /**
     * Fonction excécuter après l'authentification interne de Symfony.
     * @Route("/login", name="login")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function login(AuthenticationUtils $AuthenticationUtils, InfosContacts $infosContactService)
    {
        // Récupére les erreurs probable de l'authentification.
        $error = $AuthenticationUtils->getLastAuthenticationError();

        /* Si il n'y a pas d'erreur et que l'utilisateur est bien authentifié, on redigire l'utilisateur vers la liste de ses contacts. 
        dans le cas contraire il est dirigé vers la page d'accueil & d'identification.
        */
        if ($error == null and $this->getUser() !== null)
        {
            // L'utilisateur est bien identifié.
            return $this->redirectToRoute('Contact.Show', [], 301);
        }
        else
        {
            // Echec de l'identification.
            // Récupère le dernier Identifiant saisi par l'utilisateur.
            $lastUsername = $AuthenticationUtils->getLastUsername();
            return $this->render('home/index.html.twig', ['last_Username' => $lastUsername, 'error' => $error]);
        }        
    }


    /**
     * Inscription d'un nouvel utilisateur.
     * @Route("/Inscription", name="Inscription")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Inscription(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {        
        $inscriptionSuccess = false; // Variable indiquant si un ajout en base de donnée est fait.
        $user = new User($passwordEncoder); // initiale un nouvel utilisateur 
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $em = $this->GetDoctrine()->GetManager(); // Attribut un "ObjectManager".
            $em->persist($user);
            $em->flush();

            $inscriptionSuccess = true;
            return $this->render('home/index.html.twig', ['inscriptionSuccess' => $inscriptionSuccess]);
        }

        // Retourne la page d'ajout de contact. La variable "result" permet de définir si un contact vient d'être ajouté.
        return $this->render('authentification/Inscription.html.twig', 
            ['form' => $form->createView()]);
    }



}
