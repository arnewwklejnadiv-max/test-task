<?php

declare(strict_types=1);

use App\Bootstrap;
use App\Http\Kernel;
use App\Http\Request;

require dirname(__DIR__) . '/vendor/autoload.php';

Bootstrap::container()->get(Kernel::class)->handle(Request::fromGlobals())->send();
