<?php

namespace FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Expander;

use Codeception\Test\Unit;
use FondOfSpryker\Shared\GoogleTagManagerOrderConnector\GoogleTagManagerOrderConnectorConstants as ModuleConstants;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CalculatedDiscountTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\TaxTotalTransfer;
use Generated\Shared\Transfer\TotalsTransfer;
use Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface;

class DataLayerExpanderTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\OrderTransfer
     */
    protected $orderTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface
     */
    protected $moneyPluginMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\TotalsTransfer
     */
    protected $totalsTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\TaxTotalTransfer
     */
    protected $taxTotalTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\ItemTransfer
     */
    protected $itemTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\PaymentTransfer
     */
    protected $paymentTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\CalculatedDiscountTransfer
     */
    protected $calucatedDiscountTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\AddressTransfer
     */
    protected $billingAddressMock;

    /**
     * @var \FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Expander\DataLayerExpanderInterface
     */
    protected $expander;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->orderTransferMock = $this->getMockBuilder(OrderTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->totalsTransferMock = $this->getMockBuilder(TotalsTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->taxTotalTransferMock = $this->getMockBuilder(TaxTotalTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->itemTransferMock = $this->getMockBuilder(ItemTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->paymentTransferMock = $this->getMockBuilder(PaymentTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->moneyPluginMock = $this->getMockBuilder(MoneyPluginInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->calucatedDiscountTransferMock = $this->getMockBuilder(CalculatedDiscountTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->billingAddressMock = $this->getMockBuilder(AddressTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->expander = new DataLayerExpander($this->moneyPluginMock);
    }

    /**
     * @return void
     */
    public function testExpand(): void
    {
        $this->orderTransferMock->expects(static::atLeastOnce())
            ->method('getItems')
            ->willReturn([$this->itemTransferMock]);

        $this->orderTransferMock->expects(static::atLeastOnce())
            ->method('getPayments')
            ->willReturn([$this->paymentTransferMock]);

        $this->itemTransferMock->expects(static::atLeastOnce())
            ->method('getName')
            ->willReturn('product name');

        $this->orderTransferMock->expects(static::atLeastOnce())
            ->method('getTotals')
            ->willReturn($this->totalsTransferMock);

        $this->totalsTransferMock->expects(static::atLeastOnce())
            ->method('getGrandTotal')
            ->willReturn(9999);

        $this->totalsTransferMock->expects(static::atLeastOnce())
            ->method('getShipmentTotal')
            ->willReturn(999);

        $this->totalsTransferMock->expects(static::atLeastOnce())
            ->method('getSubtotal')
            ->willReturn(5555);

        $this->itemTransferMock->expects(static::atLeastOnce())
            ->method('getUnitPrice')
            ->willReturn(2999);

        $this->itemTransferMock->expects(static::atLeastOnce())
            ->method('getUnitTaxAmount')
            ->willReturn(999);

        $this->moneyPluginMock->expects(static::atLeastOnce())
            ->method('convertIntegerToDecimal')
            ->withConsecutive([9999], [9000], [5555], [2999], [2000], [999])
            ->willReturnOnConsecutiveCalls(99.99, 90, 55.55, 29.99, 20, 9.99);

        $this->orderTransferMock->expects(static::atLeastOnce())
            ->method('getCalculatedDiscounts')
            ->willReturn([$this->calucatedDiscountTransferMock]);

        $this->calucatedDiscountTransferMock->expects(static::atLeastOnce())
            ->method('getVoucherCode')
            ->willReturn('VOUCHER_CODE');

        $this->orderTransferMock->expects(static::atLeastOnce())
            ->method('getBillingAddress')
            ->willReturn($this->billingAddressMock);

        $this->billingAddressMock->expects(static::atLeastOnce())
            ->method('getEmail')
            ->willReturn('foo@bar.com');

        $result = $this->expander->expand(
            ModuleConstants::PAGE_TYPE,
            [ModuleConstants::PARAM_ORDER => $this->orderTransferMock],
            []
        );

        static::assertArrayHasKey(ModuleConstants::FIELD_ENTIY, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_ID, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_DATE, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_AFFILIATION, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_TOTAL, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_WITHOUT_SHIPPING_AMOUNT, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_SUBTOTAL, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_TAX, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_SHIPPING, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_PAYMENT, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_CURRENCY, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_PRODUCTS, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_PRODUCTS_SKUS, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_VOUCHER_CODE, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_DISCOUNT_TOTAL, $result);
        static::assertArrayHasKey(ModuleConstants::FIELD_CUSTOMER_EMAIL, $result);
    }
}
