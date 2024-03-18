<?php

namespace App\Controller\IndexController;

use App\Entity\Sortie;
use App\Form\SearchSortieType;
use App\Entity\User;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;



#[Route(name: 'home_')]
class HomeController extends  AbstractController
{
    #[Route(path: '', name: 'home', methods: ['GET', 'POST'])]
    public function home(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, Security $security): Response
    {
        if ($security->isGranted('IS_AUTHENTICATED')) {
            $userId = $this->getUser()->getId();
            $user = $entityManager->getRepository(User::class)->find($userId);

            // La page s'affiche si on est un utilisateur connecté
            if ($user && !$user->getActif()) {
                // Récupération de toutes les sorties
                $sorties = $sortieRepository->findAllEvents();

                // Initialisation de la variable pour stocker les sorties à afficher
                $sortiesToDisplay = $sorties;

                // Création du formulaire de recherche
                $searchForm = $this->createForm(SearchSortieType::class);
                $searchForm->handleRequest($request);

                // POST du formulaire de recherche
                if ($searchForm->isSubmitted()) {
                    $formData = $searchForm->getData();
                    //dd($formData);
                    //dd($userId);
                    $sortiesToDisplay = $sortieRepository->filterEvent($formData, $userId);
                }

                // Compte inscrits/sortie
                $count = [];
                foreach ($sortiesToDisplay as $sortie) {
                    $count[$sortie->getId()] = $sortie->getUsers()->count();
                }

                return $this->render('home/home.html.twig', [
                    'sorties' => $sortiesToDisplay,
                    'count' => $count,
                    'searchForm' => $searchForm->createView()
                ]);
            } else {
                return $this->render('user/actif.html.twig');
            }
        }else{
            // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }
    }
    // TEST POUR RECUP LES FICHIERS
}




