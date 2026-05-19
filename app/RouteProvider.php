<?php

namespace App;

use App\Controllers\BlogController;
use App\Controllers\HomeController;
use App\Controllers\ProjectController;
use App\Controllers\TagController;
use App\Controllers\TaskController;
use App\Controllers\UserController;
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
        $router->addRoute('GET', '/', [$homeController, 'index']);

        $userController = $container->get(UserController::class);
        $router->addRoute('GET', '/register', [$userController, 'registerForm']);
        $router->addRoute('POST', '/register', [$userController, 'register']);

        $router->addRoute('GET', '/login', [$userController, 'loginForm']);
        $router->addRoute('POST', '/login', [$userController, 'login']);


        $blogController = $container->get(BlogController::class);
        $router->addRoute('GET', '/blogs', [$blogController, 'index']);
        $router->addRoute('GET', '/blogs/(?<id>\d+)', [$blogController, 'show']);
        $router->addRoute('GET', '/blogs/create', [$blogController, 'create']);
        $router->addRoute('POST', '/blogs', [$blogController, 'store']);
        $router->addRoute('POST', '/blogs/(?<id>\d+)/edit', [$blogController, "edit"]);
        $router->addRoute('POST', '/blogs/(?<id>\d+)', [$blogController, "update"]);
        $router->addRoute('POST', '/blogs/(?<id>\d+)/deleteConfirm', [$blogController, "deleteConfirm"]);
        $router->addRoute('POST', '/blogs/(?<id>\d+)/delete', [$blogController, "delete"]);

    }
}
