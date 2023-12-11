<?php

namespace App\Command\Payment\Create;

class Command
{
    private int $productId;

    private float $totalPrice;

    private ?string $couponCode;

    private string $paymentType;

    public function __construct(
        int $productId,
        float $totalPrice,
        string $paymentType,
        ?string $couponCode = null
    )
    {
        $this->productId = $productId;
        $this->totalPrice = $totalPrice;
        $this->couponCode = $couponCode;
        $this->paymentType = $paymentType;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function getCouponCode(): ?string
    {
        return $this->couponCode;
    }

    public function getPaymentType(): string
    {
        return $this->paymentType;
    }
}