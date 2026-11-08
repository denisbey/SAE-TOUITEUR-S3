<?php

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\action\InfluencersAction;

$dispatcher = new Dispatcher();

$dispatcher->addRoute('default', DefaultAction::class);

$dispatcher->addRoute('statistiques', InfluencersAction::class);

$dispatcher->run();
