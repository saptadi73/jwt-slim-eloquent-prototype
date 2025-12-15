<?php
use Slim\App;

return function (App $app) {
    (require __DIR__ . '/auth.php')($app);
    (require __DIR__ . '/users.php')($app);
    (require __DIR__ . '/customers.php')($app);
    (require __DIR__ . '/workorders.php')($app);
    (require __DIR__ . '/seeds.php')($app);
    (require __DIR__ . '/orders.php')($app);
    (require __DIR__ . '/chart_of_accounts.php')($app);
    (require __DIR__ . '/vendors.php')($app);
    (require __DIR__ . '/products.php')($app);
    (require __DIR__ . '/kategoris.php')($app);
    (require __DIR__ . '/brands.php')($app);
    (require __DIR__ . '/satuans.php')($app);
};
