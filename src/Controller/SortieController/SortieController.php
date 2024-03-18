<?php

namespace App\Controller\SortieController;



use App\Entity\Etat;
use App\Entity\Site;
use App\Entity\Sortie;
use App\Entity\User;
use App\Form\CancelSortieType;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('sortie/', name: 'sortie_')]
class SortieController extends AbstractController
{

    //Création d'un controller pour créer une sortie
    #[Route('create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em, Security $security): Response
    {
        // Je récup l'utilisateur connecté AVEC LA CLASSE SECURITY!!!!!!
        $user = $security->getUser();
        // Creer une nouvelle sortie
        $sortie = new Sortie();
        // Creer le formulaire
        $sortieForm = $this->createForm(SortieType::class, $sortie);
        // hydrater le formulaire
        $sortieForm->handleRequest($request);

        // si le formulaire est envoyé
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if ($request->request->has('draft')) {
                $etatId = $em->getRepository(Etat::class)->find(3); // ID de l'état "Brouillon"
            } elseif ($request->request->has('publish')) {
                $etatId = $em->getRepository(Etat::class)->find(4); // ID de l'état "Publié"
            }
            //Définir l'utilisateur organisateur AVEC LA CLASSE SECURITY UP UP UP UP UP (retrouve le $user pour comprendre, ligne 27)
            $sortie->setOrganisateur($user);
            $site = $em->getRepository(Site::class)->find($user);
            $sortie->setSite($site);



               // Je prend le 1er etat de sortie (dans la base de donnée)

               // Je set l'etat avec la fonction setEtatId
               $sortie->setEtatId($etatId);


               $em->persist($sortie);

               $em->flush();

               // message d'alerte
               $this->addFlash('success', 'La sortie a bien été enregistrée.');
               return $this->redirectToRoute('home_home');

        }



        // Pour acceder à la page
        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm
        ]);
    }

    #[Route('newEtat/{id}', name: 'newEtat', methods: ['GET', 'POST'])]
    public function newEtat(Request $request, EntityManagerInterface $em,int $id): Response
    {
        $sortie = $em->getRepository(Sortie::class)->find($id);

        $userId = $this->getUser()->getId();

        $organisateur = $sortie->getOrganisateur();

        if($organisateur->getId() == $userId){
            if( $sortie->getEtatId()->getId() == 3){
                $etatId = $em->getRepository(Etat::class)->find(4); // ID de l'état "Publié"

                $sortie->setEtatId($etatId);
                $em->persist($sortie);

                $em->flush();
            }
        }
        // Récupérer la sortie avec son id



        return $this->redirectToRoute('home_home');
    }




    #[Route('suscribe/{id}', name: 'suscribe', methods: ['GET', 'POST'])]
    public function suscribe(Request $request, EntityManagerInterface $em,int $id): Response
    {
        // Récupérer l'utilisateur connecte (oui, c'est une nouvelle façon de faire  tu va faire quoi?)
        $userId = $this->getUser()->getId();


        // Récupérer la sortie avec son id
        $sortie = $em->getRepository(Sortie::class)->find($id);
        // Récupère l'utilisateur connecte avec son id
        $user = $em->getRepository(User::class)->find($userId);

        // Ajouter la sortie a l'utilisateur (c'est une méthode de quoicoubeh? nan je rigole de l' Entity User)
        // On verifie l'etat + la date de cloture

     //  $prout = new \DateTimeImmutable('now' , new \DateTimeZone('Europe/Paris')) = null, calendar = "gregorian", locale = null);
        $dateActuelle = new \DateTime('now' , new \DateTimeZone('Europe/Paris'));
        $dateDebut = $sortie->getDateHeureDebut();

        if ($dateActuelle < $dateDebut && $sortie->getEtatId()->getId() == 4) {
            if (!$user->getSorties()) { // Vérifie si l'utilisateur n'a pas déjà des sorties
                $this->addFlash('error', 'Vous êtes déjà inscrit à une sortie.');
            } else {
                $user->addSorty($sortie);
                $em->flush();
                $this->addFlash('success', 'Vous êtes inscrit à cette sortie.');

            }
        } else {
            $this->addFlash('error', 'Les inscriptions sont terminées pour cette sortie.');
        }







        return $this->redirectToRoute('home_home');
    }

    #[Route('unsuscribe/{id}', name: 'unsuscribe', methods: ['GET', 'POST'])]
    public function unsuscribe(Request $request, EntityManagerInterface $em,int $id): Response
    {

        // Récupérer l'utilisateur connecte, oui c'est exactement la mème chose qu'avant
        $userId = $this->getUser()->getId();
        // Récupérer la sortie avec son id
        $sortie = $em->getRepository(Sortie::class)->find($id);
        // Récupère l'utilisateur connecte avec son id
        $user = $em->getRepository(User::class)->find($userId);

        // devine ça fait quoi ça???? allez 1, 2, 3, flop.... la fonction removeSorty dans ENTITY User (le contraire de addSorty)
        // On verifie l'etat + la date de cloture

                if ($user->getSorties()) {
                    $user->removeSorty($sortie);
                    $em->flush();
        }
        return $this->redirectToRoute('home_home');
    }

    #[Route('update/{id}', name: 'update', methods: ['GET', 'POST'])]
    public function update(Request $request, EntityManagerInterface $em,int $id): Response
    {
        // Récupérer la sortie avec son id
        $sortie = $em->getRepository(Sortie::class)->find($id);
        // Création du formulaire avec la sortie recupérée
        $form = $this->createForm(SortieType::class, $sortie);

        // hydrater le formulaire
        $form->handleRequest($request);
        // si le formulaire est envoyé et valide
        if ($form->isSubmitted() && $form->isValid()) {


            $em->persist($sortie);

            $em->flush();
            return $this->redirectToRoute('home_home');
        }
        return $this->render('sortie/update.html.twig', ['form' => $form->createView(), 'id' => $id ]);
    }

    #[Route('detail/{id}', name: 'detail', methods: ['GET', 'POST'])]
    public function detail(int $id, EntityManagerInterface $em): Response
    {
        // Récupère la sortie avec son id sans le formulaire vue qu'on veut simplement le détail aucune modification prévu
        $sortie = $em->getRepository(Sortie::class)->find($id);

        return $this->render('/sortie/detail.html.twig', [
            'sortie'=>$sortie,
        ]);
    }

    #[Route('cancel/{id}', name: 'cancel', methods: ['GET', 'POST'])]
    public function cancel(Request $request, EntityManagerInterface $em, int $id): Response
    {
        // Récupérer la sortie avec son id
        $sorties = $em->getRepository(Sortie::class)->find($id);
        // Création du formulaire avec la sortie recupérée
        $form = $this->createForm(CancelSortieType::class, $sorties);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            // si submit alors on set un état qu'on récupère avec l'id
            $etatId = $em->getRepository(Etat::class)->find(2);
            $sorties->setEtatId($etatId);
            $em->persist($sorties);
            $em->flush();

            return $this->redirectToRoute('home_home');
        }
        return $this->render('sortie/cancel.html.twig', [
            'form' => $form ->createView(),
            'sorties' => $sorties
        ]);
    }

}