<?php
declare(strict_types=1);

namespace LoyaltyCorp\EasyIdentity\Tests\Implementations\Auth0;

use Auth0\SDK\API\Management;
use Mockery\MockInterface;
use LoyaltyCorp\EasyIdentity\Implementations\Auth0\Config;
use LoyaltyCorp\EasyIdentity\Implementations\Auth0\ManagementApiClientFactory;
use LoyaltyCorp\EasyIdentity\Implementations\Auth0\ManagementTokenProvider;
use LoyaltyCorp\EasyIdentity\Tests\AbstractTestCase;

/**
 * @covers \LoyaltyCorp\EasyIdentity\Implementations\Auth0\ManagementApiClientFactory
 */
class ManagementApiClientFactoryTest extends AbstractTestCase
{
    /**
     * Factory should request a management token and return the expected instance of management api client.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\EasyIdentity\Exceptions\RequiredDataMissingException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testCreate(): void
    {
        /** @var \LoyaltyCorp\EasyIdentity\Implementations\Auth0\ManagementTokenProvider $tokenProvider */
        $tokenProvider = $this->mock(ManagementTokenProvider::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getToken')->once()->withNoArgs()->andReturn('access_token');
        });

        self::assertInstanceOf(
            Management::class,
            (new ManagementApiClientFactory(new Config(['domain' => 'domain']), $tokenProvider))->create()
        );
    }
}

\class_alias(
    ManagementApiClientFactoryTest::class,
    'StepTheFkUp\EasyIdentity\Tests\Implementations\Auth0\ManagementApiClientFactoryTest',
    false
);
