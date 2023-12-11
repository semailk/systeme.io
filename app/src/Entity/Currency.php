<?php

namespace App\Entity;

use App\Repository\CurrencyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
class Currency
{
    #[ORM\Column(type: 'currency_code', length: 3, unique: true)]
    #[ORM\Id]
    private Code $code;

    #[ORM\Column(type: Types::STRING, length: 8)]
    #[Assert\Length(min: 1, max: 8)]
    private string $name;

    public function __construct(
        ?Code $code = null,
        ?string $name = null
    )
    {
        $this->name = strtoupper($name);
        $this->code = $code;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = strtoupper($name);

        return $this;
    }

    public function getCode(): Code
    {
        return $this->code;
    }

    public function setCode(Code $code): void
    {
        $this->code = $code;
    }
}
