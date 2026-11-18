<?php

namespace iutnc\touiteur\action;

class DefaultAction extends Action
{
    public function execute(): string
    {
        return "<h1>Bienvenue sur Touiteur.app !</h1>"
            . "<p>Votre plateforme de publication de touites.</p>"
            . "<a href='?action=display-touites'> Les touites le plus récents </a>";
    }
}
