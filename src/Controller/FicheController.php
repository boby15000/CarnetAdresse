<?php

namespace App\Controller;

use App\Entity\Fiche;
use Doctrine\Common\Collections\Criteria;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

 /**
  * @Route("/Fiche")
  */
class FicheController extends AbstractController
{
    /**
     * @Route("/VoirFiches-{Letter}", name="VoirFiches", requirements={"letter"="[A-Z]{1}"})
     */
    public function VoirLesFiches($Letter, PaginatorInterface $paginator, Request $request)
    {   
      // Récupérer toutes les fiches de l'utilisateur dont le Nom commence par la lettre spécifié. 
      $query = $this->GetDoctrine()->getRepository(Fiche::class)->findAllByUserAndLetter($this->getUser(), $Letter);
      $pagination = $paginator->paginate($query, $request->query->getInt('page',1),5);
      $pagination->setCustomParameters([
          'align' => 'center',
          'size' => 'small',
          'style' => 'bottom',
          'span_class' => 'whatever',
      ]);

        // Retourne la vue des contacts.
        return $this->render('fiche/voirfiches.html.twig', ['fiches' => $pagination,'letter' => $Letter]);
    }
}
