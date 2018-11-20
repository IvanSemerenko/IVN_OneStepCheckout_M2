<?php

namespace IVN\OneStepCheckout\Model;

use IVN\OneStepCheckout\Api\CrossSellInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class GuestCrossSell
 * @package IVN\OneStepCheckout\Model
 */
class GuestCrossSell implements \IVN\OneStepCheckout\Api\GuestCrossSellInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CrossSellInterface
     */
    private $crossSell;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CrossSellInterface $crossSell
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CrossSellInterface $crossSell
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->crossSell = $crossSell;
    }

    /**
     * @param string $cartId
     * @return mixed
     */
    public function getCrossSellItems($cartId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()
            ->load($cartId, 'masked_id');
        return $this->crossSell->getCrossSellItems($quoteIdMask->getQuoteId());
    }
}
