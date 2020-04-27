<?php

namespace App\Controller;

use App\Entity\Fiche;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

 /**
  * @Route("/Fiche")
  */
class FicheController extends AbstractController
{
    /**
     * @Route("/VoirFiches", name="VoirFiches")
     */
    public function VoirLesFiches()
    {
        
    	// On récupére les fiches.
		$query = $this->GetDoctrine()->getRepository(Fiche::class)->findAll();


    	$indexLetter = "A";
    	$lesContacts = null;


    	dump($query);

    	dump($this->getUser());

        // Retourne la vue des contacts.
        return $this->render('fiche/voirfiches.html.twig', ['lesContacts' => $lesContacts,'IndexLetter' => $indexLetter]);
    }
}
