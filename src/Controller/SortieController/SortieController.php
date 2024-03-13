<?php

namespace App\Controller\SortieController;



use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $etatId = $em->getRepository(Etat::class)->find(1);
            $sortie->setEtatId($etatId);

            $em->persist($sortie);
            $em->flush();


            $this->addFlash('success', 'La sortie a bien été enregistrée.');
            return $this->redirectToRoute('home_home');
        }

        return $this->render('sortie/create.html.twig', [
            'sortieForm' => $sortieForm
        ]);
    }

    #[Route('update', name: 'update', methods: ['GET', 'POST'])]
    public function update(Request $request, EntityManagerInterface $em): Response
    {

        return $this->redirectToRoute('home_home');
    }

    #[Route('detail', name: 'detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, EntityManagerInterface $em): Response
    {

        return $this->redirectToRoute('home_home');
    }
    #[Route('delete', name: 'delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, EntityManagerInterface $em): Response
    {

        return $this->redirectToRoute('home_home');
    }
}