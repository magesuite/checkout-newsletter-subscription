<?php

namespace MageSuite\CheckoutNewsletterSubscription\Test\Integration\Plugin\Magento\Checkout\Api;

class PaymentInformationManagementInterfaceTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $pluginName = 'newsletter_subscription_checkout_model_payment_information_management';

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
    }

    /**
     * @return array
     */
    protected function getPluginInfo()
    {
        /** @var \Magento\TestFramework\Interception\PluginList $pluginList */
        $pluginList = $this->objectManager->create(\Magento\TestFramework\Interception\PluginList::class);
        return $pluginList->get(\Magento\Checkout\Api\PaymentInformationManagementInterface::class, []);
    }

    public function testPluginIsConfiguredToWrapOtherClasses()
    {
        $pluginInfo = $this->getPluginInfo();
        $this->assertSame(
            \MageSuite\CheckoutNewsletterSubscription\Plugin\Magento\Checkout\Api\PaymentInformationManagementInterface::class,
            $pluginInfo[$this->pluginName]['instance']
        );
    }
}
