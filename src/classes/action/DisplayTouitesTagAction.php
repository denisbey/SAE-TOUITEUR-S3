<?php 

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RenderTouites;
use iutnc\touiteur\touite\ListeTouites;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\touite\Tag;

class DisplayTouitesTagAction extends Action {

    public function execute() : string {
        $tag = $_GET['tag'];
        if(is_null($tag)) { return "<p> tag manquant </p>"; }

        $repo = \iutnc\touiteur\repository\TouiteRepository::getInstance();
        $r = new RenderTouites($repo->touitesDuTag($tag));
        return $r->render(1);
    }

}