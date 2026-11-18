<?php

namespace iutnc\touiteur\action;

use Exception;
use iutnc\touiteur\render\RenderTouite;
use iutnc\touiteur\touite\Touite;

class DisplayTouiteAction extends Action {

    public function execute() : string {
        $id = $_GET['id'];
        if(is_null($id)) { return "<p> id du touite manquant </p>"; }

        $repo = \iutnc\touiteur\repository\TouiteRepository::getInstance();
        $r = new RenderTouite($repo->touite($id));
        return $r->render(2);
    }

}