<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\DateType;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);

    }

        public function findAllEvents(int $page, int$maxPerPage): array
        {
            $queryBuilder = $this->createQueryBuilder('event');


            $queryBuilder->leftJoin('event.users', 'user')
                ->addSelect('user')
                ->addOrderBy('event.dateLimiteInscription', 'DESC');

            //$queryBuilder->leftJoin('event.organisateur', 'organisateur')
                //->addSelect('organisateur')
                //->addOrderBy('event.dateLimiteInscription', 'DESC');


            $query = $queryBuilder->getQuery();

            $query->setMaxResults($maxPerPage);
            $query->setFirstResult($maxPerPage * ($page - 1));

            return  $query->getResult();

       }

       public function filterEvent(array $formData, $userId): array
       {
           $queryBuilder = $this->createQueryBuilder('event')
                ->leftjoin('event.users', 'users')
                ->leftjoin('event.etatId', 'etat')
                ->leftJoin('event.site', 'site')
           ->addSelect('event');



           if(!empty($formData['nom'])){
               $queryBuilder->where('event.nom LIKE :nom')
                   ->setParameter('nom', '%'.$formData['nom'].'%');
           }

           if(!empty($formData['site'])){
               $queryBuilder->andWhere('event.site = :site')
                   ->setParameter('site', $formData['site']);
           }

           if(!empty($formData['date_one'])){
               $dateDebut = $formData['date_one']->setTime(0,0,0);
               $queryBuilder->andWhere('event.dateHeureDebut >= :dateHeureDebut')
               ->setParameter('dateHeureDebut', $dateDebut  );
           }

           if(!empty($formData['date_two'])){
               $dateFin = $formData['date_two']->setTime(23,59,59);
               $queryBuilder->andWhere('event.dateLimiteInscription <= :dateLimiteInscription')
               ->setParameter('dateLimiteInscription', $dateFin );
           }

           //filtre si personne connectée = id  de l'organisateur
           if(!empty($formData['sorties_orga'])){
               $queryBuilder->andWhere('event.organisateur = :userId')
                   ->setParameter('userId', $userId);
           }

           //filtre si on trouve dans les id des participants l'id de la personne connectée
           if(!empty($formData['sorties_inscrit'])&& empty($formData['sorties_nonInscrit'])){
               $queryBuilder->andWhere('users.id IN (:userId)')
                   ->setParameter('userId', $userId);
           }

           //filtre si on ne trouve PAS dans les id des participants l'id de la personne connectée OU s'il n'y a aucun participant
           if(!empty($formData['sorties_nonInscrit'])&& empty($formData['sorties_inscrit'])){
               $queryBuilder->andWhere('users.id NOT IN (:userId) OR users.id IS NULL')
                   ->setParameter('userId', $userId);
           }

           //filtre si etat de la sortie = passee
           if(!empty($formData['sorties_passees'])){
               $queryBuilder->andWhere('etat = 5 ');
           }

           $queryBuilder->addOrderBy('event.dateHeureDebut', 'ASC');

           $query = $queryBuilder->getQuery();

           return $query->getResult();
       }


}
