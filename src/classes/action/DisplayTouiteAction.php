<?php

namespace iutnc\touiteur\action;

use Exception;
use iutnc\touiteur\render\RenderTouite;
use iutnc\touiteur\touite\Touite;

class DisplayTouiteAction extends Action {

    public function execute() : string {
        if ($this->http_method === 'GET') {
            $t = new Touite("Test touite", "idk", "05/11/2026", ["test"]);
            $r = new RenderTouite($t);
            return $r->render(2);
        } else {
            return new \Exception("Touite introuvable");
        }
    }

}