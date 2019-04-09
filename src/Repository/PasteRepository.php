<?php

namespace App\Repository;

use App\Entity\Paste;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Paste|null find($id, $lockMode = null, $lockVersion = null)
 * @method Paste|null findOneBy(array $criteria, array $orderBy = null)
 * @method Paste[]    findAll()
 * @method Paste[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PasteRepository extends ServiceEntityRepository {
    public function __construct(RegistryInterface $registry) {
        parent::__construct($registry, Paste::class);
    }

    public function findOneByName($value): ?Paste {
        return $this->createQueryBuilder('p')
            ->andWhere('p.name = :name')
            ->setParameter('name', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUserPastes(User $user) {
        return $this->createQueryBuilder('p')
            ->andWhere('p.owner = :owner')
            ->setParameter('owner', $user->getId())
            ->getQuery()
            ->getResult();
    }

    public function getSidebar() {
        return $this->createQueryBuilder('p')
            ->andWhere('p.visibility = 1')
            ->setMaxResults(8)
            ->orderBy('p.upload_date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
