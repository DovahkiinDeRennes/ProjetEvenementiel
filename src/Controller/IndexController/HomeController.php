<?php

namespace App\Controller\IndexController;

use App\Entity\Sortie;
use App\Form\SearchSortieType;
use App\Entity\User;
use App\Repository\SortieRepository;
use App\Service\MiseAjourSortie;
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


    public function __construct(MiseAjourSortie $MiseAjourSortie)
    {
        $this->MiseAjourSortie = $MiseAjourSortie;
    }

    #[Route(path: '', name: 'home', methods: ['GET', 'POST'])]
    public function home(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, Security $security, MiseAjourSortie $MiseAjourSortie): Response
    {
        $this->MiseAjourSortie->updateSortieState();
        // La page s'affiche si on est un utilisateur connecté
        if ($security->isGranted('IS_AUTHENTICATED')) {
            $userId = $this->getUser()->getId();
            $user = $entityManager->getRepository(User::class)->find($userId);



            if (!$security->isGranted('ROLE_BANNED')) {
                $maxPerPage = 4;

                if (($page = $request->query->get('p', 1)) < 1) {
                    return $this->redirectToRoute('home_home');
                }
//                $count2 = $entityManager->getRepository(Wish::class)->count(['' => true]);
                $count2 = $entityManager->getRepository(Sortie::class)->count();
                $sorties = $sortieRepository->findAllEvents($maxPerPage,$page);

                // Vérification de la page
                if ($page !== 1 && empty($sorties)) {
                    return $this->redirectToRoute('home_home');
                }
                // Récupération de toutes les sorties
                $sorties = $entityManager->getRepository(Sortie::class)->findAllEvents($page, $maxPerPage);
                // Initialisation de la variable pour stocker les sorties à afficher
                $sortiesToDisplay = [];

                // Vérifier le rôle de l'utilisateur
                $isAdmin = $security->isGranted('ROLE_ADMIN');

                foreach ($sorties as $sortie) {
                    // Vérifier si l'utilisateur est l'organisateur de la sortie ou s'il est administrateur
                    if ($sortie->getOrganisateur() === $user || $isAdmin) {
                        // Ajouter la sortie à afficher
                        $sortiesToDisplay[] = $sortie;
                    } elseif ($sortie->getEtatId()->getId() != 3) {
                        $sortiesToDisplay[] = $sortie;
                    }
                }

                // Création du formulaire de recherche
                $searchForm = $this->createForm(SearchSortieType::class);
                $searchForm->handleRequest($request);

                // POST du formulaire de recherche
                if ($searchForm->isSubmitted()) {
                    $formData = $searchForm->getData();
                    $sortiesToDisplay = $sortieRepository->filterEvent($formData, $userId);
                }

                // Compte inscrits/sortie
                $count = [];
                foreach ($sortiesToDisplay as $sortie) {

                    $count[$sortie->getId()] = $sortie->getUsers()->count();
                }

                // Inscrit ? ('x')
                $isRegistered = [];
                $inscrit = '';
                foreach ($sortiesToDisplay as $sortie) {
                    $isRegistered[$sortie->getId()] = $sortie->getUsers()->contains($user);
                    if ($isRegistered[$sortie->getId()]) {
                        $inscrit= 'x';
                    }
                }

                return $this->render('home/home.html.twig', [
                    'sorties' => $sortiesToDisplay,
                    'count' => $count,
                    'isRegistered' => $isRegistered,
                    'inscrit' => $inscrit,
                    'searchForm' => $searchForm->createView(),
                    'user' => $user,
                    'maxPerPage' => $maxPerPage,
                    'count2' => $count2,
                ]);
            }
            else {
                    return $this->render('user/actif.html.twig');
            }
        } else {
            // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }
    }
    // TEST POUR RECUP LES FICHIERS
}




