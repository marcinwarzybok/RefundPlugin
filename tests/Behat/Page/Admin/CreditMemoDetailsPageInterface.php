<?php

declare(strict_types=1);

namespace Tests\Sylius\RefundPlugin\Behat\Page\Admin;

use Sylius\Behat\Page\SymfonyPageInterface;

interface CreditMemoDetailsPageInterface extends SymfonyPageInterface
{
    public function countUnitsWithProduct(string $productName): int;

    public function download(): void;

    public function getUnitTax(int $number, string $productName): string;

    public function getUnitTotal(int $number, string $unitName): string;

    public function getNumber(): string;

    public function getChannelName(): string;

    public function getTotal(): string;

    public function getComment(): string;
}
