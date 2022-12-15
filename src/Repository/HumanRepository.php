<?php

namespace App\Repository;

use App\Entity\Human;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Human>
 *
 * @method Human|null find($id, $lockMode = null, $lockVersion = null)
 * @method Human|null findOneBy(array $criteria, array $orderBy = null)
 * @method Human[]    findAll()
 * @method Human[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HumanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Human::class);
    }

    public function save(Human $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush)
        {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Human $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush)
        {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Human[] Returns an array of Human objects
     */
    public function findChildren(Human $human): array
    {
        return $this->createQueryBuilder('h')
            ->andWhere('h.father = :val OR h.mother = :val')
            ->setParameter('val', $human->getId())
            ->orderBy('h.year_birth', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @return Human[] Returns an array of Human objects
     */
    public function findOldestInTreeIncluding(Human $human): ?array
    {
        $return = [];
        $mother = $human->getMother();
        $father = $human->getFather();
        if ($mother == null && $father == null)
        {
            return [$human];
        }
        if ($father != null)
        {
            $return = array_merge($return, $this->findOldestInTreeIncluding($father));
        }
        if ($mother != null)
        {
            $return = array_merge($return, $this->findOldestInTreeIncluding($mother));
        }
        return $return;

    }

    public function findSpouse(Human $human): ? Human
    {
        return $this->createQueryBuilder('h')
            ->andWhere('(SELECT COUNT(child.id) FROM App\Entity\Human AS child WHERE (h.gender = \'male\' AND child.father = h.id AND child.mother = :id) OR (h.gender = \'female\' AND child.mother = h.id AND child.father = :id)) > 0')

            ->setParameter('id', $human->getId())
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }











}