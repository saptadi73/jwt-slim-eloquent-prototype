<?php
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container as Psr11Container;
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Middlewares\CorsMiddleware;
use App\Services\CustomerService;
use App\Services\OrganisasiService;
use App\Services\PurchaseOrderService;
use App\Services\SaleOrderService;
use App\Services\WorkOrderService;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

$pimple = new PimpleContainer();
$pimple[CustomerService::class] = fn($c) => new CustomerService();
$pimple[WorkOrderService::class] = fn($c) => new WorkOrderService();
$pimple[OrganisasiService::class] = fn($c) => new OrganisasiService();
$pimple[PurchaseOrderService::class] = fn($c) => new PurchaseOrderService();
$pimple[SaleOrderService::class] = fn($c) => new SaleOrderService();
// (opsional) $pimple[CorsMiddleware::class] = fn($c) => new CorsMiddleware();

$container = new Psr11Container($pimple);

AppFactory::setContainer($container);
$app = AppFactory::createFromContainer($container);

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

$displayError = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
$app->addErrorMiddleware($displayError, true, true);

// Preflight CORS (optional but helpful)
$app->options('/{routes:.+}', fn($req, $res) => $res);

// CORS
$app->add(new CorsMiddleware()); // atau $app->add(CorsMiddleware::class);



// Koneksi ke PostgreSQL (langsung, tanpa ENV)
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'pgsql',
    'host'      => '127.0.0.1',
    'database'  => 'erpmini',
    'username'  => 'openpg',
    'password'  => 'openpgpwd',
    'charset'   => 'utf8',
    'prefix'    => '',
    'schema'    => 'public',
    'port'      => 5432,
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

date_default_timezone_set($_ENV['APP_TZ'] ?? 'Asia/Jakarta');

(require __DIR__ . '/../routes/index.php')($app);
return $app;
