<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
class PurchaseRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $product;

    #[Assert\NotBlank]
    #[Assert\Regex("/^(DE|IT|GR|FR[A-Z]{0,2})\d+$/")]
    public string $taxNumber;

    #[Assert\Regex('/^[A-Z0-9]+$/')]
    public ?string $couponCode = null;

    #[Assert\NotBlank]
    #[Assert\Choice(['paypal', 'stripe'])]
    public string $paymentProcessor;
}