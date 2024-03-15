<?php

namespace App\Controller\AdminController;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Form\CreatePlaceType;
use App\Form\CreateSiteType;
use App\Repository\LieuRepository;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: 'admin/', name: 'admin_')]
class AdminController extends AbstractController
{
    #[Route(path: 'place', name: 'place')]
    public function getPlaces(LieuRepository $lieuRepository){
        // fonction créer dans le lieuRepository pour récuprer tous lieux
        $places = $lieuRepository->findAllLieuVille();
        return $this->render('Admin/places.html.twig',compact('places'));
    }

    #[Route(path: 'createPlace', name: 'createPlace')]
    public function createPlaces(EntityManagerInterface $entityManager, Request $request){

        // on crée un nouveau lieu
    $places = new Lieu();

    // on crée le formulaire pour ce lieu
    $form = $this->createForm(CreatePlaceType::class, $places);
    $form->handleRequest($request);
    // ta capté je pense ?? si tu submits et que le formulaire est valide alors t'envoie dans la base de données
    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($places);
        $entityManager->flush();
        return $this->redirectToRoute('admin_place');
    }
        return $this->render('Admin/createPlace.html.twig',['form' => $form->createView()]);
    }

    #[Route(path: 'updatePlace/{id}', name: 'updatePlace')]
    public function updatePlace(EntityManagerInterface $entityManager, Request $request, int $id){

        // on récupere le lieu avec l'id
      $place = $entityManager->getRepository(Lieu::class)->find($id);
        // on crée le formulaire pour ce lieu
        $form = $this->createForm(CreatePlaceType::class, $place);
        $form->handleRequest($request);
        // si tu submits et que le formulaire est valide alors t'envoie dans la base de données
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($place);
            $entityManager->flush();
            return $this->redirectToRoute('admin_place');
        }
        return $this->render('Admin/createPlace.html.twig',['form' => $form->createView()]);
    }
    #[Route(path: 'deletePlace/{id}', name: 'deletePlace')]
    public function deletePlace(EntityManagerInterface $entityManager,  int $id)
    {
        // on récupere le lieu avec l'id
        $place = $entityManager->getRepository(Lieu::class)->find($id);
        // et on le delete avec remove()
        $entityManager->remove($place);
        $entityManager->flush();
        return $this->redirectToRoute('admin_place');

    }

#[Route(path : 'site', name:'site')]
public function getSites(EntityManagerInterface $entityManager,SiteRepository $siteRepository){
// fonction créer dans le SiteRepository pour récuprer tous les sites
    $sites = $siteRepository->findAllSiteUser();

        return $this->render('Admin/sites.html.twig',compact('sites'));

}

    #[Route(path: 'createSite', name: 'createSite')]
    public function createSites(EntityManagerInterface $entityManager, Request $request){
    // on crée un nouveau site
        $sites = new Site();
    // on crée le formulaire pour ce site
        $form = $this->createForm(CreateSiteType::class, $sites);
        $form->handleRequest($request);
        // si tu submits et que le formulaire est valide alors t'envoie dans la base de données
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sites);
            $entityManager->flush();
            return $this->redirectToRoute('admin_site');
        }
        return $this->render('Admin/createSite.html.twig',['form' => $form->createView()]);
    }

    #[Route(path: 'updateSite/{id}', name: 'updateSite')]
    public function updateSite(EntityManagerInterface $entityManager, Request $request, int $id){
    //on recupere le site avec l'id
        $site = $entityManager->getRepository(Site::class)->find($id);
// on crée le formulaire pour ce site et on l'hydrate avec $site
        $form = $this->createForm(CreateSiteType::class, $site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();
            return $this->redirectToRoute('admin_site');
        }
        return $this->render('Admin/createSite.html.twig',['form' => $form->createView()]);
    }

    #[Route(path: 'deleteSite/{id}', name: 'deleteSite')]
    public function deleteSite(EntityManagerInterface $entityManager,  int $id)
    {
        //on recupere le site avec l'id
    $site = $entityManager->getRepository(Site::class)->find($id);
    //et on le delete avec remove()
    $entityManager->remove($site);
    $entityManager->flush();
    return $this->redirectToRoute('admin_site');

    }


// TEST POUR RECUP LES FICHIERS
}
