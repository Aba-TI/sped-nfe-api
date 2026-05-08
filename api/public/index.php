<?php

declare(strict_types=1);

use App\Http\Kernel;
use App\Http\Request;

require dirname(__DIR__, 2) . '/bootstrap.php';

$kernel = new Kernel();
$response = $kernel->handle(Request::capture());
$response->send();
