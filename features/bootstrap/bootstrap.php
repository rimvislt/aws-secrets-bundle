<?php
/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

date_default_timezone_set('UTC');
$loader = require __DIR__.'/../../vendor/autoload.php';
require __DIR__.'/Kernel.php';
require __DIR__.'/FeatureContext.php';

return $loader;
