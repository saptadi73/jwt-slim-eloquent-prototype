<?php
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container as Psr11Container;
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use App\Middlewares\CorsMiddleware;
use App\Middlewares\MethodOverrideMiddleware;
use App\Services\ChartOfAccountService;
use App\Services\CustomerService;
use App\Services\OrganisasiService;
use App\Services\ProductStockService;
use App\Services\PurchaseOrderService;
use App\Services\SaleOrderService;
use App\Services\ProductService;
use App\Services\KategoriService;
use App\Services\BrandService;
use App\Services\SatuanService;
use App\Services\VendorService;
use App\Services\WorkOrderService;
use App\Services\ServiceService;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

$pimple = new PimpleContainer();
$pimple[ProductStockService::class] = fn($c) => new ProductStockService();
$pimple[ProductService::class] = fn($c) => new ProductService();
$pimple[KategoriService::class] = fn($c) => new KategoriService();
$pimple[BrandService::class] = fn($c) => new BrandService();
$pimple[SatuanService::class] = fn($c) => new SatuanService();
$pimple[CustomerService::class] = fn($c) => new CustomerService();
$pimple[WorkOrderService::class] = fn($c) => new WorkOrderService();
$pimple[OrganisasiService::class] = fn($c) => new OrganisasiService();
$pimple[PurchaseOrderService::class] = fn($c) => new PurchaseOrderService($pimple[ProductStockService::class]);
$pimple[SaleOrderService::class] = fn($c) => new SaleOrderService($pimple[ProductStockService::class]);
$pimple[ChartOfAccountService::class] = fn($c) => new ChartOfAccountService();
$pimple[VendorService::class] = fn($c) => new VendorService();
$pimple[ServiceService::class] = fn($c) => new ServiceService();
// (opsional) $pimple[CorsMiddleware::class] = fn($c) => new CorsMiddleware();

$container = new Psr11Container($pimple);

AppFactory::setContainer($container);
$app = AppFactory::createFromContainer($container);

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

$displayError = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
$app->addErrorMiddleware($displayError, true, true);

// Method Override Middleware (untuk PUT request dengan multipart/form-data)
$app->add(new MethodOverrideMiddleware());

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
