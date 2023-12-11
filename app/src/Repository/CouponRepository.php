<?php

namespace App\Repository;

use App\Entity\Coupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @extends ServiceEntityRepository<Coupon>
 *
 * @method Coupon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coupon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coupon[]    findAll()
 * @method Coupon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getByCode(string $code): Coupon
    {
        $coupon = $this->createQueryBuilder('c')
            ->where('c.code =:code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$coupon){
            throw new NotFoundHttpException();
        }

        return $coupon;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findByCode(string $code): ?Coupon
    {
        return $this->createQueryBuilder('c')
            ->where('c.code =:code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
