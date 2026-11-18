<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\render\RenderTouites;
use iutnc\touiteur\touite\ListeTouites;
use iutnc\touiteur\touite\Touite;
use iutnc\touiteur\touite\Tag;

class DisplayTouitesAction extends Action {

    public function execute() : string {
        $tag1 = new Tag("test");
        $tag2 = new Tag("dev");
        $touite1 = new Touite("Test touite", "Me", "04/11/2026", [$tag1, $tag2]);
        $touite2 = new Touite("Annother test", "Me", "05/11/2026", [$tag1, $tag2]);
        $r = new RenderTouites(new ListeTouites([$touite1, $touite2]));
        return $r->render(1);
    }

}