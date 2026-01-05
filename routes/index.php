<?php
use Slim\App;

return function (App $app) {
    (require __DIR__ . '/auth.php')($app);
    (require __DIR__ . '/users.php')($app);
    (require __DIR__ . '/roles.php')($app);
    (require __DIR__ . '/customers.php')($app);
    (require __DIR__ . '/workorders.php')($app);
    (require __DIR__ . '/seeds.php')($app);
    (require __DIR__ . '/orders.php')($app);
    (require __DIR__ . '/chart_of_accounts.php')($app);
    (require __DIR__ . '/bank_accounts.php')($app);
    (require __DIR__ . '/accounting.php')($app);
    (require __DIR__ . '/vendors.php')($app);
    (require __DIR__ . '/products.php')($app);
    (require __DIR__ . '/kategoris.php')($app);
    (require __DIR__ . '/brands.php')($app);
    (require __DIR__ . '/satuans.php')($app);
    (require __DIR__ . '/services.php')($app);
    (require __DIR__ . '/expenses.php')($app);
    (require __DIR__ . '/reports.php')($app);
    (require __DIR__ . '/positions.php')($app);
    (require __DIR__ . '/departments.php')($app);
    (require __DIR__ . '/groups.php')($app);
    (require __DIR__ . '/pegawai.php')($app);
    (require __DIR__ . '/timeoffs.php')($app);
    (require __DIR__ . '/attendances.php')($app);
    (require __DIR__ . '/tanda_tangan.php')($app);
};
