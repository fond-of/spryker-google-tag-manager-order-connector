<?php

namespace FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Plugin\DataLayer;

use Codeception\Test\Unit;
use FondOfSpryker\Shared\GoogleTagManagerOrderConnector\GoogleTagManagerOrderConnectorConstants as ModuleConstants;
use FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Expander\DataLayerExpander;
use FondOfSpryker\Yves\GoogleTagManagerOrderConnector\GoogleTagManagerOrderConnectorFactory;
use Generated\Shared\Transfer\OrderTransfer;

class OrderDataLayerExpanderPluginTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Yves\GoogleTagManagerOrderConnector\GoogleTagManagerOrderConnectorFactory
     */
    protected $factoryMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Generated\Shared\Transfer\OrderTransfer
     */
    protected $orderTransferMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Expander\DataLayerExpander
     */
    protected $dataLayerExpanderMock;

    /**
     * @var \FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Plugin\DataLayer\OrderDataLayerExpanderPlugin
     */
    protected $plugin;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->factoryMock = $this->getMockBuilder(GoogleTagManagerOrderConnectorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->orderTransferMock = $this->getMockBuilder(OrderTransfer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dataLayerExpanderMock = $this->getMockBuilder(DataLayerExpander::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new OrderDataLayerExpanderPlugin();
        $this->plugin->setFactory($this->factoryMock);
    }

    /**
     * @return void
     */
    public function testIsApplicable(): void
    {
        static::assertEquals(true, $this->plugin->isApplicable(
            ModuleConstants::PAGE_TYPE,
            [ModuleConstants::PARAM_ORDER => $this->orderTransferMock]
        ));
    }

    /**
     * @return void
     */
    public function testIsNotApplicableWrongPageType(): void
    {
        static::assertEquals(false, $this->plugin->isApplicable(
            'PAGE',
            [ModuleConstants::PARAM_ORDER => $this->orderTransferMock]
        ));
    }

    /**
     * @return void
     */
    public function testIsNotApplicableOrderMissing(): void
    {
        static::assertEquals(false, $this->plugin->isApplicable(
            ModuleConstants::PAGE_TYPE,
            []
        ));
    }

    /**
     * @return void
     */
    public function testExpand(): void
    {
        $this->factoryMock->expects(static::atLeastOnce())
            ->method('createDataLayerExpander')
            ->willReturn($this->dataLayerExpanderMock);

        $this->dataLayerExpanderMock->expects(static::atLeastOnce())
            ->method('expand')
            ->with(ModuleConstants::PAGE_TYPE, [ModuleConstants::PARAM_ORDER => $this->orderTransferMock], [])
            ->willReturn([]);

        $this->plugin->expand(ModuleConstants::PAGE_TYPE, [ModuleConstants::PARAM_ORDER => $this->orderTransferMock], []);
    }
}
