<?php

session_start();

require_once __DIR__ . '/vendor/autoload.php';

use iutnc\touiteur\action\DefaultAction;
use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\action\FollowUserAction;
use iutnc\touiteur\action\FollowTagAction;
use iutnc\touiteur\action\WallAction;

$dispatcher = new Dispatcher();

$dispatcher->addRoute('default', DefaultAction::class);
$dispatcher->addRoute('follow-user', FollowUserAction::class);
$dispatcher->addRoute('follow-tag', FollowTagAction::class);
$dispatcher->addRoute('wall', WallAction::class);

$dispatcher->run();
