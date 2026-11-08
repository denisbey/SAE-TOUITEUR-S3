<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\model\Touite;

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

        $tags = $this->extraireTags($texte);
        $touite = new Touite($this->nettoyer($pseudo), $this->nettoyer($texte), $tags);

        return $this->confirmation($touite);
    }

    // Cherche les mots qui commencent par # et renvoie la liste des tags (sans le #)
    private function extraireTags(string $texte): array
    {
        $mots = explode(' ', str_replace(["\r", "\n"], ' ', $texte));
        $tags = [];

        foreach ($mots as $mot) {
            if (strlen($mot) > 1 && $mot[0] === '#') {
                $tag = $this->nettoyer(trim(substr($mot, 1), '.,;:!?'));
                if ($tag !== '' && !in_array($tag, $tags)) {
                    $tags[] = $tag;
                }
            }
        }

        return $tags;
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

    // Les valeurs de l'objet Touite sont déjà nettoyées
    private function confirmation(Touite $touite): string
    {
        $adresse = $this->nettoyer($this->script_name) . '?action=publish';

        $tags = '';
        foreach ($touite->tags as $tag) {
            $tags .= '#' . $tag . ' ';
        }
        if ($tags === '') {
            $tags = 'aucun';
        }

        return <<<HTML
        <h2>Touite validé</h2>
        <p><strong>{$touite->pseudo}</strong> - {$touite->date}</p>
        <p>{$touite->texte}</p>
        <p>Hashtags détectés : $tags</p>
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
