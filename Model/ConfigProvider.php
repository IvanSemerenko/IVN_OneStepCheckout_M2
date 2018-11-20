<?php

namespace IVN\OneStepCheckout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var CrossSell  */
    protected $crossSelItems;

    /** @var \Magento\Checkout\Model\Session  */
    protected $checkoutSession;

    /** @var \Magento\Framework\View\Asset\Repository  */
    protected $assetRepo;

    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        CrossSell $crossSelItems,
        \Magento\Framework\View\Asset\Repository $assetRepo
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->crossSelItems = $crossSelItems;
        $this->assetRepo = $assetRepo;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig()
    {
        return [
            'crossSellItems' => $this->crossSelItems->getCrossSellItems($this->checkoutSession->getQuoteId()),
            'cardinityImg' => $this->assetRepo->getUrl("IVN_OneStepCheckout::images/visa.png"),
            'paypalImg' => $this->assetRepo->getUrl("IVN_OneStepCheckout::images/paypal.png"),
        ];
    }
}