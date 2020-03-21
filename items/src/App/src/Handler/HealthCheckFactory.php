<?php
namespace App\Handler;

use Psr\Container\ContainerInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HealthCheckFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new HealthCheck($container->get('config')['mysql']);
    }
}

