<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RenderTouites;
use iutnc\touiteur\touite\ListeTouites;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\touite\Tag;

class DisplayTouitesAction extends Action {

    public function execute() : string {
        $repo = \iutnc\touiteur\repository\TouiteRepository::getInstance();
        $r = new RenderTouites($repo->tousLesTouites());
        return $r->render(1);
    }

}