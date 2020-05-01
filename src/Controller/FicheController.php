<?php

namespace App\Controller;

use App\Entity\Fiche;
use App\Entity\Invitation;
use App\Entity\User;
use App\Form\FicheType;
use App\Form\InvitationType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

 

class FicheController extends AbstractController
{
    

    const LETTRE_PAR_DEFAUT = 'A';


    /**
     * @Route("/Fiche/Fiches-{Letter}", name="Fiche.voirTout", requirements={"letter"="[A-Z]{1}"})
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



/**************************************************************************************************************************/


    /**
     * @Route("/Fiche/Ajouter", name="Fiche.ajouter")
     */
    public function Add(Request $request)
    {   
        $modeEdition = false;
        $fiche = new Fiche(); // initiale un nouvel fiche.
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

            $fiche = new Fiche(); // initiale un nouvel fiche.
            $form = $this->createForm(FicheType::class, $fiche);
            // Message flash de success.
            $titrefiche = ( $fiche->isProfessionel() ) ? $fiche->getLibelle() : $fiche->getNomComplet() ;
            $this->addFlash('success','La fiche de '. $titrefiche .' est enregistré.');

        }

        // Retourne la vue des contacts.
        return $this->render('fiche/add_edit.html.twig',['form' => $form->createView(), 'modeEdition'=> $modeEdition]);
    }



/**************************************************************************************************************************/


    /**
     * @Route("/Fiche/Modifier-{id}-{Letter}", name="Fiche.modifier", requirements={"id"="\d*", "letter"="[A-Z]{1}"})
     */
    public function Edit(Fiche $fiche, $Letter, Request $request)
    {   
        $modeEdition = true;
        $form = $this->createForm(FicheType::class, $fiche);
        $form->handleRequest($request); // Enregistre le valeur de POST dans un objet contact.
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid())
        {
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



/**************************************************************************************************************************/


    /**
     * @Route("/Fiche/Supprimer-{id}-{Letter}", name="Fiche.supprimer", requirements={"id"="\d*", "letter"="[A-Z]{1}"})
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



/**************************************************************************************************************************/


    /**
     * @Route("/Fiche/Invitation", name="Fiche.Invitation")
     */
    public function Invitation(Request $request, ValidatorInterface $validator)
    {  
        $form = $this->createForm(InvitationType::class);
        $form->handleRequest($request); // Enregistre le valeur de POST dans un objet contact.
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid())
        {
            // On récupére les données du formulaire et on initie Doctrine.
            $credentials = $form->getData();
            $em = $this->GetDoctrine()->GetManager(); 

            // On parcour le tableau de Destinataires pour créer les invitations et le enregistrer. 
            foreach ($credentials['destinataires'] as $email) 
            {

                $invitation = (new Invitation())
                ->setInvite($email)
                ->setMessage($credentials['message'])
                ->setUser($this->getUser());

                // Validation de l'objet Invitation
                $errors = $validator->validate($invitation);

                if ( count($errors) > 0 )
                { 
                  foreach ($errors as $error) 
                  { $this->addFlash('warning', $error->getMessage() ); }
              }
                else
                { $em->persist($invitation); }

            }

            $em->flush();

            if ( count($errors) != count($credentials['destinataires']) )
            { $this->addFlash('success',"L'invitation est bien envoyé à vos destinataires."); }
        }

        return $this->render('fiche/invitation.html.twig', ['form' => $form->createView()]);
    }



/**************************************************************************************************************************/


    /**
     * @Route("/Invitation-Stop-{clefPublic}", name="Fiche.Invitation.Stop")
     */
    public function Invitation_Stop($clefPublic, Request $request)
    {
        // On récupére l'invitation
        $invitation = $this->GetDoctrine()->getRepository(Invitation::class)->findOneBy(['clefpublic' => $clefPublic]);

          // Controle que l'utilisateur n'est pas null.
          if ( $invitation === null )
          { 
            $this->addFlash('warning', "Impossible d'identifier le compte de l'utilisateur.");
            return $this->redirectToRoute('login');
          }
       
        // Informe l'objet invitation que la fiche est remplie.
        $invitation->setStop(true);
        // Enregistre le contact en base de données.
        $em = $this->GetDoctrine()->GetManager(); 
        $em->persist($invitation);
        $em->flush();

        $this->addFlash('success', "Vous ne recevrez plus d'invitation du site Carnet-Adresse.fr.");

        return $this->redirectToRoute('login');
    }
/**************************************************************************************************************************/





    /**
     * @Route("/Invitation-{clefPublic}", name="Fiche.Invitation.Add")
     */
    public function Invitation_Add($clefPublic, Request $request)
    {
        $fiche = new Fiche(); // initiale un nouvelle fiche.
        $form = $this->createForm(FicheType::class, $fiche);
        $form->handleRequest($request); // Enregistre le valeur de POST dans un objet contact.
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid())
        {
            
            
            // On récupére l'invitation
            $invitation = $this->GetDoctrine()->getRepository(Invitation::class)->findOneBy(['clefpublic' => $clefPublic]);
            // On récupére l'utilisateur
            $user = $invitation->getUser();
            

            // Controle que l'utilisateur n'est pas null.
            if ( $user === null || $invitation === null )
            { 
              $this->addFlash('warning', "Impossible d'identifier le compte de l'utilisateur.");
              return $this->render('fiche/invitation_add.html.twig', ['form' => $form->createView()]);  
            }

            // Attribut l'utilisateur propriétaire de la fiche.
            $fiche->setUser($user);
            // Informe l'objet invitation que la fiche est remplie.
            $invitation->setComfirmer(true);
            // Enregistre le contact en base de données.
            $em = $this->GetDoctrine()->GetManager(); 
            $em->persist($fiche);
            $em->persist($invitation);
            $em->flush();
            
            $this->addFlash('success','Votre fiche est enregistrée.<br> '. $user->getNomComplet() . ' vous remercie.');

            return $this->render('fiche/invitation_add.html.twig');

        }

        // Retourne la vue des contacts.
        return $this->render('fiche/invitation_add.html.twig', ['form' => $form->createView()]);
    }

}
