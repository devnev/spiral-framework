<?php

declare(strict_types=1);

namespace Spiral\Console;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Spiral\Console\Interceptor\AttributeInterceptor;
use Spiral\Core\Attribute\Scope;
use Spiral\Core\CoreInterceptorInterface;
use Spiral\Core\CoreInterface;
use Spiral\Core\InterceptableCore;

#[Scope('console.command')]
final class CommandCoreFactory
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    /**
     * @param array<class-string<CoreInterceptorInterface>> $interceptors
     */
    public function make(array $interceptors, ?EventDispatcherInterface $eventDispatcher = null): CoreInterface
    {
        /** @var CommandCore $core */
        $core = $this->container->get(CommandCore::class);

        $interceptableCore = new InterceptableCore($core, $eventDispatcher);

        foreach ($interceptors as $interceptor) {
            $interceptableCore->addInterceptor($this->container->get($interceptor));
        }
        $interceptableCore->addInterceptor($this->container->get(AttributeInterceptor::class));

        return $interceptableCore;
    }
}