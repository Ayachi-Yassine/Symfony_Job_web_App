<?php

namespace App\Twig;

use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouteExistsExtension extends AbstractExtension
{
    public function __construct(private RouterInterface $router) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('route_exists', [$this, 'routeExists']),
        ];
    }

    public function routeExists(string $name): bool
    {
        return (bool) $this->router->getRouteCollection()->get($name);
    }
}
