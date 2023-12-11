<?php

namespace App\Command\Payment\Create;

use App\Entity\Payment;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

readonly class Handler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ProductRepository      $productRepository,
        private CouponRepository       $couponRepository
    )
    {
    }

    public function handle(Command $command): Payment
    {
        $product = $this->productRepository->getById($command->getProductId());
        $coupon = $command->getCouponCode() ? $this->couponRepository->findByCode($command->getCouponCode()) : null;
        $payment = new Payment($product, $command->getTotalPrice(), $command->getPaymentType(), $coupon);
        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        return $payment;
    }
}