<?php

namespace iutnc\touiteur\action;

class WallAction extends Action
{
    public function execute(): string
    {
        if ($this->http_method !== 'GET') {
            return "<p>Méthode non autorisée.</p>";
        }

        $userId = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);

        if (!$userId || $userId < 1) {
            return <<<HTML
            <h2>Mon mur</h2>

            <form method="get" action="index.php">
                <input type="hidden" name="action" value="wall">

                <label>Votre identifiant :</label>
                <input type="number" name="user_id" min="1" required>

                <button type="submit">Afficher mon mur</button>
            </form>
            HTML;
        }

        $usersSuivis = $_SESSION['follow_users'][$userId] ?? [];
        $tagsSuivis = $_SESSION['follow_tags'][$userId] ?? [];

        // Données temporaires en attendant la base de données
        // Les touites sont classés du plus récent au plus ancien
        $touites = [
            [
                'auteur_id' => 3,
                'auteur' => 'Alice',
                'texte' => 'Je découvre le PHP ! #informatique',
                'tags' => ['informatique']
            ],
            [
                'auteur_id' => 2,
                'auteur' => 'Bob',
                'texte' => 'Bonjour tout le monde ! #bonjour',
                'tags' => ['bonjour']
            ],
            [
                'auteur_id' => 4,
                'auteur' => 'Lucas',
                'texte' => 'Nouveau projet en PHP #informatique',
                'tags' => ['informatique']
            ]
        ];

        $html = "<h2>Mur de l'utilisateur $userId</h2>";
        $nombreTouites = 0;

        foreach ($touites as $touite) {
            $afficher = isset($usersSuivis[$touite['auteur_id']]);

            foreach ($touite['tags'] as $tag) {
                if (isset($tagsSuivis[$tag])) {
                    $afficher = true;
                }
            }

            if ($afficher) {
                $auteur = filter_var(
                    $touite['auteur'],
                    FILTER_SANITIZE_SPECIAL_CHARS
                );

                $texte = filter_var(
                    $touite['texte'],
                    FILTER_SANITIZE_SPECIAL_CHARS
                );

                $html .= "<article>";
                $html .= "<h3>$auteur</h3>";
                $html .= "<p>$texte</p>";
                $html .= "</article>";

                $nombreTouites++;
            }
        }

        if ($nombreTouites === 0) {
            $html .= "<p>Aucun touite à afficher.</p>";
        }

        return $html;
    }
}
