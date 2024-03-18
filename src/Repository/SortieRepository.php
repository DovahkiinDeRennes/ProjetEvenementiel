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

        public function findAllEvents(): array
        {
            $queryBuilder = $this->createQueryBuilder('event');


            $queryBuilder->leftJoin('event.users', 'user')
                ->addSelect('user')
                ->addOrderBy('event.dateLimiteInscription', 'DESC');

            //$queryBuilder->leftJoin('event.organisateur', 'organisateur')
                //->addSelect('organisateur')
                //->addOrderBy('event.dateLimiteInscription', 'DESC');


            $query = $queryBuilder->getQuery();

            return  $query->getResult();

       }

       public function filterEvent(array $formData, $userId): array
       {
           $queryBuilder = $this->createQueryBuilder('event')
                ->join('event.users', 'users')
                ->join('event.etatId', 'etat')
                ->join('event.lieuId', 'site')
           ->addSelect('event');



           if(!empty($formData['nom'])){
               $queryBuilder->where('event.nom LIKE :nom')
                   ->setParameter('nom', '%'.$formData['nom'].'%');
           }

           if(!empty($formData['site'])){
               $queryBuilder->andWhere('event.lieuId LIKE :site')
                   ->setParameter('site', $formData['site']);
           }

           if(!empty($formData['date_one'])){
               $queryBuilder->andWhere('event.dateHeureDebut >= :dateHeureDebut')
               ->setParameter('dateHeureDebut', $formData['date_one'] );
           }

           if(!empty($formData['date_two'])){
           $queryBuilder->andWhere('event.dateLimiteInscription <= :dateLimiteInscription')
               ->setParameter('dateLimiteInscription', $formData['date_two'] );
           }

           //filtre si personne connectée = id  de l'organisateur
           if(!empty($formData['sorties_orga'])){
               //dd($formData['sorties_orga']);
               //dd($queryBuilder->getQuery()->getResult());
               $queryBuilder->andWhere('event.organisateur = :userId')
                   ->setParameter('userId', $userId);
               //dd($queryBuilder->getQuery()->getSQL());
           }

           //filtre si on trouve dans les id des participants l'id de la personne connectée
           if(!empty($formData['sorties_inscrit'])&& empty($formData['sorties_nonInscrit'])){
               $queryBuilder->andWhere('users.id IN (:userId)')
                   ->setParameter('userId', $userId);
           }

           //filtre si on ne trouve PAS dans les id des participants l'id de la personne connectée
           if(!empty($formData['sorties_nonInscrit'])&& empty($formData['sorties_inscrit'])){
               $queryBuilder->andWhere('users.id NOT IN(:userId)')
                   ->setParameter('userId', $userId);
           }

           //filtre si etat de la sortie = passee
           if(!empty($formData['sorties_passees'])){
               $queryBuilder->andWhere("event.etat = 'Passée' ");
           }




           $query = $queryBuilder->getQuery();

           return $query->getResult();
       }


    //    /**
    //     * @return Sortie[] Returns an array of Sortie objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Sortie
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
