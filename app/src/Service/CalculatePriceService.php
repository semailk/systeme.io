<?php

namespace App\Service;

use App\Command\Payment\Create\Command;
use App\Command\Payment\Create\Handler;
use App\Dto\CalculatePriceRequest;
use App\Dto\PurchaseRequest;
use App\Entity\Coupon;
use App\Entity\Payment;
use App\Repository\CouponRepository;
use App\Repository\ProductRepository;
use App\Service\PaymentProcessor\PaypalPaymentProcessor;
use App\Service\PaymentProcessor\StripePaymentProcessor;

class CalculatePriceService
{
    private const TAX_RATES = [
        'DE' => 19,
        'IT' => 22,
        'FR' => 20,
        'GR' => 24
    ];

    public function __construct(
        private readonly ProductRepository      $productRepository,
        private readonly CouponRepository       $couponRepository,
        private readonly PaypalPaymentProcessor $paypalPaymentProcessor,
        private readonly StripePaymentProcessor $stripePaymentProcessor,
        private readonly Handler                $handler
    ){
    }

    public function getTaxRate(string $taxNumber): int
    {
        return self::TAX_RATES[substr(preg_replace("/[0-9]/", "", $taxNumber),0,2)] ?? throw new \InvalidArgumentException('Invalid country code');
    }

    public function getCalculatePrice(CalculatePriceRequest $calculatePriceRequest): float|null
    {
        $product = $this->productRepository->getById($calculatePriceRequest->product);
        $productPrice = $product->getPrice();

        $taxRate = $this->getTaxRate($calculatePriceRequest->taxNumber);

        $coupon = $calculatePriceRequest->couponCode;
        if ($coupon) {
            $coupon = $this->couponRepository->getByCode($calculatePriceRequest->couponCode);
        }

        return $this->getTotalPrice($productPrice, $taxRate, $coupon);
    }

    public function getTotalPrice(float $productPrice, int $taxRate, Coupon $coupon = null): float|null
    {
        $taxAmount = $productPrice / 100 * $taxRate;
        $totalPrice = $productPrice + $taxAmount;

        if ($coupon) {
            if ($coupon->isTypeFixed()) {
                $totalPrice = $totalPrice - $coupon->getValue();
            } else {
                $totalPrice = $totalPrice - ($totalPrice / 100 * $coupon->getValue());
            }
        }

        return $totalPrice;
    }

    public function purchace(PurchaseRequest $purchaseRequest): Payment
    {

        $product = $this->productRepository->getById($purchaseRequest->product);
        $productPrice = $product->getPrice();
        $taxRate = $this->getTaxRate($purchaseRequest->taxNumber);

        $coupon = $purchaseRequest->couponCode;
        if ($coupon) {
            $coupon = $this->couponRepository->getByCode($purchaseRequest->couponCode);
        }

        $totalPrice = $this->getTotalPrice($productPrice, $taxRate, $coupon);

        return match ($purchaseRequest->paymentProcessor) {
            'paypal' => $this->paypalPaymentProcessor::pay(
                $this->handler,
                new Command(
                    $product->getId(),
                    $totalPrice,
                    $purchaseRequest->paymentProcessor,
                    $coupon?->getCode())
            ),
            'stripe' => $this->stripePaymentProcessor::processPayment(
                $this->handler,
                new Command(
                    $product->getId(),
                    $totalPrice,
                    $purchaseRequest->paymentProcessor,
                    $coupon?->getCode()
                ))
        };
    }
}