<?php

namespace App;

use App\Controllers\ApiBlogController;
use App\Controllers\BlogController;
use App\Controllers\DashboardController;
use App\Controllers\FaqController;
use App\Controllers\HomeController;
use App\Controllers\ProfileController;
use App\Controllers\UserController;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthMiddleware;
use App\Services\AuthService;
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
        $authMiddleware = $container->get(AuthMiddleware::class);
        $adminMiddleware = $container->get(AdminMiddleware::class);

        $router->addMiddleware([$authMiddleware, 'handle']);
        $router->addMiddleware([$adminMiddleware, 'handle']);


        $homeController = $container->get(HomeController::class);
        $router->addRoute('GET', '/', [$homeController, 'index']);


        $userController = $container->get(UserController::class);
        $router->addRoute('GET', '/register', [$userController, 'registerForm']);
        $router->addRoute('POST', '/register', [$userController, 'register']);

        $router->addRoute('GET', '/login', [$userController, 'loginForm']);
        $router->addRoute('POST', '/login', [$userController, 'login']);
        $router->addRoute('POST', '/logout', [$userController, 'logout']);


        $blogController = $container->get(BlogController::class);
        $router->addRoute('GET', '/blogs', [$blogController, 'index']);

        $router->addRoute('GET', '/blogs/(?<id>\d+)', [$blogController, 'show']);
        $router->addRoute('GET', '/blogs/create', [$blogController, 'create']);
        $router->addRoute('POST', '/blogs', [$blogController, 'store']);
        $router->addRoute('POST', '/blogs/(?<id>\d+)/edit', [$blogController, "edit"]);
        $router->addRoute('POST', '/blogs/(?<id>\d+)', [$blogController, "update"]);
        $router->addRoute('POST', '/blogs/(?<id>\d+)/deleteConfirm', [$blogController, "deleteConfirm"]);
        $router->addRoute('POST', '/blogs/(?<id>\d+)/delete', [$blogController, "delete"]);

        $profileController = $container->get(ProfileController::class);
        $router->addRoute('GET', '/profile', [$profileController, 'index']);
        $router->addRoute('GET', '/profile/edit', [$profileController, 'edit']);
        $router->addRoute('POST', '/profile/(?<id>\d+)', [$profileController, 'update']);

        $dashboardController = $container->get(DashboardController::class);
        $router->addRoute('GET', '/dashboard', [$dashboardController, 'index']);
        $router->addRoute('GET', '/dashboard/edit', [$dashboardController, 'edit']);
        $router->addRoute('POST', '/dashboard/update-credits', [$dashboardController, 'updateCredits']);
        $router->addRoute('POST', '/dashboard/update-grade', [$dashboardController, 'updateGrade']);
        $router->addRoute('POST', '/dashboard/add-course', [$dashboardController, 'addCourse']);
        $router->addRoute('POST', '/dashboard/delete-course', [$dashboardController, 'deleteCourse']);


        $faqController = $container->get(FaqController::class);
        $router->addRoute('GET', '/faq', [$faqController, 'index']);

        $apiBlogController = $container->get(ApiBlogController::class);
        $router->addRoute('GET', '/api/blogs', [$apiBlogController, 'blogs']);
        $router->addRoute('GET', '/api/blogs/(?<id>\d+)', [$apiBlogController, 'blog']);
    }
}
