<?php

namespace iutnc\touiteur\action;

class PublishTouiteAction extends Action
{
    public function execute(): string
    {
        return $this->formulaire();
    }

    private function formulaire(): string
    {
        $adresse = $this->script_name . '?action=publish';

        return <<<HTML
        <h2>Publier un touite</h2>
        <form method="post" action="$adresse">
            <p>
                <label for="pseudo">Pseudo :</label><br>
                <input type="text" id="pseudo" name="pseudo">
            </p>
            <p>
                <label for="texte">Votre touite (235 caractères maximum) :</label><br>
                <textarea id="texte" name="texte" rows="5" cols="50"></textarea>
            </p>
            <p><button type="submit">Publier</button></p>
        </form>
        HTML;
    }
}
