<?php

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\action\PublishTouiteAction;
use iutnc\touiteur\dispatch\Dispatcher;

$dispatcher = new Dispatcher();

$dispatcher->addRoute('default', DefaultAction::class);
$dispatcher->addRoute('publish', PublishTouiteAction::class);

$dispatcher->run();
