<?php

namespace iutnc\touiteur\action;

class PublishTouiteAction extends Action
{
    private const LONGUEUR_MAX = 235;

    public function execute(): string
    {
        if ($this->http_method === 'GET') {
            return $this->formulaire('', '', []);
        }

        $pseudo = $this->champ('pseudo');
        $texte = $this->champ('texte');
        $erreurs = [];

        if ($pseudo === '') {
            $erreurs[] = "Le pseudo est obligatoire.";
        }
        if ($texte === '') {
            $erreurs[] = "Le texte du touite est obligatoire.";
        } elseif (strlen($texte) > self::LONGUEUR_MAX) {
            $erreurs[] = "Le touite ne doit pas dépasser " . self::LONGUEUR_MAX
                . " caractères (vous en avez " . strlen($texte) . ").";
        }

        if ($erreurs !== []) {
            return $this->formulaire($pseudo, $texte, $erreurs);
        }

        return $this->confirmation($this->nettoyer($pseudo), $this->nettoyer($texte));
    }

    // Lit un champ envoyé en POST : renvoie '' s'il est absent ou invalide (ex. un tableau)
    private function champ(string $nom): string
    {
        if (!isset($_POST[$nom]) || filter_var($_POST[$nom], FILTER_SANITIZE_SPECIAL_CHARS) === false) {
            return '';
        }
        return trim($_POST[$nom]);
    }

    // Filtre de nettoyage du cours : les caractères spéciaux (< > " ' &) deviennent affichables
    private function nettoyer(string $valeur): string
    {
        return filter_var($valeur, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    // Les valeurs reçues ici sont déjà nettoyées
    private function confirmation(string $pseudo, string $texte): string
    {
        $adresse = $this->nettoyer($this->script_name) . '?action=publish';

        return <<<HTML
        <h2>Touite validé</h2>
        <p><strong>$pseudo</strong></p>
        <p>$texte</p>
        <p><a href="$adresse">Publier un autre touite</a></p>
        HTML;
    }

    private function formulaire(string $pseudo, string $texte, array $erreurs): string
    {
        $adresse = $this->nettoyer($this->script_name) . '?action=publish';
        $pseudo = $this->nettoyer($pseudo);
        $texte = $this->nettoyer($texte);

        $messages = '';
        foreach ($erreurs as $erreur) {
            $messages .= "<li>$erreur</li>";
        }
        if ($messages !== '') {
            $messages = "<ul class=\"erreurs\">$messages</ul>";
        }

        return <<<HTML
        <h2>Publier un touite</h2>
        $messages
        <form method="post" action="$adresse">
            <p>
                <label for="pseudo">Pseudo :</label><br>
                <input type="text" id="pseudo" name="pseudo" value="$pseudo">
            </p>
            <p>
                <label for="texte">Votre touite (235 caractères maximum) :</label><br>
                <textarea id="texte" name="texte" rows="5" cols="50">$texte</textarea>
            </p>
            <p><button type="submit">Publier</button></p>
        </form>
        HTML;
    }
}
