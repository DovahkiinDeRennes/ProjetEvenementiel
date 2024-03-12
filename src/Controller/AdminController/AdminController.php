<?php

namespace App\Controller\AdminController;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: 'admin/', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route(path: 'place', name: 'place')]
    public function getPlaces(){
        return $this->render('Admin/places.html.twig');
    }

#[Route(path : 'site', name:'site')]
public function getSites(){
        return $this->render('Admin/sites.html.twig');

}
}
