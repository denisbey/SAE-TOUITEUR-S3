<?php

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\action\DisplayTouitesAction;
use iutnc\touiteur\action\DisplayTouiteAction;
use iutnc\touiteur\action\DisplayTouitesPersonneAction;
use iutnc\touiteur\action\DisplayTouitesTagAction;
use iutnc\touiteur\dispatch\Dispatcher;

$dispatcher = new Dispatcher();

$dispatcher->addRoute('default', DefaultAction::class);
$dispatcher->addRoute('display-touites', DisplayTouitesAction::class);
$dispatcher->addRoute('display-touite', DisplayTouiteAction::class);
$dispatcher->addRoute('display-touites-auteur', DisplayTouitesPersonneAction::class);
$dispatcher->addRoute('display-touites-tag', DisplayTouitesTagAction::class);

$dispatcher->run();
