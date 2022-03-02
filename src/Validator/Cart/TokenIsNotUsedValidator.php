<?php

/**
 * This file is part of the Sylius package.
 *
 *  (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\ShopApiPlugin\Validator\Cart;

use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class TokenIsNotUsedValidator extends ConstraintValidator
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    /** {@inheritdoc} */
    public function validate($token, Constraint $constraint): void
    {
        if (null !== $this->orderRepository->findOneBy(['tokenValue' => $token])) {
            $this->context->addViolation($constraint->message);
        }
    }
}
