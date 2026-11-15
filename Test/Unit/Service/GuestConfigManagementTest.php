<?php
declare(strict_types=1);

namespace MageSuite\CheckoutNewsletterSubscription\Test\Unit\Service;

class GuestConfigManagementTest extends \PHPUnit\Framework\TestCase
{
    protected array $config = [];

    protected ?\MageSuite\CheckoutNewsletterSubscription\Service\GuestConfigManagement $guestConfigManagement;

    protected function setUp(): void
    {
        $scopeConfig = $this->createStub(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->willReturnCallback(
            fn (string $path): bool => in_array($path, $this->config, true)
        );

        $this->guestConfigManagement = new \MageSuite\CheckoutNewsletterSubscription\Service\GuestConfigManagement(
            $this->createStub(\Magento\Framework\Serialize\SerializerInterface::class),
            $this->createStub(\Magento\Store\Model\StoreManagerInterface::class),
            $this->createStub(\Magento\Newsletter\Model\SubscriberFactory::class),
            new \MageSuite\CheckoutNewsletterSubscription\Helper\Configuration($scopeConfig)
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('guestVisibilityPaths')]
    public function testItReadsGuestVisibilityFromGuestConfig(int $status, string $guestPath, string $loggedInPath): void
    {
        $this->config = [$guestPath];
        $this->assertTrue($this->guestConfigManagement->isVisibleForGuest($this->getSubscriber($status)));

        $this->config = [$loggedInPath];
        $this->assertFalse($this->guestConfigManagement->isVisibleForGuest($this->getSubscriber($status)));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('guestCheckedPaths')]
    public function testItReadsGuestCheckedStateFromGuestConfig(int $status, string $guestPath, string $loggedInPath): void
    {
        $this->config = [$guestPath];
        $this->assertTrue($this->guestConfigManagement->isCheckedForGuest($this->getSubscriber($status)));

        $this->config = [$loggedInPath];
        $this->assertFalse($this->guestConfigManagement->isCheckedForGuest($this->getSubscriber($status)));
    }

    public static function guestVisibilityPaths(): array
    {
        return [
            'subscribed' => [
                \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED,
                'checkout_newsletter_subscription/guest_customer/is_visible_for_subscribed',
                'checkout_newsletter_subscription/logged_in_customer/is_visible_for_subscribed',
            ],
            'not subscribed' => [
                \Magento\Newsletter\Model\Subscriber::STATUS_UNSUBSCRIBED,
                'checkout_newsletter_subscription/guest_customer/is_visible',
                'checkout_newsletter_subscription/logged_in_customer/is_visible',
            ],
        ];
    }

    public static function guestCheckedPaths(): array
    {
        return [
            'unsubscribed' => [
                \Magento\Newsletter\Model\Subscriber::STATUS_UNSUBSCRIBED,
                'checkout_newsletter_subscription/guest_customer/is_checked_for_unsubscribed',
                'checkout_newsletter_subscription/logged_in_customer/is_checked_for_unsubscribed',
            ],
            'not active' => [
                \Magento\Newsletter\Model\Subscriber::STATUS_NOT_ACTIVE,
                'checkout_newsletter_subscription/guest_customer/is_checked_for_not_active',
                'checkout_newsletter_subscription/logged_in_customer/is_checked_for_not_active',
            ],
            'default' => [
                \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED,
                'checkout_newsletter_subscription/guest_customer/is_checked_by_default',
                'checkout_newsletter_subscription/logged_in_customer/is_checked_by_default',
            ],
        ];
    }

    protected function getSubscriber(int $status): \Magento\Newsletter\Model\Subscriber
    {
        $subscriber = $this->getMockBuilder(\Magento\Newsletter\Model\Subscriber::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $subscriber->setData('subscriber_status', $status);

        return $subscriber;
    }
}
