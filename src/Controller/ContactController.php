<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\DemandeByMail;
use App\Form\ContactType;
use App\Form\DemandeByMailType;
use App\Repository\ContactRepository;
use App\Service\Invitation;
use App\Service\MailJet;
use App\Service\Message;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{

    private $em;  // Pour enregistrer/modifier les données. ObjectManager $em
    private $repository; // Pour récuperer les données.
    private $keyPublic; // Clef publique permetant de récuperer les données uniquement lié à cette clef.

    public function __construct(ContactRepository $repository )
    {
        $this->em = "";
        $this->repository = $repository;
        $this->keyPublic = 0;
    }


	/**
     * Affiche les contacts en base de données.
     * @Route("/Contact/Show", name="Contact.Show")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Show(PaginatorInterface $paginator, Request $request)
    {

        $keyPrivate = $this->getUser()->getKeyPrivate();

        $indexLetter = "A";
        if ( $request->query->get('letter') != null )
        { $indexLetter = $request->query->get('letter'); }

        $lesContacts = $paginator->paginate(
            $this->repository->findAllByKeyAndLetter($indexLetter, $keyPrivate),
            $request->query->getInt('page',1),5);

        $lesContacts->setCustomParameters([
            'align' => 'center', # center|right (for template: twitter_bootstrap_v4_pagination)
            'size' => 'small', # small|large (for template: twitter_bootstrap_v4_pagination)
            'style' => 'bottom',
            'span_class' => 'whatever',
        ]);

        // Défini si un contact à été modifié.
        $modifSuccess = $request->query->get('modifSuccess');
        // Défini si un contact à été supprimé.
        $DeleteSuccess = $request->query->get('DeleteSuccess');
        // Défini si une invitation à été envoyé.
        $mailEnvoye = $request->query->get('mailEnvoye');

        

        return $this->render('Contact/Show.html.twig', [
            'lesContacts' => $lesContacts,'IndexLetter' => $indexLetter, 'modifSuccess' => $modifSuccess, 'DeleteSuccess' => $DeleteSuccess, 'mailEnvoye' => $mailEnvoye
        ]);
    }


    /**
     * Ajouter un contact en base de données.
     * @Route("/Contact/Add", name="Contact.Add")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Add(Request $request)
    {
        $result = false; // Variable indiquant si un ajout en base de donnée à été fait.
        $contact = new contact(); // initiale un nouveau contact.
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request); // Enregistre le valeur de POST dans un objet contact.
        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->GetDoctrine()->GetManager(); // Attribut un "ObjectManager".
            $em->persist($contact);
            $em->flush();

            $result = true;
            $contact = new contact(); // initiale un nouveau contact.
        }

        // Retourne la page d'ajout de contact. La variable "result" permet de définir si un contact vient d'être ajouté.
        return $this->render('Contact/Add.html.twig',
            ['form' => $form->createView(), 'result' => $result]);
    }


    /**
     * Modifier un contact en base de données.
     * @Route("/Contact/Edit-{id}", name="Contact.Edit", requirements={"id"="\d+"})
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Edit(Contact $contact, Request $request)
    {
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $modifSuccess = true;

            return $this->redirectToRoute('Contact.Show', ['modifSuccess' => $modifSuccess]);
        }

        return $this->render('Contact/Edit.html.twig',
            ['form' => $form->createView()]);

    }


    /**
     * Supprime un contact en base de données.
     * @Route("/Contact/Delete-{id}", name="Contact.Delete", requirements={"id"="\d+"})
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Delete(Contact $contact)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($contact);
        $em->flush();
        $DeleteSuccess = true;

        return $this->redirectToRoute('Contact.Show', ['DeleteSuccess' => $DeleteSuccess]);

    }


    /**
     * Demande d'adresse par mail.
     * @Route("/Contact/Demande", name="Contact.Demande")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function InvitationByMail(Request $request, Invitation $invitation, MailJet $MailJet)
    {
        $MessagePerso = $request->request->get('message');
        $mailEnvoye = false; // Variable indiquant si une invitation est faite.
        $demande = new DemandeByMail(); // Initialise une nouvelle Demande.
        $form = $this->createForm(DemandeByMailType::class, $demande, ['nomComplet' => $this->getUser()->getNomComplet() ]); // Initialise le formulaire de Demande.
        $form->handleRequest($request); // Enregistre les valeur de POST dans l'objet 'nouvelle Demande'.

        // Controle si le formulaire est émis.
        if ($form->isSubmitted() && $form->isValid())
        {
            // Génére l'invitation.
            $invitation->GetInvitation($this->getUser(), $demande, $request->request->get('message'));
            // Variable indiquant si une invitation est faite.
            $mailEnvoye = true;
            // Redirige l'utilisateur sur la liste des contacts
            return $this->redirectToRoute('Contact.Show', ['mailEnvoye' => $mailEnvoye]);

        }
       
        return $this->render('Contact/Demande.html.twig',
            ['form' => $form->createView(), 'MessagePerso' => $MessagePerso]);

    }


    /**
     * Demande d'adresse par mail.
     * @Route("/Invitation-{key}", name="Contact.Invitation")
     * @return Symfony\Component\HttpFoundation\Response;
     */
    public function Invitation(Request $request, Invitation $invitation, MailJet $MailJet)
    {
         return $this->render('Contact/Demande.html.twig',
            ['form' => $form->createView(), 'MessagePerso' => $MessagePerso]); 
    }


}
