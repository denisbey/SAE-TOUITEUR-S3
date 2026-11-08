
<?php

namespace iutnc\touiteur\action;

class FollowUserAction extends Action
{
    public function execute(): string
    {
        if ($this->http_method === 'GET') {
            return <<<HTML
            <h2>Suivre un utilisateur</h2>
            <form method="post" action="index.php?action=follow-user">
                <label>Votre identifiant :</label>
                <input type="number" name="user_id" min="1" required>

                <label>Identifiant de l'utilisateur à suivre :</label>
                <input type="number" name="followed_id" min="1" required>

                <button type="submit">Suivre</button>
            </form>
            HTML;
        }

        if ($this->http_method === 'POST') {
            $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
            $followedId = filter_input(INPUT_POST, 'followed_id', FILTER_VALIDATE_INT);

            if (!$userId || !$followedId || $userId < 1 || $followedId < 1) {
                return "<p>Identifiants invalides.</p>";
            }

            if ($userId === $followedId) {
                return "<p>Vous ne pouvez pas vous suivre vous-même.</p>";
            }

            if (!isset($_SESSION['follow_users'])) {
                $_SESSION['follow_users'] = [];
            }

            $_SESSION['follow_users'][$userId][$followedId] = true;

            return "<p>Utilisateur suivi avec succès !</p>";
        }

        return "<p>Méthode non autorisée.</p>";
    }
}
