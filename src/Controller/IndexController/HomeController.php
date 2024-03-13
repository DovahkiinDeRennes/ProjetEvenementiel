<?php

namespace App\Controller\IndexController;

use App\Entity\Sortie;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route(name: 'home_')]
class HomeController extends  AbstractController
{
    #[Route(path: '',name: 'home',methods:['GET'])]
    public function home(EntityManagerInterface $entityManager, SortieRepository $sortieRepository): Response
    {
        $sorties = $sortieRepository->findAllEvents();
        return $this->render('home/home.html.twig', [
            'sorties'=> $sorties
        ]);
    }
}