<?php

namespace iutnc\touiteur\render;

use iutnc\touiteur\render\Renderer;
use iutnc\touiteur\touite\Touite;

class RenderTouite implements Renderer {

    private Touite $touite;

    public function __construct(Touite $t) {
        $this->touite = $t;
    }

    public function renderLong(Touite $touite) : string {
        $r = "<a href='?action=display-touites-auteur&pseudo=$touite->auteur'> $touite->auteur $touite->date </a><br>
        <div name='text'> $touite->text </div><br>
        <img src=$touite->image alt='Image could not load'><br><br>
        <div name='tags'>";
        foreach($touite->tags as $t) {
            $r = $r . "<a href='?action=display-touites-tag&tag=$t->text'> $t->text </a>";
        }  
        $r = $r . "</div>";
        return $r;
    }

    public function render(int $selector) : string {
        return $this->renderLong($this->touite);
    }

}