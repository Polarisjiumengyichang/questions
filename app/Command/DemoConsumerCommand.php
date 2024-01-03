<?php

declare(strict_types=1);

namespace App\Command;

use App\Amqp\Consumer\DemoConsumer;
use App\Amqp\Consumer\DemoConsumer2;
use Hyperf\Amqp\Consumer;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Di\Annotation\AnnotationCollector;
use Psr\Container\ContainerInterface;
use Hyperf\Amqp\Annotation\Consumer as ConsumerAnnotation;

/**
 * @Command
 */
#[Command]
class DemoConsumerCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('DemoConsumer:command');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('手动启动消费进程测试');
    }

    public function handle()
    {
        $consumer = $this->container->get(Consumer::class);
        $consumer->consume(make(DemoConsumer2::class));

        $this->line('ok.', 'info');
    }
}
