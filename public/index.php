<?php
declare(strict_types=1);

use App\WebAPI\App;

require __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../src/WebAPI/App.php';

$app = new App();
$app->run();