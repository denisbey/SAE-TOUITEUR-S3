<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\touite\ListeTouites;

class RenderTouites implements Renderer {

    private ListeTouites $touites;

    public function __construct(ListeTouites $ts) {
        $this->touites = $ts;
    }

    public function renderCompact(ListeTouites $listeTouite) : string {
        $r = " ";
        foreach($listeTouite->touites as $lt) {
            $r = $r . "<div name='auteur'> $lt->auteur </div>
            <div name='date'> $lt->date </div>
            <div name='tags'> Tags: ";
            foreach($lt->tags as $t) {
                $r = $r . "$t->text ";
            }
            $r = $r . "</div><br>";
        }
        return $r;
    }

    public function render(int $selector) : string {
        return $this->renderCompact($this->touites);
    }

}