<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Invitation;
use App\Form\ContactType;
use App\Form\InvitationType;
use App\Repository\ContactRepository;
use App\Service\Email\MailJet;
use App\Service\Email\Message;
use App\Service\InvitationServ;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;


class ContactController extends AbstractController
{


    /**
     * Affiche les contacts en base de données.
     * 
     * @Route("/Contact/Show", name="Contact.Show")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Show(ContactRepository $contactRepository, PaginatorInterface $paginator, Request $request)
    {
        // Recuperation de la Clef privé.
        $keyPrivate = $this->getUser()->getKeyPrivate();

        // Détermine le lettre dont les contacts commenceront.
        $indexLetter = ( $request->query->get('letter') === null ) ? 'A' : $request->query->get('letter');
        $indexLetter = ( $request->query->get('letter') === null && $request->getSession()->get('letter') !== null ) ? $request->getSession()->get('letter') : $indexLetter ;

        // Selectionne les contacts en fonction de la lettre et du paginator.
        $lesContacts = $paginator->paginate($contactRepository->findAllByKeyAndLetter($indexLetter, $keyPrivate),$request->query->getInt('page',1),5);

        // Parametre le paginator.
        $lesContacts->setCustomParameters([
            'align' => 'center', # center|right (for template: twitter_bootstrap_v4_pagination)
            'size' => 'small', # small|large (for template: twitter_bootstrap_v4_pagination)
            'style' => 'bottom',
            'span_class' => 'whatever',
        ]);

        // Enregistrement de l'index lettre
        $request->getSession()->set('letter', $indexLetter);
     
        // Retourne la vue des contacts.
        return $this->render('Contact/Show.html.twig', ['lesContacts' => $lesContacts,'IndexLetter' => $indexLetter]);

    }



/**********************************************************************************************************/



    /**
     * Ajouter un contact en base de données.
     * 
     * @Route("/Contact/Add", name="Contact.Add")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Add(Request $request)
    {
        $contact = new contact(); // initiale un nouveau contact.
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request); // Enregistre le valeur de POST dans un objet contact.
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid())
        {
            // Recuperation de la Clef privé.
            $keyPrivate = $this->getUser()->getKeyPrivate();
            $contact->setPrivateKey($keyPrivate);

            // Enregistre le contact en base de données.
            $em = $this->GetDoctrine()->GetManager(); 
            $em->persist($contact);
            $em->flush();
           
            $contact = new contact(); // initiale un nouveau contact.
            $form = $this->createForm(ContactType::class, $contact); // Défini un formulaire vierge

            // Message flash de success.
            $this->addFlash('contact','Votre contact est enregistré.');

        }

        // Retourne la page d'ajout de contact. La variable "result" permet de définir si un contact vient d'être ajouté.
        return $this->render('Contact/Add.html.twig', ['form' => $form->createView()]);
    }




/**********************************************************************************************************/



    /**
     * Modifier un contact en base de données.
     * 
     * @Route("/Contact/Edit-{id}", name="Contact.Edit", requirements={"id"="\d+"})
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Edit(Contact $contact, Request $request)
    {
        $form = $this->createForm(ContactType::class, $contact); // Défini un formulaire avec les données
        $form->handleRequest($request);

        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid())
        {
            // Enregistre les modifications en base de données.
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            // Message flash de success.
            $this->addFlash('contact','Votre contact '. $contact->getNomComplet() .' est modifié.');

            return $this->redirectToRoute('Contact.Show');
        }

        return $this->render('Contact/Edit.html.twig', ['form' => $form->createView()]);

    }



/**********************************************************************************************************/



    /**
     * Supprime un contact en base de données.
     * 
     * @Route("/Contact/Delete-{id}", name="Contact.Delete", requirements={"id"="\d+"})
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Delete(Contact $contact)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($contact);
        $em->flush();

        // Message flash de success.
        $this->addFlash('contact','Votre contact '. $contact->getNomComplet() .' est supprimé.');

        return $this->redirectToRoute('Contact.Show');

    }



/**********************************************************************************************************/


    /**
     * Demande d'adresse par mail.
     * @Route("/Contact/Invitation", name="Contact.Demande")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function InvitationDemande(Request $request, InvitationServ $invitationServ)
    {
       
        $messageEmail = $this->getUser()->getNomComplet() . " à besoin de vous pour remplir vos informations suivantes : Adresse postale, email, téléphone.";
        $invitation = new Invitation(); // Génére une nouvelle invitation
        $form = $this->createForm(InvitationType::class, $invitation, ['messageEmail'=> $messageEmail]); // Défini un formulaire avec les données
        $form->handleRequest($request);

        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid())
        {
        
            // Enregistre l'invitation
            $em = $this->getDoctrine()->getManager();
            $em->persist($invitation);
            
            // Génére l'invitation.
            $result = $invitationServ->getInvitation($invitation, $this->getUser());
            
            if ( $result['success'] )
            { $em->flush(); }
          
            // Message flash de success.
            $this->addFlash('contact',$result['message']);
        }

        return $this->render('Contact/InvitationDemande.html.twig', ['form' => $form->createView(), 'message' => $messageEmail]);

    }



/**********************************************************************************************************/



    /**
     * Demande d'adresse par mail.
     * @Route("/Invitation-{clefPublic}", name="Contact.Invitation")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Invitation($clefPublic, Request $request)
    {
        return $this->render('Contact/Demande.html.twig',
            ['form' => $form->createView(), 'MessagePerso' => $MessagePerso]); 
    }


}
