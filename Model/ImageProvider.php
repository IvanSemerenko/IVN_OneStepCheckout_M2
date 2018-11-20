<?php

namespace IVN\OneStepCheckout\Model;

use Magento\Store\Model\StoreManagerInterface as StoreManager;
use Magento\Store\Model\App\Emulation as AppEmulation;

/**
 * Class ImageProvider
 * @package IVN\OneStepCheckout\Model
 */
class ImageProvider
{
    /**
     * Image Id
     */
    const IMAGE_ID = 'mini_cart_product_thumbnail';

    /**
     * Image Height
     */
    const IMAGE_HEIGHT = 115;

    /**
     * Image Width
     */
    const IMAGE_WIDTH = 110;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $helperImage;

    /**
     *@var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     *@var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     * ImageProvider constructor.
     * @param \Magento\Catalog\Helper\ImageFactory $imageFactory
     * @param AppEmulation $appEmulation
     * @param StoreManager $storeManager
     */
    public function __construct(
        \Magento\Catalog\Helper\ImageFactory $imageFactory,
        AppEmulation $appEmulation,
        StoreManager $storeManager
    ) {
        $this->helperImage = $imageFactory;
        $this->appEmulation = $appEmulation;
        $this->storeManager = $storeManager;
    }

    /**
     * @param $item
     * @return array
     */
    public function getConfigImageData($item)
    {
        return [
            'attributes' => [
                'width' => self::IMAGE_WIDTH,
                'height' => self::IMAGE_HEIGHT
            ],
            'src' => $this->getImageUrl($item, self::IMAGE_ID),
            'alt' => $item->getName(),
        ];
    }

    /**
     * @param $product
     * @param string|null $imageType
     * @return mixed
     */
    protected function getImageUrl($product, string $imageType = NULL)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $this->appEmulation->startEnvironmentEmulation($storeId, \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $imageUrl = $this->helperImage->create()->init($product, $imageType)->getUrl();
        $this->appEmulation->stopEnvironmentEmulation();

        return $imageUrl;
    }
}
