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
            $r = $r . "<a href='?action=display-touites-auteur&pseudo=$lt->auteur'> $lt->auteur </a>
            <div name='date'> $lt->date </div>
            <div name='tags'> Tags: ";
            foreach($lt->tags as $t) {
                $r = $r . "<a href='?action=display-touites-tag&tag=$t->text'> $t->text </a>";
            }
            $r = $r . "</div><a href='?action=display-touite&id=$lt->id'> voir detail touite </a><br><br>";
        }
        return $r;
    }

    public function render(int $selector) : string {
        return $this->renderCompact($this->touites);
    }

}