<?php

declare(strict_types=1);

namespace App\Components;

use Hyperf\Cache\CacheManager;

class Cache extends \Hyperf\Cache\Cache
{
    public function __construct(CacheManager $manager, $driver = 'default')
    {
        $this->driver = $manager->getDriver($driver);
    }
}