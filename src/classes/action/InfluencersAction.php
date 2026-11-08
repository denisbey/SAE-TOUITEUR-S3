<?php

namespace iutnc\touiteur\action;

class InfluencersAction extends Action
{
    public function execute(): string
    {
        // Données temporaires pour tester l'affichage
        // Déjà classées du plus suivi au moins suivi
        $utilisateurs = [
            ['pseudo' => 'Alice', 'abonnes' => 25],
            ['pseudo' => 'Lucas', 'abonnes' => 18],
            ['pseudo' => 'Emma', 'abonnes' => 12],
            ['pseudo' => 'Hugo', 'abonnes' => 7]
        ];

        $html = '<h2>Utilisateurs les plus suivis</h2>';

        $html .= '<table>';
        $html .= '<tr>';
        $html .= '<th>Position</th>';
        $html .= '<th>Utilisateur</th>';
        $html .= '<th>Abonnés</th>';
        $html .= '</tr>';

        $position = 1;

        foreach ($utilisateurs as $utilisateur) {
            $pseudo = htmlspecialchars(
                $utilisateur['pseudo'],
                ENT_QUOTES,
                'UTF-8'
            );

            $abonnes = (int) $utilisateur['abonnes'];

            $html .= '<tr>';
            $html .= '<td>' . $position . '</td>';
            $html .= '<td>' . $pseudo . '</td>';
            $html .= '<td>' . $abonnes . '</td>';
            $html .= '</tr>';

            $position++;
        }

        $html .= '</table>';

        return $html;
    }
}
