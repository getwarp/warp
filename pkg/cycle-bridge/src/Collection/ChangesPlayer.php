<?php

declare(strict_types=1);

namespace spaceonfire\Bridge\Cycle\Collection;

final class ChangesPlayer
{
    /**
     * @template T of object
     * @template P
     * @param \SplObjectStorage<T,P|null> $storage
     * @param array<Change<T,P>> $changes
     * @return \SplObjectStorage<T,P|null>
     */
    public function play(\SplObjectStorage $storage, array $changes): \SplObjectStorage
    {
        if (0 === \count($changes)) {
            return $storage;
        }

        /** @phpstan-var \SplObjectStorage<T,P|null> $output */
        $output = new \SplObjectStorage();
        $output->addAll($storage);

        foreach ($changes as $change) {
            switch ($change->getType()) {
                case Change::ADD:
                    $pivot = $change->getPivot() ?? $storage[$change->getElement()] ?? null;
                    $output->attach($change->getElement(), $pivot);
                    break;

                case Change::REMOVE:
                    $output->detach($change->getElement());
                    break;
            }
        }

        return $output;
    }
}
