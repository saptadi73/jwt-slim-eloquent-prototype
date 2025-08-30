<?php
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container as Psr11Container;
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Middlewares\CorsMiddleware;
use App\Services\CustomerService;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

$pimple = new PimpleContainer();
$pimple[CustomerService::class] = function ($c) {
    return new CustomerService();
};

$container = new Psr11Container($pimple);

AppFactory::setContainer($container);
$app = AppFactory::create();

$app->addRoutingMiddleware();
$displayError = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
$app->addErrorMiddleware($displayError, true, true);

$app->add(new CorsMiddleware());

$capsule = new Capsule;
// ... (konfigurasi DB sama seperti sebelumnya)

(require __DIR__ . '/../routes/api.php')($app);
return $app;
