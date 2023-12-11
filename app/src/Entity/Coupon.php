<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 255)]
    #[Assert\NotBlank]
    private ?string $code;

    #[ORM\Column(type: Types::STRING, length: 50, options: ['default' => 'percentage'])]
    #[Assert\NotBlank]
    private ?string $type;

    #[ORM\Column(type: Types::FLOAT)]
    #[Assert\NotBlank]
    private ?float $value;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    private bool $isActive;

    public function __construct(
        string $code,
        string $type,
        float  $value,
        bool   $isActive = true
    )
    {
        $this->code = $code;
        $this->type = $type;
        $this->value = $value;
        $this->isActive = $isActive;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isTypeFixed(): bool
    {
        return $this->type === 'fixed';
    }
}