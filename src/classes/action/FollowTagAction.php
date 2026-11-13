<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;
use iutnc\touiteur\exception\DataNotFoundException;
use iutnc\touiteur\exception\DatabaseConnectionException;

class FollowTagAction extends Action
{
    public function execute(): string
    {
        if ($this->http_method === 'GET') {
            return <<<HTML
            <h2>S'abonner à un hashtag</h2>

            <form method="post" action="index.php?action=follow-tag">
                <label>Votre identifiant :</label>
                <input type="number" name="user_id" min="1" required>

                <label>Hashtag :</label>
                <input type="text" name="tag" maxlength="100" required>

                <button type="submit">S'abonner</button>
            </form>
            HTML;
        }

        if ($this->http_method === 'POST') {
            $userId = filter_input(
                INPUT_POST,
                'user_id',
                FILTER_VALIDATE_INT
            );

            $tag = trim($_POST['tag'] ?? '');
            $tag = ltrim($tag, '#');

            if (!$userId || $userId < 1 || $tag === '' || strlen($tag) > 100) {
                return "<p>Données invalides.</p>";
            }

            try {
                $repository = TouiteRepository::getInstance();
                $repository->suivreTag($userId, $tag);

                return "<p>Abonnement au hashtag enregistré !</p>";
            } catch (DataNotFoundException $e) {
                return "<p>Utilisateur introuvable.</p>";
            } catch (DatabaseConnectionException | \PDOException $e) {
                return "<p>Erreur lors de l'abonnement.</p>";
            }
        }

        return "<p>Méthode non autorisée.</p>";
    }
}
