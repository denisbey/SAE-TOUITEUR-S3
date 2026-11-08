<?php

session_start();

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\action\FollowUserAction;
use iutnc\touiteur\action\FollowTagAction;

$dispatcher = new Dispatcher();

$dispatcher->addRoute('default', DefaultAction::class);
$dispatcher->addRoute('follow-user', FollowUserAction::class);
$dispatcher->addRoute('follow-tag', FollowTagAction::class);

$dispatcher->run();
