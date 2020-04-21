<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\InfosContacts;



class HomeController extends AbstractController
{
    

    /**
     * @Route("/", name="Home")
     */
    public function home(InfosContacts $infosContactService)
    {
        
        if ($this->getUser() !== null)
        {
            // L'utilisateur est bien identifié.
            return $this->redirectToRoute('Contact.Show');
        }






        $nbrContacts = 0;
        if ( $this->getUser() !== null )
        { $nbrContacts = $infosContactService->GetNbrContacts($this->getUser()->getKeyPrivate()); }
        
        return $this->render('home/index.html.twig', [
            'nbrContacts' => $nbrContacts
        ]);
    }
    

    /**
     * @Route("/faq", name="faq")
     */
    public function faq()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    /**
     * @Route("/Contact", name="Contact")
     */
    public function Contact()
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }




}
