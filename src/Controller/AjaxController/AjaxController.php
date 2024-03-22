<?php

namespace App\Controller\AjaxController;



use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\CreatePlaceType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


#[Route('/lieu')]
class AjaxController extends AbstractController

{
    #[Route('/info/{id}', name: 'lieu_info')]
    public function getInfoLieu(Lieu $lieu, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $lieuId = $request->get('lieuID');

        if ($lieuId) {
            $lieu = $entityManager->getRepository(Lieu::class)->find($lieuId);
        }

        $information = [
            'ville' => $lieu->getVilleId()->getNom(),
            'rue' => $lieu->getRue(),
            'codepostal' => $lieu->getVilleId()->getCodePostal(),
            'latitude' => $lieu->getLatitude(),
            'longitude' => $lieu->getLongitude(),
        ];
        return new JsonResponse($information);
    }

    #[Route('/formLieu', name: 'form_Lieu')]
    public function getFormLieu(Request $request, EntityManagerInterface $entityManager)
    {
        $place = new Lieu();
        $form = $this->createForm(CreatePlaceType::class, $place);
        $villes = $entityManager->getRepository(Ville::class)->findAll();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement des données du formulaire et enregistrement dans la base de données
            $entityManager->persist($place);
            $entityManager->flush();

            // Répondre avec une confirmation de succès ou toute autre donnée nécessaire
            return new JsonResponse(['success' => true, 'message' => 'Le lieu a été créé avec succès']);
        }

        // Si le formulaire n'est pas soumis ou n'est pas valide, renvoyer le formulaire en HTML
        $htmlForm = $this->renderView('Sortie/AjaxCreatePlace.html.twig', [
            'form' => $form->createView(),
            'villes' => $villes
        ]);

        return new JsonResponse($htmlForm);
    }

    #[Route('/submitLieuForm', name: 'submitLieuForm', methods: ['POST'])]
    public function submitLieuForm(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): JsonResponse
    {
        $formData = json_decode($request->getContent(), true);
        $logger->info('Données récupérées du formulaire: ' . json_encode($formData));

        // Vérifier si toutes les données requises sont présentes
        $requiredFields = ['nom', 'rue', 'latitude', 'longitude'];
        foreach ($requiredFields as $field) {
            if (!isset($formData[$field])) {
                return new JsonResponse(['success' => false, 'message' => "Le champ '$field' est manquant"]);
            }
        }

        // Créer une nouvelle instance de Lieu et hydrater ses propriétés avec les données soumises
        $place = new Lieu();
        $place->setNom($formData['nom']);
        $place->setRue($formData['rue']);
        $place->setLatitude($formData['latitude']);
        $place->setLongitude($formData['longitude']);

        // Récupérer la ville associée
        $ville = $entityManager->getRepository(Ville::class)->find($formData['villeId']);
        if (!$ville) {
            return new JsonResponse(['success' => false, 'message' => 'La ville spécifiée n\'existe pas']);
        }
        $place->setVilleId($ville);

        // Enregistrer dans la base de données
        $entityManager->persist($place);
        $entityManager->flush();

        // Répondre avec une confirmation de succès ou toute autre donnée nécessaire
        return new JsonResponse(['success' => true, 'message' => 'Le lieu a été créé avec succès']);
    }



}