<?php

namespace App\Repository;

use App\Entity\Movie;
use App\Entity\Casting;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Casting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Casting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Casting[]    findAll()
 * @method Casting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CastingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Casting::class);
    }

    /**
     * Get Casting + Person for a given movie
     */
    public function findAllJoinedToPersonByMovieDql(Movie $movie)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT c, p
            FROM App\Entity\Casting c
            INNER JOIN c.person p
            WHERE c.movie = :movie
            ORDER BY c.creditOrder ASC'
        )->setParameter('movie', $movie);

        return $query->getResult();
    }

    /**
     * Get Casting + Person for a given movie
     */
    public function findAllJoinedToPersonByMovieQb(Movie $movie)
    {
        // SELECT c.* FROM casting
        return $this->createQueryBuilder('c')
            // JOIN casting ON c.person = person.id
            ->innerJoin('c.person', 'p')
            // SELECT aussi p.* :)
            ->addSelect('p')
            // WHERE c.movie_id = movie.id
            ->where('c.movie = :movie')
            ->setParameter('movie', $movie)
            // ORDER BY creditOrder ASC
            ->orderBy('c.creditOrder', 'ASC')
            // Récupère la requête
            ->getQuery()
            // Récupère les résultats
            ->getResult();
    }
    
    // /**
    //  * @return Casting[] Returns an array of Casting objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Casting
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
