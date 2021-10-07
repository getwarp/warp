<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Mapper;

use Cycle\ORM\Command\CommandInterface;
use Cycle\ORM\Command\ContextCarrierInterface;
use Cycle\ORM\Heap\Node;
use Cycle\ORM\Heap\State;
use Cycle\ORM\Mapper\Mapper;
use Cycle\ORM\MapperInterface;
use Cycle\ORM\ORMInterface;
use Laminas\Hydrator\HydratorInterface;
use Laminas\Hydrator\NamingStrategy\IdentityNamingStrategy;
use Laminas\Hydrator\NamingStrategy\NamingStrategyEnabledInterface;
use Laminas\Hydrator\NamingStrategy\NamingStrategyInterface;
use spaceonfire\DataSource\IdenticalPropertyExtractor;
use spaceonfire\DataSource\LaminasPropertyExtractor;
use spaceonfire\DataSource\PropertyExtractorInterface;

class HydratorMapper extends Mapper
{
    private MapperPluginInterface $plugin;

    public function __construct(
        ORMInterface $orm,
        string $role,
        ?HydratorInterface $hydrator = null,
        ?MapperPluginInterface $plugin = null
    ) {
        parent::__construct($orm, $role);

        $this->hydrator = $hydrator ?? $this->hydrator;
        $this->plugin = $plugin ?? new Plugin\NoopMapperPlugin();
    }

    /**
     * @inheritDoc
     * @param array<string,mixed> $data
     */
    public function hydrate($entity, array $data): object
    {
        /** @var Plugin\HydrateBeforeEvent $event */
        $event = $this->plugin->dispatch(new Plugin\HydrateBeforeEvent($entity, $data));

        $entity = $event->getEntity();
        $data = $event->getData();

        parent::hydrate($entity, $data);

        /** @var Plugin\HydrateAfterEvent $event */
        $event = $this->plugin->dispatch(new Plugin\HydrateAfterEvent($entity, $data));

        return $event->getEntity();
    }

    /**
     * @inheritDoc
     * @return array<string,mixed>
     */
    public function extract($entity): array
    {
        /** @var Plugin\ExtractBeforeEvent $event */
        $event = $this->plugin->dispatch(new Plugin\ExtractBeforeEvent($entity));

        $entity = $event->getEntity();

        $extracted = parent::extract($entity);

        /** @var Plugin\ExtractAfterEvent $event */
        $event = $this->plugin->dispatch(new Plugin\ExtractAfterEvent($entity, $extracted));

        return $event->getData();
    }

    public function queueCreate($entity, Node $node, State $state): ContextCarrierInterface
    {
        /** @var Plugin\QueueBeforeEvent $event */
        $event = $this->plugin->dispatch(new Plugin\QueueBeforeEvent($entity, $node, $state));

        $entity = $event->getEntity();
        $node = $event->getNode();
        $state = $event->getState();

        $insert = parent::queueCreate($entity, $node, $state);

        /** @var Plugin\QueueAfterEvent $event */
        $event = $this->plugin->dispatch(new Plugin\QueueAfterEvent($entity, $node, $state, $insert));

        $command = $event->getCommand();
        \assert($command instanceof ContextCarrierInterface);
        return $command;
    }

    public function queueUpdate($entity, Node $node, State $state): ContextCarrierInterface
    {
        /** @var Plugin\QueueBeforeEvent $event */
        $event = $this->plugin->dispatch(new Plugin\QueueBeforeEvent($entity, $node, $state));

        $entity = $event->getEntity();
        $node = $event->getNode();
        $state = $event->getState();

        $update = parent::queueUpdate($entity, $node, $state);

        /** @var Plugin\QueueAfterEvent $event */
        $event = $this->plugin->dispatch(new Plugin\QueueAfterEvent($entity, $node, $state, $update));

        $command = $event->getCommand();
        \assert($command instanceof ContextCarrierInterface);
        return $command;
    }

    public function queueDelete($entity, Node $node, State $state): CommandInterface
    {
        /** @var Plugin\QueueBeforeEvent $event */
        $event = $this->plugin->dispatch(new Plugin\QueueBeforeEvent($entity, $node, $state));

        $entity = $event->getEntity();
        $node = $event->getNode();
        $state = $event->getState();

        $delete = parent::queueDelete($entity, $node, $state);

        /** @var Plugin\QueueAfterEvent $event */
        $event = $this->plugin->dispatch(new Plugin\QueueAfterEvent($entity, $node, $state, $delete));

        return $event->getCommand();
    }

    public static function getPropertyExtractor(?MapperInterface $mapper): PropertyExtractorInterface
    {
        if ($mapper instanceof Mapper) {
            return new LaminasPropertyExtractor($mapper->hydrator);
        }

        return new IdenticalPropertyExtractor();
    }

    public static function getNamingStrategy(?MapperInterface $mapper): NamingStrategyInterface
    {
        if ($mapper instanceof Mapper && $mapper->hydrator instanceof NamingStrategyEnabledInterface) {
            return $mapper->hydrator->getNamingStrategy();
        }

        return new IdentityNamingStrategy();
    }
}
