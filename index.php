<?php

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\action\FollowUserAction;

$dispatcher = new Dispatcher();

$dispatcher->addRoute('default', DefaultAction::class);

$dispatcher->run();
