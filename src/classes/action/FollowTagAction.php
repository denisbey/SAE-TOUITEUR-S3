<?php

namespace iutnc\touiteur\action;

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
                <input type="text" name="tag" required>

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

            if (!$userId || $userId < 1 || $tag === '') {
                return "<p>Données invalides.</p>";
            }

            $tag = filter_var(
                $tag,
                FILTER_SANITIZE_SPECIAL_CHARS
            );

            if (!isset($_SESSION['follow_tags'])) {
                $_SESSION['follow_tags'] = [];
            }

            $_SESSION['follow_tags'][$userId][$tag] = true;

            return "<p>Abonnement au hashtag enregistré !</p>";
        }

        return "<p>Méthode non autorisée.</p>";
    }
}
