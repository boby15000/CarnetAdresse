<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Form\FicheType;
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
    

    const LETTRE_PAR_DEFAUT = 'A';


    /**
     * @Route("/Fiches-{Letter}", name="Fiche.voirTout", requirements={"letter"="[A-Z]{1}"})
     */
    public function Show($Letter, PaginatorInterface $paginator, Request $request)
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
        return $this->render('fiche/show.html.twig', ['fiches' => $pagination,'letter' => $Letter]);
    }



    /**
     * @Route("/Ajouter", name="Fiche.ajouter")
     */
    public function Add(Request $request)
    {   
        $modeEdition = false;
        $fiche = new Fiche(); // initiale un nouveau contact.
        $form = $this->createForm(FicheType::class, $fiche);
        $form->handleRequest($request); // Enregistre le valeur de POST dans un objet contact.
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid())
        {
            // Attribut l'utilisateur propriétaire de la fiche.
            $fiche->setUser($this->getUser());
            // Enregistre le contact en base de données.
            $em = $this->GetDoctrine()->GetManager(); 
            $em->persist($fiche);
            $em->flush();

            $fiche = new Fiche(); // initiale un nouveau contact.
            $form = $this->createForm(FicheType::class, $fiche);
            // Message flash de success.
            $titrefiche = ( $fiche->isProfessionel() ) ? $fiche->getLibelle() : $fiche->getNomComplet() ;
            $this->addFlash('success','La fiche de '. $titrefiche .' est enregistré.');

        }

        // Retourne la vue des contacts.
        return $this->render('fiche/add_edit.html.twig',['form' => $form->createView(), 'modeEdition'=> $modeEdition]);
    }





    /**
     * @Route("/Modifier-{id}-{Letter}", name="Fiche.modifier", requirements={"id"="\d*", "letter"="[A-Z]{1}"})
     */
    public function Edit(Fiche $fiche, $Letter, Request $request)
    {   
        $modeEdition = true;
        $form = $this->createForm(FicheType::class, $fiche);
        $form->handleRequest($request); // Enregistre le valeur de POST dans un objet contact.
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid())
        {
            // Attribut l'utilisateur propriétaire de la fiche.
            //$fiche->setUser($this->getUser());
            // Enregistre le contact en base de données.
            $em = $this->GetDoctrine()->GetManager(); 
            $em->persist($fiche);
            $em->flush();

            // Message flash de success.
            $titrefiche = ( $fiche->isProfessionel() ) ? $fiche->getLibelle() : $fiche->getNomComplet() ;
            $this->addFlash('success','La fiche de '. $titrefiche .' est modifié.');

            $fiche = new Fiche(); // initiale un nouveau contact.
            $form = $this->createForm(FicheType::class, $fiche);

            return $this->redirectToRoute('Fiche.voirTout', ['Letter'=> $Letter]);

        }

        // Retourne la vue des contacts.
        return $this->render('fiche/add_edit.html.twig', ['form' => $form->createView(), 'Letter'=> $Letter, 'modeEdition'=> $modeEdition]);
    }




    /**
     * @Route("/Supprimer-{id}-{Letter}", name="Fiche.supprimer", requirements={"id"="\d*", "letter"="[A-Z]{1}"})
     */
    public function Delete(Fiche $fiche, $Letter)
    {   
      
        $em = $this->getDoctrine()->getManager();
        $em->remove($fiche);
        $em->flush();

        // Message flash de success.
        $this->addFlash('success','La fiche de '. $fiche->getNomComplet() .' est supprimé.');

        return $this->redirectToRoute('Fiche.voirTout', ['Letter'=> $Letter]);
    }







}
