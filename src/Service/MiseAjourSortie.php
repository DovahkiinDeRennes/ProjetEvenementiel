<?php

namespace App\Service;

use App\Entity\Etat;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Sortie;
use App\Repository\EtatRepository;

class MiseAjourSortie
{
    private $entityManager;
    private $etatRepository;

    public function __construct(EntityManagerInterface $entityManager, EtatRepository $etatRepository)
    {
        $this->entityManager = $entityManager;
        $this->etatRepository = $etatRepository;
    }

    public function updateSortieState()
    {
        // Récupérer toutes les sorties
        $sorties = $this->entityManager->getRepository(Sortie::class)->findAll();

        // Date actuelle
        $dateActuelle = new \DateTime('now', new \DateTimeZone('Europe/Paris'));


        // Parcourir toutes les sorties
        foreach ($sorties as $sortie) {
            // Récupérer la date de début de la sortie actuelle
            $dateDebut = $sortie->getDateHeureDebut();
            $interval = new \DateInterval('P30D');

            $dateFin = clone $dateDebut;
            $dateFin->add($interval);
            // Vérifier si la date de début de la sortie est passée
            if ($dateActuelle > $dateDebut) {

                // Changer l'état de la sortie en fonction de vos règles métier
                $etatId = 5;
                $etat = $this->etatRepository->find($etatId);
                $sortie->setEtatId($etat);
            }
                if ($dateActuelle >= $dateFin) {
                $etatId = 6; // ID de l'état correspondant
                $etat = $this->etatRepository->find($etatId);
                $sortie->setEtatId($etat);
            }

            // Enregistrer les modifications dans la base de données
            $this->entityManager->flush();
        }
    }
}