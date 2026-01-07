<?php
use Pimple\Container as PimpleContainer;
use Pimple\Psr11\Container as Psr11Container;
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Pagination\Paginator;
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
use App\Services\AccountingService;
use App\Services\BankAccountService;
use App\Services\ReportService;
use App\Services\ExpenseService;
use App\Services\UserService;
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
$pimple[AccountingService::class] = fn($c) => new AccountingService();
$pimple[PurchaseOrderService::class] = fn($c) => new PurchaseOrderService($pimple[ProductStockService::class], $pimple[AccountingService::class]);
$pimple[SaleOrderService::class] = fn($c) => new SaleOrderService($pimple[ProductStockService::class], $pimple[AccountingService::class]);
$pimple[ChartOfAccountService::class] = fn($c) => new ChartOfAccountService();
$pimple[BankAccountService::class] = fn($c) => new BankAccountService();
$pimple[VendorService::class] = fn($c) => new VendorService();
$pimple[ServiceService::class] = fn($c) => new ServiceService();
$pimple[ReportService::class] = fn($c) => new ReportService();
$pimple[ExpenseService::class] = fn($c) => new ExpenseService();
$pimple[UserService::class] = fn($c) => new UserService();
// (opsional) $pimple[CorsMiddleware::class] = fn($c) => new CorsMiddleware();

$container = new Psr11Container($pimple);

AppFactory::setContainer($container);
$app = AppFactory::createFromContainer($container);

// LOAD ROUTES PERTAMA (sebelum middleware)
(require __DIR__ . '/../routes/index.php')($app);

$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();

$displayError = ($_ENV['APP_DEBUG'] ?? 'false') === 'true';
$app->addErrorMiddleware($displayError, true, true);



// Koneksi ke PostgreSQL (menggunakan ENV)
$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => $_ENV['DB_DRIVER'] ?? 'pgsql',
    'host'      => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'database'  => $_ENV['DB_NAME'] ?? 'erpmini',
    'username'  => $_ENV['DB_USERNAME'] ?? 'openpg',
    'password'  => $_ENV['DB_PASSWORD'] ?? 'openpgpwd',
    'charset'   => $_ENV['DB_CHARSET'] ?? 'utf8',
    'prefix'    => $_ENV['DB_PREFIX'] ?? '',
    'schema'    => $_ENV['DB_SCHEMA'] ?? 'public',
    'port'      => (int)($_ENV['DB_PORT'] ?? 5432),
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Setup Paginator untuk mendukung pagination di Eloquent
Paginator::currentPathResolver(function () {
    return isset($_SERVER['REQUEST_URI']) ? strtok($_SERVER['REQUEST_URI'], '?') : '/';
});

Paginator::currentPageResolver(function ($pageName = 'page') {
    return isset($_GET[$pageName]) ? (int) $_GET[$pageName] : 1;
});

date_default_timezone_set($_ENV['APP_TZ'] ?? 'Asia/Jakarta');

// Method Override Middleware (untuk PUT request dengan multipart/form-data)
$app->add(new MethodOverrideMiddleware());

// CORS Middleware (harus di paling akhir / luar)
$app->add(new CorsMiddleware());

return $app;
