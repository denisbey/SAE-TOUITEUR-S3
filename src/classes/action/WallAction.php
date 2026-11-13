<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\exception\DatabaseConnectionException;

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

        try {
            $repository = TouiteRepository::getInstance();

            if (!$repository->utilisateurExiste($userId)) {
                return "<p>Utilisateur introuvable.</p>";
            }

            $liste = $repository->mur($userId);

            $html = "<h2>Mur de l'utilisateur $userId</h2>";

            if (count($liste->touites) === 0) {
                return $html . "<p>Aucun touite à afficher.</p>";
            }

            foreach ($liste->touites as $touite) {
                $auteur = htmlspecialchars(
                    $touite->auteur,
                    ENT_QUOTES,
                    'UTF-8'
                );

                $texte = htmlspecialchars(
                    $touite->text,
                    ENT_QUOTES,
                    'UTF-8'
                );

                $html .= "<article>";
                $html .= "<h3>$auteur</h3>";
                $html .= "<p>$texte</p>";
                $html .= "</article>";
            }

            return $html;

        } catch (DatabaseConnectionException | \PDOException $e) {
            return "<p>Erreur lors du chargement du mur.</p>";
        }
    }
}
