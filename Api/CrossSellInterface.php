<?php

namespace IVN\OneStepCheckout\Api;

/**
 * Interface CrossSellInterface
 * @package IVN\OneStepCheckout\Api
 */
interface CrossSellInterface
{
    /**
     * @param $cartId
     * @return mixed
     */
    public function getCrossSellItems($cartId);
}
