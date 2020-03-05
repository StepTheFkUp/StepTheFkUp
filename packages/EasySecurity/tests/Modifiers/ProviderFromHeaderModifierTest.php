<?php
declare(strict_types=1);

namespace EonX\EasySecurity\Tests\Modifiers;

use EonX\EasySecurity\Context;
use EonX\EasySecurity\Interfaces\ContextInterface;
use EonX\EasySecurity\Interfaces\ProviderInterface;
use EonX\EasySecurity\Interfaces\ProviderProviderInterface;
use EonX\EasySecurity\Modifiers\ProviderFromHeaderModifier;
use EonX\EasySecurity\Role;
use EonX\EasySecurity\Tests\AbstractTestCase;
use EonX\EasySecurity\Tests\Stubs\ProviderInterfaceStub;
use EonX\EasySecurity\Tests\Stubs\ProviderProviderInterfaceStub;
use Symfony\Component\HttpFoundation\Request;

final class ProviderFromHeaderModifierTest extends AbstractTestCase
{
    /**
     * @return iterable<mixed>
     */
    public function modifyProvider(): iterable
    {
        yield 'No provider resolved because no header' => [
            new ProviderProviderInterfaceStub()
        ];

        $request = new Request();
        $request->headers->set('X-Provider-Id', '');

        yield 'No provider resolved because header empty' => [
            new ProviderProviderInterfaceStub(),
            $request
        ];

        $request = new Request();
        $request->headers->set('X-Provider-Id', 'provider-id');

        yield 'No provider resolved because no permission' => [
            new ProviderProviderInterfaceStub(),
            $request
        ];

        $context = new Context();
        $context->setRoles([new Role('app:role', ['provider:switch'])]);

        yield 'No provider resolved because provider provider returns null' => [
            new ProviderProviderInterfaceStub(),
            $request,
            $context
        ];

        yield 'Provider resolved' => [
            new ProviderProviderInterfaceStub($provider = new ProviderInterfaceStub('provider-id')),
            $request,
            $context,
            $provider
        ];
    }

    /**
     * @dataProvider modifyProvider
     */
    public function testModify(
        ProviderProviderInterface $providerProvider,
        ?Request $request = null,
        ?ContextInterface $context = null,
        ?ProviderInterface $provider = null
    ): void {
        $context = $context ?? new Context();

        (new ProviderFromHeaderModifier($providerProvider))->modify($context, $request ?? new Request());

        self::assertSame($provider, $context->getProvider());
    }
}
