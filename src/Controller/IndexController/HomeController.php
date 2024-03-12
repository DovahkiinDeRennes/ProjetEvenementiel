<?php

namespace App\Controller\IndexController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route(name: 'home_')]
class HomeController extends  AbstractController
{
    #[Route(path: '',name: 'home',methods:['GET'])]
    public function home(): Response
    {

        return $this->render('home/home.html.twig');
    }
}