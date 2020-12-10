<?php

namespace FondOfSpryker\Yves\GoogleTagManagerOrderConnector\Plugin\DataLayer;

use FondOfSpryker\Shared\GoogleTagManagerOrderConnector\GoogleTagManagerOrderConnectorConstants as ModuleConstants;
use FondOfSpryker\Yves\GoogleTagManagerExtension\Dependency\GoogleTagManagerDataLayerExpanderPluginInterface;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \FondOfSpryker\Yves\GoogleTagManagerOrderConnector\GoogleTagManagerOrderConnectorFactory getFactory()
 */
class OrderDataLayerExpanderPlugin extends AbstractPlugin implements GoogleTagManagerDataLayerExpanderPluginInterface
{
    /**
     * @param string $pageType
     * @param array $twigVariableBag
     *
     * @return bool
     */
    public function isApplicable(string $pageType, array $twigVariableBag = []): bool
    {
        return $pageType === ModuleConstants::PAGE_TYPE
            && isset($twigVariableBag[ModuleConstants::PARAM_ORDER])
            && $twigVariableBag[ModuleConstants::PARAM_ORDER] instanceof OrderTransfer;
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
        return $this->getFactory()
            ->createDataLayerExpander()
            ->expand($page, $twigVariableBag, $dataLayer);
    }
}
