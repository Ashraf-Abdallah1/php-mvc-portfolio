<?php

namespace App;

use App\Controllers\AgentController;
use App\Controllers\BrokerController;
use App\Controllers\HomeController;
use App\Controllers\WarrantController;
use Framework\Router;
use Framework\RouteProviderInterface;
use Framework\ServiceContainer;

class RouteProvider implements RouteProviderInterface
{
    /**
     * @throws \Exception
     */
    public function register(Router $router, ServiceContainer $container): void
    {
        $homeController = $container->get(HomeController::class);
        $router->addRoute('GET', '/', [$homeController, "index"]);
        $router->addRoute('GET', '/history', [$homeController, "about"]);

        $brokerController = $container->get(BrokerController::class);
        $router->addRoute('GET', '/brokers', [$brokerController, "index"]);
        $router->addRoute('GET', '/brokers/(?<id>\d+)', [$brokerController, "show"]);
        $router->addRoute('GET', '/brokers/create', [$brokerController, "create"]);
        $router->addRoute('POST', '/brokers', [$brokerController, "store"]);
        $router->addRoute('GET', '/brokers/(?<id>\d+)/edit', [$brokerController, "edit"]);
        $router->addRoute('POST', '/brokers/(?<id>\d+)', [$brokerController, "update"]);
        $router->addRoute('POST', '/brokers/delete/(?<id>\d+)', [$brokerController, "delete"]);

        $warrantController = $container->get(WarrantController::class);
        $router->addRoute('GET', '/warrants/create', [$warrantController, "create"]);

        $agentController = $container->get(AgentController::class);
        $router->addRoute('GET', '/agents', [$agentController, "index"]);
    }
}
