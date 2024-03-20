<?php

namespace App\Controller\AjaxController;



use App\Entity\Lieu;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Contracts\HttpClient\HttpClientInterface;


#[Route('/lieu')]
class AjaxController extends AbstractController

{
    #[Route('/info/{id}', name: 'lieu_info')]
    public function getInfoLieu(Lieu $lieu): JsonResponse
    {
        $information = [
            'ville' => $lieu->getVilleId()-> getNom(),
            'rue' => $lieu->getRue(),
            'codepostal' => $lieu->getVilleId()->getCodePostal(),
            'latitude' => $lieu->getLatitude(),
            'longitude' => $lieu->getLongitude(),
        ];
        return new JsonResponse($information);
    }


}