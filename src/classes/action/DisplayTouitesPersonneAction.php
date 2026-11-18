<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RenderTouites;
use iutnc\touiteur\touite\ListeTouites;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\touite\Tag;

class DisplayTouitesPersonneAction extends Action {

    public function execute() : string {
        $pseudo = $_GET['pseudo'];
        if(is_null($pseudo)) { return "<p> pseudo de la personne manquant </p>"; }

        $repo = \iutnc\touiteur\repository\TouiteRepository::getInstance();
        $r = new RenderTouites($repo->touitesDe($pseudo));
        return $r->render(1);
    }

}