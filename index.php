<?php

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\action\InfluencersAction;
use iutnc\touiteur\repository\TouiteurRepository;

$dispatcher = new Dispatcher();

$dispatcher->addRoute('default', DefaultAction::class);

$dispatcher->addRoute('statistiques', InfluencersAction::class);

TouiteurRepository::setConfig(
    dirname(__DIR__) . '/touiteur.config.ini'
);

$dispatcher->run();
