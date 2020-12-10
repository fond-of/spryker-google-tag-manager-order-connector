<?php

namespace FondOfSpryker\Yves\GoogleTagManagerOrderConnector;

use FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Expander\DataLayerExpander;
use FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Expander\DataLayerExpanderInterface;
use Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface;
use Spryker\Yves\Kernel\AbstractFactory;

class GoogleTagManagerOrderConnectorFactory extends AbstractFactory
{
    /**
     * @return \FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Expander\DataLayerExpanderInterface
     */
    public function createDataLayerExpander(): DataLayerExpanderInterface
    {
        return new DataLayerExpander($this->getMoneyPlugin());
    }

    /**
     * @return \Spryker\Shared\Money\Dependency\Plugin\MoneyPluginInterface
     */
    public function getMoneyPlugin(): MoneyPluginInterface
    {
        return $this->getProvidedDependency(GoogleTagManagerOrderConnectorDependencyProvider::MONEY_PLUGIN);
    }
}
