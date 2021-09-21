<?php

declare(strict_types=1);

namespace spaceonfire\MonologBridge\Handler;

use Laminas\Hydrator\HydratorInterface;

abstract class AbstractHandlerFactory implements HandlerFactoryInterface
{
    protected function getParametersHydrator(): ?HydratorInterface
    {
        return null;
    }

    /**
     * @param array<string,mixed> $parameters
     * @return mixed|object
     */
    final protected function hydrateParameters(array $parameters)
    {
        if ($hydrator = $this->getParametersHydrator()) {
            return $hydrator->hydrate($parameters, new \stdClass());
        }

        return (object)$parameters;
    }
}
