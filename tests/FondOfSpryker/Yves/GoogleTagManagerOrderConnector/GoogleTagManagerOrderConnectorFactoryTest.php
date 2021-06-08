<?php

namespace FondOfSpryker\Yves\GoogleTagManagerOrderConnector;

use Codeception\Test\Unit;
use FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Expander\DataLayerExpander;
use Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface;
use Spryker\Yves\Kernel\Container;

class GoogleTagManagerOrderConnectorFactoryTest extends Unit
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Yves\Kernel\Container
     */
    protected $containerMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Expander\DataLayerExpander
     */
    protected $dataLayerExpanderMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface
     */
    protected $moneyPluginMock;

    /**
     * @var \FondOfSpryker\Yves\GoogleTagManagerOrderConnector\GoogleTagManagerOrderConnectorFactory
     */
    protected $factory;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->dataLayerExpanderMock = $this->getMockBuilder(DataLayerExpander::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->containerMock = $this->getMockBuilder(Container::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->moneyPluginMock = $this->getMockBuilder(MoneyPluginInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new GoogleTagManagerOrderConnectorFactory();
        $this->factory->setContainer($this->containerMock);
    }

    /**
     * @return void
     */
    public function testCreateDataLayerExpander(): void
    {
        $this->containerMock->expects(static::atLeastOnce())
            ->method('has')
            ->willReturn(true);

        $this->containerMock->expects(static::atLeastOnce())
            ->method('get')
            ->willReturn($this->moneyPluginMock);

        $this->factory->createDataLayerExpander();
    }

    /**
     * @return void
     */
    public function testGetMoneyPlugin(): void
    {
        $this->containerMock->expects(static::atLeastOnce())
            ->method('has')
            ->willReturn(true);

        $this->containerMock->expects(static::atLeastOnce())
            ->method('get')
            ->willReturn($this->moneyPluginMock);

        static::assertEquals(
            $this->moneyPluginMock,
            $this->factory->getMoneyPlugin()
        );
    }
}
