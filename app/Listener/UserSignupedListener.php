<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\UserSignuped;
use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class UserSignupedListener implements ListenerInterface
{
    public function __construct(protected ContainerInterface $container)
    {
    }

    public function listen(): array
    {
        return [
            UserSignuped::class
        ];
    }

    public function process(object $event): void
    {
    }
}
