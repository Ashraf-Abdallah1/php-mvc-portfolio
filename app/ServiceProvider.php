<?php

namespace App;

use App\Controllers\BlogController;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Repositories\BlogRepository;
use App\Repositories\BlogRepositoryInterface;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryInterface;
use App\Services\AuthService;
use Exception;
use Framework\Database;
use Framework\ResponseFactory;
use Framework\ServiceContainer;
use Framework\ServiceProviderInterface;
use Framework\Session;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * @throws Exception
     */
    public function register(ServiceContainer $container): void
    {

        $responseFactory = $container->get(ResponseFactory::class);
        $database = $container->get(Database::class);


        $userRepository = new UserRepository($database);
        $container->set(UserRepositoryInterface::class, $userRepository);

        $session = new Session();
        $container->set(Session::class, $session);


        $authService = new AuthService($userRepository, $session);
        $container->set(AuthService::class, $authService);


        $authMiddleware = new AuthMiddleware($authService, $responseFactory);
        $container->set(AuthMiddleware::class, $authMiddleware);

        $adminMiddleware = new AdminMiddleware($authService, $responseFactory);
        $container->set(AdminMiddleware::class, $adminMiddleware);

        $blogRepository = new BlogRepository($database);
        $container->set(BlogRepositoryInterface::class, $blogRepository);

        $homeController = new HomeController($responseFactory);
        $container->set(HomeController::class, $homeController);

        $userController = new UserController($responseFactory, $userRepository, $authService);
        $container->set(UserController::class, $userController);

        $blogController = new BlogController($responseFactory, $blogRepository, $authService);
        $container->set(BlogController::class, $blogController);
    }
}
