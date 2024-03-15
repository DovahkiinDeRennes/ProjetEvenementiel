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
    #[Route(path: '',name: 'home',methods:['GET', 'POST'])]
    public function home(Request $request, EntityManagerInterface $entityManager, SortieRepository $sortieRepository, Security $security): Response
    {
        //  $searchForm = $this->createForm(SearchSortieType::class);
        //    $searchForm->handleRequest($request);



        // if($searchForm->isSubmitted()){
        //   $formData = $searchForm->getData();
        // $sorties = $sortieRepository->filterEvent($formData);

        // return $this->redirectToRoute('home_home', [
        //   'sorties' => $sorties
        //]);
        //}


        if ($security->isGranted('IS_AUTHENTICATED')) {
            // Obtient l'ID de l'utilisateur
            $userId = $this->getUser()->getId();
            $user = $entityManager->getRepository(User::class)->find($userId);

            //on vérifie si l'utilisateur est actif si 0 ok si 1 on redirige
            if ($user && !$user->getActif()) {
                $sorties = $sortieRepository->findAllEvents();

                // on compte le nombre d'utilisateur par sortie
                $count = [];
                foreach ($sorties as $sortie) {
                    $count[$sortie->getId()] = $sortie->getUsers()->count();
                }

                return $this->render('home/home.html.twig', [
                    'sorties'=> $sorties,
                    'count' => $count,
                    // 'searchForm'=> $searchForm
                ]);
            } else {
                return $this->render('user/actif.html.twig');
            }
        } else {
            // Redirection vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }
    }
    // TEST POUR RECUP LES FICHIERS
}