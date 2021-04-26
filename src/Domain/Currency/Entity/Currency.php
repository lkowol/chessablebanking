<?php


namespace ChessableBanking\Domain\Currency\Entity;

class Currency
{
    //TODO: Currency should have precision knowledge to allow currencies like KWD to behave correctly
    public const PRECISION = 2;

    private string $id;
    private string $name;
    private string $iso4217Code;
    private float $rateToDefaultCurrency;
    private bool $isDefault;

    public function __construct(
        string $id,
        string $name,
        string $iso4217Code,
        float $rateToDefaultCurrency,
        bool $isDefault
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->iso4217Code = $iso4217Code;
        $this->rateToDefaultCurrency = $rateToDefaultCurrency;
        $this->isDefault = $isDefault;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIso4217Code(): string
    {
        return $this->iso4217Code;
    }

    public function getRateToDefaultCurrency(): float
    {
        return $this->rateToDefaultCurrency;
    }

    public function isDefault(): bool
    {
        return $this->isDefault;
    }
}
