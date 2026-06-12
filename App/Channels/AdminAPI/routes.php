<?php

use App\Channels\AdminAPI\Controllers\ContractController;



$router->group([
    'prefix' => '/admin',
    'middlewares' => []
], function ($router) {

    $router->get('/contratos/{id}', [ContractController::class, 'show']);

});
