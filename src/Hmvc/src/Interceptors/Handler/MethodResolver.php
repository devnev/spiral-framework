<?php

declare(strict_types=1);

namespace Spiral\Interceptors\Handler;

use Psr\Container\ContainerExceptionInterface;
use Spiral\Core\Exception\Resolver\ArgumentResolvingException;
use Spiral\Core\Exception\Resolver\InvalidArgumentException;
use Spiral\Core\ResolverInterface;
use Spiral\Interceptors\Exception\TargetCallException;

/**
 * @internal
 */
final class MethodResolver
{
    /**
     * @psalm-assert class-string $controller
     * @psalm-assert non-empty-string $action
     *
     * @throws TargetCallException
     */
    public static function pathToReflection(string $controller, string $action): \ReflectionMethod
    {
        try {
            /** @psalm-suppress ArgumentTypeCoercion */
            $method = new \ReflectionMethod($controller, $action);
        } catch (\ReflectionException $e) {
            throw new TargetCallException(
                \sprintf('Invalid action `%s`->`%s`', $controller, $action),
                TargetCallException::BAD_ACTION,
                $e
            );
        }

        return $method;
    }

    /**
     * @throws TargetCallException
     */
    public static function validateControllerMethod(\ReflectionMethod $method): void
    {
        if ($method->isStatic() || !$method->isPublic()) {
            throw new TargetCallException(
                \sprintf(
                    'Invalid action `%s`->`%s`',
                    $method->getDeclaringClass()->getName(),
                    $method->getName(),
                ),
                TargetCallException::BAD_ACTION
            );
        }
    }

    /**
     * @throws TargetCallException
     * @throws \Throwable
     */
    public static function resolveArguments(
        ResolverInterface $resolver,
        \ReflectionMethod $method,
        array $arguments,
    ): array {
        try {
            return $resolver->resolveArguments($method, $arguments);
        } catch (ArgumentResolvingException|InvalidArgumentException $e) {
            throw new TargetCallException(
                \sprintf(
                    'Missing/invalid parameter %s of `%s`->`%s`.',
                    $e->getParameter(),
                    $method->getDeclaringClass()->getName(),
                    $method->getName(),
                ),
                TargetCallException::BAD_ARGUMENT,
                $e,
            );
        } catch (ContainerExceptionInterface $e) {
            throw new TargetCallException(
                $e->getMessage(),
                TargetCallException::ERROR,
                $e,
            );
        }
    }
}
