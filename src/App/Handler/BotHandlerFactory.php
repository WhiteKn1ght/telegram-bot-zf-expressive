<?php

declare(strict_types=1);

namespace App\Handler;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\ServiceManager\Config;
use Zend\ServiceManager\ConfigInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Tooling\Factory\ConfigInjector;

class BotHandlerFactory
{
    public function __invoke(ContainerInterface $container) : RequestHandlerInterface
    {

        $config   = $container->get('config');
        return new BotHandler($config, get_class($container));
    }
}
