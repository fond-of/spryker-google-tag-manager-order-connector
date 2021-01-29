<?php

namespace FondOfSpryker\Shared\GoogleTagManagerOrderConnector;

interface GoogleTagManagerOrderConnectorConstants
{
    public const PAGE_TYPE = 'order';

    public const PARAM_ORDER = 'order';
    public const PARAMETER_PRODUCT_ATTR_NAME_UNTRANSLATED = 'name_untranslated';

    public const FIELD_ENTIY = 'transactionEntity';
    public const FIELD_ID = 'transactionId';
    public const FIELD_DATE = 'transactionDate';
    public const FIELD_AFFILIATION = 'transactionAffiliation';
    public const FIELD_TOTAL = 'transactionTotal';
    public const FIELD_WITHOUT_SHIPPING_AMOUNT = 'transactionTotalWithoutShippingAmount';
    public const FIELD_SUBTOTAL = 'transactionSubtotal';
    public const FIELD_TAX = 'transactionTax';
    public const FIELD_SHIPPING = 'transactionShipping';
    public const FIELD_PAYMENT = 'transactionPayment';
    public const FIELD_CURRENCY = 'transactionCurrency';
    public const FIELD_PRODUCTS = 'transactionProducts';
    public const FIELD_PRODUCTS_SKUS = 'transactionProductsSkus';
    public const FIELD_VOUCHER_CODE = 'voucherCode';
    public const FIELD_DISCOUNT_TOTAL = 'discountTotal';
    public const FIELD_CUSTOMER_EMAIL = 'customerEmail';

    public const FIELD_PRODUCT_ID = 'id';
    public const FIELD_PRODUCT_SKU = 'sku';
    public const FIELD_PRODUCT_NAME = 'name';
    public const FIELD_PRODUCT_PRICE = 'price';
    public const FIELD_PRODUCT_PRICE_EXCLUDING_TAX = 'priceexcludingtax';
    public const FIELD_PRODUCT_TAX = 'tax';
    public const FIELD_PRODUCT_TAX_RATE = 'taxrate';
    public const FIELD_PRODUCT_QUANTITY = 'quantity';
}
