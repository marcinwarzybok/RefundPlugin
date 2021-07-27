<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\RefundPlugin\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\RefundPlugin\Exception\MoreThanOneTaxAdjustment;
use Sylius\RefundPlugin\Provider\TaxRateProviderInterface;

final class TaxRateProviderSpec extends ObjectBehavior
{
    function it_implements_tax_rate_provider_interface(): void
    {
        $this->shouldImplement(TaxRateProviderInterface::class);
    }

    function it_provides_a_tax_rate_from_tax_adjustment_details(
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $taxAdjustment
    ): void {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxAdjustment->getDetails()->willReturn(['taxRateAmount' => 0.2]);

        $this->provide($orderItemUnit)->shouldReturn('20%');
    }

    function it_returns_null_if_there_is_no_tax_adjustment(OrderItemUnitInterface $orderItemUnit): void
    {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([]))
        ;

        $this->provide($orderItemUnit)->shouldReturn(null);
    }

    function it_throws_an_exception_if_there_is_no_tax_rate_amount_in_details_of_adjustment(
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $taxAdjustment
    ): void {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$taxAdjustment->getWrappedObject()]))
        ;

        $taxAdjustment->getDetails()->willReturn([]);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$orderItemUnit]);
    }

    function it_throws_an_exception_if_order_item_unit_has_more_adjustments_than_one(
        OrderItemUnitInterface $orderItemUnit,
        AdjustmentInterface $firstTaxAdjustment,
        AdjustmentInterface $secondTaxAdjustment
    ): void {
        $orderItemUnit
            ->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT)
            ->willReturn(new ArrayCollection([$firstTaxAdjustment->getWrappedObject(), $secondTaxAdjustment->getWrappedObject()]))
        ;

        $this->shouldThrow(MoreThanOneTaxAdjustment::class)->during('provide', [$orderItemUnit]);
    }
}
