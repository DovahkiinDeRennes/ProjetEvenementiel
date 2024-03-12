<?php

namespace App\Controller\AdminController;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Form\CreatePlaceType;
use App\Form\CreateSiteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: 'admin/', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route(path: 'place', name: 'place')]
    public function getPlaces(EntityManagerInterface $entityManager){
        $places = $entityManager->getRepository(Lieu::class)->findAll();
        return $this->render('Admin/places.html.twig',compact('places'));
    }

    #[Route(path: 'createPlace', name: 'createPlace')]
    public function createPlaces(EntityManagerInterface $entityManager, Request $request){

    $places = new Lieu();

    $form = $this->createForm(CreatePlaceType::class, $places);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($places);
        $entityManager->flush();
        return $this->redirectToRoute('admin_place');
    }
        return $this->render('Admin/createPlace.html.twig',['form' => $form->createView()]);
    }

#[Route(path : 'site', name:'site')]
public function getSites(EntityManagerInterface $entityManager){

    $sites = $entityManager->getRepository(Site::class)->findAll();

        return $this->render('Admin/sites.html.twig',compact('sites'));

}

    #[Route(path: 'createSite', name: 'createSite')]
    public function createSites(EntityManagerInterface $entityManager, Request $request){

        $sites = new Site();

        $form = $this->createForm(CreateSiteType::class, $sites);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sites);
            $entityManager->flush();
            return $this->redirectToRoute('admin_site');
        }
        return $this->render('Admin/createSite.html.twig',['form' => $form->createView()]);
    }
}
