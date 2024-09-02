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

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Model\RefundTypeInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Webmozart\Assert\Assert;

final class RemainingTotalProvider implements RemainingTotalProviderInterface
{
    private RepositoryInterface $orderItemUnitRepository;

    private RepositoryInterface $adjustmentRepository;

    private RepositoryInterface $refundRepository;

    public function __construct(
        RepositoryInterface $orderItemUnitRepository,
        RepositoryInterface $adjustmentRepository,
        RepositoryInterface $refundRepository,
        private ServiceLocator $totalProviderRegistry,
    ) {
        $this->orderItemUnitRepository = $orderItemUnitRepository;
        $this->adjustmentRepository = $adjustmentRepository;
        $this->refundRepository = $refundRepository;
    }

    public function getTotalLeftToRefund(int $id, RefundTypeInterface $type): int
    {
        $refundUnitTotalProvider = $this->totalProviderRegistry->get($type->getValue());

        return $refundUnitTotalProvider->getRefundUnitTotal($id);
    }

    private function getRefundUnitTotal(int $id, RefundTypeInterface $refundType): int
    {
        $refundUnitTotalProvider = $this->totalProviderRegistry->get($refundType->getValue());

        return $refundUnitTotalProvider->getRefundUnitTotal($id);
    }
}
