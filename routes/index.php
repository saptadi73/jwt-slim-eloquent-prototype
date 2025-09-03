<?php
use Slim\App;

return function (App $app) {
    (require __DIR__ . '/auth.php')($app);
    (require __DIR__ . '/users.php')($app);
    (require __DIR__ . '/customers.php')($app);
    (require __DIR__ . '/workorders.php')($app);
    (require __DIR__ . '/seeds.php')($app);
};
