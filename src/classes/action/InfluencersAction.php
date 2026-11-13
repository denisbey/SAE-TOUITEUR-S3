<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteRepository;

class InfluencersAction extends Action
{
    public function execute(): string
    {
        $repository = TouiteRepository::getInstance();
        $utilisateurs = $repository->influenceurs();

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
