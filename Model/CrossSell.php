<?php

namespace IVN\OneStepCheckout\Model;

use Magento\CatalogInventory\Helper\Stock as StockHelper;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class CrossSell
 * @package IVN\OneStepCheckout\Model
 */
class CrossSell implements \IVN\OneStepCheckout\Api\CrossSellInterface
{
    /**
     * Items quantity will be capped to this value
     *
     * @var int
     */
    protected $_maxItemCount = 3;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_productVisibility;

    /**
     * @var StockHelper
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\Product\LinkFactory
     */
    protected $_productLinkFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Item\RelatedProducts
     */
    protected $_itemRelationsList;

    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * @var ImageProvider
     */
    protected $imageProvider;

    /**
     * @var ImageProvider
     */
    protected $quoteRepository;


    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Catalog\Model\Product\Visibility $productVisibility,
        \Magento\Catalog\Model\Product\LinkFactory $productLinkFactory,
        \Magento\Quote\Model\Quote\Item\RelatedProducts $itemRelationsList,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        ImageProvider $imageProvider,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_productVisibility = $productVisibility;
        $this->_productLinkFactory = $productLinkFactory;
        $this->_itemRelationsList = $itemRelationsList;
        $this->_storeManager = $storeManager;
        $this->_catalogConfig = $catalogConfig;
        $this->imageProvider = $imageProvider;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param $cartId
     * @return array|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCrossSellItems($cartId)
    {
        $items = [];
        $ninProductIds = $this->_getCartProductIds($cartId);
        if ($ninProductIds) {
            $lastAdded = (int)$this->_getLastAddedProductId();
            if ($lastAdded) {
                $collection = $this->_getCollection()->addProductFilter($lastAdded);
                if (!empty($ninProductIds)) {
                    $collection->addExcludeProductFilter($ninProductIds);
                }
                $collection->setPositionOrder()->load();

                foreach ($collection as $item) {
                    $ninProductIds[] = $item->getId();
                    $items[] =  [
                        'item_id' => $item->getId(),
                        'name' => $item->getName(),
                        'row_total' => ($item->getFinalPrice() > 0) ? $item->getFinalPrice() : $item->getMinPrice(),
                        'image_data' => $this->imageProvider->getConfigImageData($item),
                    ];
                }
            }

            if (count($items) < $this->_maxItemCount) {
                $filterProductIds = array_merge(
                    $this->_getCartProductIds($cartId),
                    $this->_itemRelationsList->getRelatedProductIds($this->getQuote($cartId)->getAllItems())
                );
                $collection = $this->_getCollection()->addProductFilter(
                    $filterProductIds
                )->addExcludeProductFilter(
                    $ninProductIds
                )->setPageSize(
                    $this->_maxItemCount - count($items)
                )->setGroupBy()->setPositionOrder()->load();

                foreach ($collection as $item) {
                    $items[] = [
                        'item_id' => $item->getId(),
                        'item_url' => $item->getProductUrl(),
                        'name' => $item->getName(),
                        'row_total' => ($item->getFinalPrice() > 0) ? $item->getFinalPrice() : $item->getMinPrice(),
                        'sku' => $item->getSku(),
                        'image_data' => $this->imageProvider->getConfigImageData($item),
                    ];
                }
            }
        }

        return $items;
    }

    /**
     * @param $cartId
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getCartProductIds($cartId)
    {
        $ids = [];
        foreach ($this->getQuote($cartId)->getAllItems() as $item) {
            $product = $item->getProduct();
            if ($product) {
                $ids[] = $product->getId();
            }
        }

        return $ids;
    }

    /**
     * Get last product ID that was added to cart and remove this information from session
     *
     * @return int
     * @codeCoverageIgnore
     */
    protected function _getLastAddedProductId()
    {
        return $this->_checkoutSession->getLastAddedProductId(true);
    }

    /**
     * @param $cartId
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuote($cartId)
    {
        return $this->quoteRepository->getActive($cartId);
    }

    /**
     * Get crosssell products collection
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    protected function _getCollection()
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $collection */
        $collection = $this->_productLinkFactory->create()->useCrossSellLinks()->getProductCollection()->setStoreId(
            $this->_storeManager->getStore()->getId()
        )->addStoreFilter()->setPageSize(
            $this->_maxItemCount
        )->setVisibility(
            $this->_productVisibility->getVisibleInCatalogIds()
        );
        $this->_addProductAttributesAndPrices($collection);

        return $collection;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _addProductAttributesAndPrices(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        return $collection
            ->addMinimalPrice()
            ->addFinalPrice()
            ->addTaxPercents()
            ->addAttributeToSelect($this->_catalogConfig->getProductAttributes())
            ->addUrlRewrite();
    }
}
