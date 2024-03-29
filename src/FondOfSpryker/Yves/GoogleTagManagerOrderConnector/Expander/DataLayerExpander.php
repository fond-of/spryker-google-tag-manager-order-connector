<?php

namespace FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Expander;

use FondOfSpryker\Shared\GoogleTagManagerOrderConnector\GoogleTagManagerOrderConnectorConstants as ModuleConstants;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface;

class DataLayerExpander implements DataLayerExpanderInterface
{
    public const UNTRANSLATED_KEY = '_';

    /**
     * @var \Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface
     */
    protected $moneyPlugin;

    /**
     * @param \Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface $moneyPlugin
     */
    public function __construct(MoneyPluginInterface $moneyPlugin)
    {
        $this->moneyPlugin = $moneyPlugin;
    }

    /**
     * @param string $page
     * @param array $twigVariableBag
     * @param array $dataLayer
     *
     * @return array
     */
    public function expand(string $page, array $twigVariableBag, array $dataLayer): array
    {
        /** @var \Generated\Shared\Transfer\OrderTransfer $orderTransfer */
        $orderTransfer = $twigVariableBag[ModuleConstants::PARAM_ORDER];

        $dataLayer[ModuleConstants::FIELD_ENTIY] = strtoupper($page);
        $dataLayer[ModuleConstants::FIELD_ID] = $orderTransfer->getOrderReference();
        $dataLayer[ModuleConstants::FIELD_DATE] = $orderTransfer->getCreatedAt();
        $dataLayer[ModuleConstants::FIELD_AFFILIATION] = $orderTransfer->getStore();
        $dataLayer[ModuleConstants::FIELD_TOTAL] = $this->getTotal($orderTransfer);
        $dataLayer[ModuleConstants::FIELD_WITHOUT_SHIPPING_AMOUNT] = $this->getTotalWithoutShippingAmount($orderTransfer);
        $dataLayer[ModuleConstants::FIELD_SUBTOTAL] = $this->getSubtotal($orderTransfer);
        $dataLayer[ModuleConstants::FIELD_TAX] = $this->getTax($orderTransfer);
        $dataLayer[ModuleConstants::FIELD_SHIPPING] = implode(',', $this->getShipmentMethods($orderTransfer));
        $dataLayer[ModuleConstants::FIELD_PAYMENT] = implode(',', $this->getPaymentMethods($orderTransfer));
        $dataLayer[ModuleConstants::FIELD_CURRENCY] = $orderTransfer->getCurrencyIsoCode();
        $dataLayer[ModuleConstants::FIELD_PRODUCTS] = $this->getProducts($orderTransfer);
        $dataLayer[ModuleConstants::FIELD_PRODUCTS_SKUS] = $this->getSkus($orderTransfer);
        $dataLayer[ModuleConstants::FIELD_VOUCHER_CODE] = implode(',', $this->getVoucherCodes($orderTransfer));
        $dataLayer[ModuleConstants::FIELD_DISCOUNT_TOTAL] = $this->getDiscountTotal($orderTransfer);
        $dataLayer[ModuleConstants::FIELD_CUSTOMER_EMAIL] = $this->getCustomerEmail($orderTransfer);

        return $dataLayer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    protected function getProducts(OrderTransfer $orderTransfer): array
    {
        $products = [];

        foreach ($this->mergeMultipleProducts($orderTransfer) as $itemTransfer) {
            $products[] = [
                ModuleConstants::FIELD_PRODUCT_ID => $itemTransfer->getIdProductAbstract(),
                ModuleConstants::FIELD_PRODUCT_SKU => $itemTransfer->getSku(),
                ModuleConstants::FIELD_PRODUCT_NAME => $this->getProductName($itemTransfer),
                ModuleConstants::FIELD_PRODUCT_PRICE => $this->getProductPrice($itemTransfer),
                ModuleConstants::FIELD_PRODUCT_PRICE_EXCLUDING_TAX => $this->getPriceExcludingTax($itemTransfer),
                ModuleConstants::FIELD_PRODUCT_TAX => $this->getProductTaxAmount($itemTransfer),
                ModuleConstants::FIELD_PRODUCT_TAX_RATE => $itemTransfer->getTaxRate(),
                ModuleConstants::FIELD_PRODUCT_QUANTITY => $itemTransfer->getQuantity(),
            ];
        }

        return $products;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    protected function mergeMultipleProducts(OrderTransfer $orderTransfer): array
    {
        /** @var \Generated\Shared\Transfer\ItemTransfer[] $products */
        $products = [];

        foreach ($orderTransfer->getItems() as $itemTransfer) {
            if (array_key_exists($itemTransfer->getSku(), $products)) {
                $products[$itemTransfer->getSku()]->setQuantity(
                    $products[$itemTransfer->getSku()]->getQuantity() + $itemTransfer->getQuantity()
                );

                continue;
            }

            $products[$itemTransfer->getSku()] = $itemTransfer;
        }

        return $products;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return float
     */
    protected function getTotal(OrderTransfer $orderTransfer): float
    {
        if ($orderTransfer->getTotals() === null) {
            return 0;
        }

        return $this->moneyPlugin->convertIntegerToDecimal(
            $orderTransfer->getTotals()->getGrandTotal()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return float
     */
    protected function getSubtotal(OrderTransfer $orderTransfer): float
    {
        if ($orderTransfer->getTotals() === null) {
            return 0;
        }

        return $this->moneyPlugin->convertIntegerToDecimal(
            $orderTransfer->getTotals()->getSubtotal()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return float
     */
    protected function getTax(OrderTransfer $orderTransfer): float
    {
        if ($orderTransfer->getTotals() === null) {
            return 0;
        }

        if ($orderTransfer->getTotals()->getTaxTotal() === null) {
            return 0;
        }

        return $this->moneyPlugin->convertIntegerToDecimal(
            $orderTransfer->getTotals()->getTaxTotal()->getAmount()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    protected function getShipmentMethods(OrderTransfer $orderTransfer): array
    {
        $shipmentMethods = [];

        foreach ($orderTransfer->getItems() as $itemTransfer) {
            if ($itemTransfer->getShipment() === null) {
                continue;
            }

            if ($itemTransfer->getShipment()->getMethod() === null) {
                continue;
            }

            if (!in_array($itemTransfer->getShipment()->getMethod()->getName(), $shipmentMethods)) {
                $shipmentMethods[] = $itemTransfer->getShipment()->getMethod()->getName();
            }
        }

        return $shipmentMethods;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    protected function getPaymentMethods(OrderTransfer $orderTransfer): array
    {
        $paymentMethods = [];

        foreach ($orderTransfer->getPayments() as $payment) {
            $paymentMethods[] = $payment->getPaymentMethod();
        }

        return $paymentMethods;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    protected function getSkus(OrderTransfer $orderTransfer): array
    {
        $collection = [];

        foreach ($orderTransfer->getItems() as $itemTransfer) {
            if (!in_array($itemTransfer->getSku(), $collection)) {
                $collection[] = $itemTransfer->getSku();
            }
        }

        return $collection;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function getProductName(ItemTransfer $itemTransfer): string
    {
        $productAttributes = $itemTransfer->getAbstractAttributes();

        if (isset($productAttributes[static::UNTRANSLATED_KEY][ModuleConstants::PARAMETER_PRODUCT_ATTR_NAME_UNTRANSLATED])) {
            return $productAttributes[static::UNTRANSLATED_KEY][ModuleConstants::PARAMETER_PRODUCT_ATTR_NAME_UNTRANSLATED];
        }

        return $itemTransfer->getName();
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return float
     */
    protected function getProductPrice(ItemTransfer $itemTransfer): float
    {
        return $this->moneyPlugin->convertIntegerToDecimal($itemTransfer->getUnitPrice());
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return float
     */
    protected function getPriceExcludingTax(ItemTransfer $itemTransfer): float
    {
        return $this->moneyPlugin->convertIntegerToDecimal($itemTransfer->getUnitPrice() - $itemTransfer->getUnitTaxAmount());
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return float
     */
    protected function getProductTaxAmount(ItemTransfer $itemTransfer): float
    {
        return $this->moneyPlugin->convertIntegerToDecimal($itemTransfer->getUnitTaxAmount());
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return float|null
     */
    protected function getTotalWithoutShippingAmount(OrderTransfer $orderTransfer): ?float
    {
        if ($orderTransfer->getTotals() === null) {
            return null;
        }

        $expenseTotal = (int)$orderTransfer->getTotals()->getExpenseTotal();
        $grandTotal = (int)$orderTransfer->getTotals()->getGrandTotal();

        if ($expenseTotal === null || $grandTotal === null) {
            return null;
        }

        return $this->moneyPlugin->convertIntegerToDecimal($grandTotal - $expenseTotal);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return float|null
     */
    protected function getDiscountTotal(OrderTransfer $orderTransfer): ?float
    {
        if ($orderTransfer->getTotals() instanceof TotalsTransfer && $orderTransfer->getTotals()->getDiscountTotal() > 0) {
            return $this->moneyPlugin->convertIntegerToDecimal($orderTransfer->getTotals()->getDiscountTotal());
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return array
     */
    protected function getVoucherCodes(OrderTransfer $orderTransfer): array
    {
        $codes = [];

        foreach ($orderTransfer->getCalculatedDiscounts() as $calculatedDiscountTransfer) {
            if (!in_array($calculatedDiscountTransfer->getVoucherCode(), $codes)) {
                $codes[] = $calculatedDiscountTransfer->getVoucherCode();
            }
        }

        return $codes;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return string|null
     */
    protected function getCustomerEmail(OrderTransfer $orderTransfer): ?string
    {
        if ($orderTransfer->getBillingAddress() === null) {
            return null;
        }

        if ($orderTransfer->getBillingAddress()->getEmail() === null) {
            return null;
        }

        return $orderTransfer->getBillingAddress()->getEmail();
    }
}
