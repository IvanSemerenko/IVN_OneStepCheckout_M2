<?php

namespace IVN\OneStepCheckout\Api;

/**
 * Interface GuestCrossSellInterface
 * @package IVN\OneStepCheckout\Api
 */
interface GuestCrossSellInterface
{
    /**
     * @param string $cartId
     * @return mixed
     */
    public function getCrossSellItems($cartId);
}
