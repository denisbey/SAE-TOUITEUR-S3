<?php

namespace iutnc\touiteur\repository;

use iutnc\touiteur\exception\DatabaseConnectionException;
use iutnc\touiteur\exception\DataNotFoundException;
use iutnc\touiteur\touite\ListeTouites;
use iutnc\touiteur\touite\Tag;
use iutnc\touiteur\touite\Touite;

/**
 * Toutes les requetes SQL de l'application (modele Repository + Singleton).
 * Utilisation : TouiteRepository::getInstance()->tousLesTouites();
 *
 * Les listes de touites sont des ListeTouites dont les CLES sont les identifiants
 * des touites : foreach ($liste->touites as $id => $touite) { ... }
 */
class TouiteRepository
{
    private \PDO $pdo;
    private static ?TouiteRepository $instance = null;
    private static array $config = [];

    private const SELECT_TOUITE = "SELECT t.id, t.texte, t.date_pub, t.image, u.pseudo
        FROM touite t INNER JOIN utilisateur u ON u.id = t.id_utilisateur";
    private const ORDRE_RECENT = " ORDER BY t.date_pub DESC, t.id DESC";

    private function __construct(array $conf)
    {
        try {
            $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'],
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
        } catch (\PDOException $e) {
            throw new DatabaseConnectionException("Connexion à la base de données impossible.");
        }
        $this->pdo->prepare('SET NAMES \'UTF8\'')->execute();
    }

    public static function getInstance(): TouiteRepository
    {
        if (is_null(self::$instance)) {
            if (self::$config === []) {
                // fichier de configuration a la racine du projet (jamais dans Git)
                self::setConfig(__DIR__ . '/../../../config.db.ini');
            }
            self::$instance = new TouiteRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $fichier): void
    {
        if (!is_file($fichier)) {
            throw new DatabaseConnectionException("Fichier de configuration de la base introuvable.");
        }
        $conf = parse_ini_file($fichier);
        if ($conf === false) {
            throw new DatabaseConnectionException("Fichier de configuration de la base illisible.");
        }
        self::$config = [
            'dsn' => $conf['driver'] . ':host=' . $conf['host'] . ';dbname=' . $conf['database'],
            'user' => $conf['username'],
            'pass' => $conf['password']
        ];
    }

    /* ------------------------------------------------------------ lecture */

    // F1 + F3 : tous les touites, du plus recent au plus ancien, page par page
    public function tousLesTouites(int $page = 1, int $parPage = 10): ListeTouites
    {
        $liste = $this->liste(self::SELECT_TOUITE . self::ORDRE_RECENT);
        $debut = ($page - 1) * $parPage;
        return new ListeTouites(array_slice($liste->touites, $debut, $parPage, true));
    }

    public function nombreDeTouites(): int
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) AS nb FROM touite");
        $stmt->execute();
        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $ligne['nb'];
    }

    // F2 : un touite
    public function touite(int $id): Touite
    {
        $stmt = $this->pdo->prepare(self::SELECT_TOUITE . " WHERE t.id = ?");
        $stmt->execute([$id]);
        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($ligne === false) {
            throw new DataNotFoundException("Touite introuvable.");
        }
        return $this->versTouite($ligne);
    }

    // F4 : les touites d'un utilisateur
    public function touitesDe(string $pseudo): ListeTouites
    {
        return $this->liste(self::SELECT_TOUITE . " WHERE u.pseudo = ?" . self::ORDRE_RECENT, [$pseudo]);
    }

    // F5 : les touites qui portent un tag
    public function touitesDuTag(string $tag): ListeTouites
    {
        $sql = self::SELECT_TOUITE . " WHERE t.id IN (
                SELECT tt.id_touite FROM touite_tag tt
                INNER JOIN tag g ON g.id = tt.id_tag WHERE g.libelle = ?)" . self::ORDRE_RECENT;
        return $this->liste($sql, [$tag]);
    }

    // F9 : le mur = touites des utilisateurs suivis + touites des tags suivis
    public function mur(int $idUtilisateur): ListeTouites
    {
        $sql = self::SELECT_TOUITE . " WHERE t.id_utilisateur IN (
                SELECT id_suivi FROM suit_utilisateur WHERE id_suiveur = ?)
            OR t.id IN (
                SELECT tt.id_touite FROM touite_tag tt WHERE tt.id_tag IN (
                    SELECT id_tag FROM suit_tag WHERE id_utilisateur = ?))" . self::ORDRE_RECENT;
        return $this->liste($sql, [$idUtilisateur, $idUtilisateur]);
    }

    /* ------------------------------------------------------- utilisateurs */

    // Liste des utilisateurs : tableau de ['id' => ..., 'pseudo' => ...]
    public function utilisateurs(): array
    {
        $stmt = $this->pdo->prepare("SELECT id, pseudo FROM utilisateur ORDER BY pseudo");
        $stmt->execute();
        $resultat = [];
        while ($ligne = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $resultat[] = ['id' => (int) $ligne['id'], 'pseudo' => $ligne['pseudo']];
        }
        return $resultat;
    }

    public function idUtilisateur(string $pseudo): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM utilisateur WHERE pseudo = ?");
        $stmt->execute([$pseudo]);
        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($ligne === false) {
            throw new DataNotFoundException("Pseudo inconnu.");
        }
        return (int) $ligne['id'];
    }

    public function utilisateurExiste(int $id): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM utilisateur WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) !== false;
    }

    /* ----------------------------------------------------------- ecriture */

    // F6 + F8 + F13 : enregistre un touite et ses tags ; renvoie l'identifiant du touite.
    // $touite->auteur = pseudo (doit exister), $touite->image = '' ou chemin (ex. uploads/xxx.png)
    public function publier(Touite $touite): int
    {
        $idAuteur = $this->idUtilisateur($touite->auteur);
        $image = $touite->image === '' ? null : $touite->image;

        $stmt = $this->pdo->prepare(
            "INSERT INTO touite (texte, date_pub, image, id_utilisateur) VALUES (?, ?, ?, ?)");
        $stmt->execute([$touite->text, $touite->date, $image, $idAuteur]);
        $idTouite = (int) $this->pdo->lastInsertId();

        $dejaLies = [];
        foreach ($touite->tags as $tag) {
            $libelle = ($tag instanceof Tag) ? $tag->text : $tag;
            $idTag = $this->idDuTag($libelle);
            if (!in_array($idTag, $dejaLies)) {
                $dejaLies[] = $idTag;
                $stmt = $this->pdo->prepare("INSERT INTO touite_tag (id_touite, id_tag) VALUES (?, ?)");
                $stmt->execute([$idTouite, $idTag]);
            }
        }
        return $idTouite;
    }

    // F10 : suivre un utilisateur (sans effet si on le suit deja)
    public function suivreUtilisateur(int $idSuiveur, int $idSuivi): void
    {
        if (!$this->utilisateurExiste($idSuiveur) || !$this->utilisateurExiste($idSuivi)) {
            throw new DataNotFoundException("Utilisateur inconnu.");
        }
        $stmt = $this->pdo->prepare(
            "SELECT id_suiveur FROM suit_utilisateur WHERE id_suiveur = ? AND id_suivi = ?");
        $stmt->execute([$idSuiveur, $idSuivi]);
        if ($stmt->fetch(\PDO::FETCH_ASSOC) === false) {
            $stmt = $this->pdo->prepare(
                "INSERT INTO suit_utilisateur (id_suiveur, id_suivi) VALUES (?, ?)");
            $stmt->execute([$idSuiveur, $idSuivi]);
        }
    }

    // F11 : s'abonner a un tag (sans effet si on y est deja abonne)
    public function suivreTag(int $idUtilisateur, string $tag): void
    {
        if (!$this->utilisateurExiste($idUtilisateur)) {
            throw new DataNotFoundException("Utilisateur inconnu.");
        }
        $idTag = $this->idDuTag($tag);
        $stmt = $this->pdo->prepare(
            "SELECT id_tag FROM suit_tag WHERE id_utilisateur = ? AND id_tag = ?");
        $stmt->execute([$idUtilisateur, $idTag]);
        if ($stmt->fetch(\PDO::FETCH_ASSOC) === false) {
            $stmt = $this->pdo->prepare("INSERT INTO suit_tag (id_utilisateur, id_tag) VALUES (?, ?)");
            $stmt->execute([$idUtilisateur, $idTag]);
        }
    }

    // F7 : like (1) ou dislike (-1) ; renvoie false si la note est invalide ou deja donnee
    public function noter(int $idUtilisateur, int $idTouite, int $valeur): bool
    {
        if ($valeur !== 1 && $valeur !== -1) {
            return false;
        }
        if (!$this->utilisateurExiste($idUtilisateur)) {
            throw new DataNotFoundException("Utilisateur inconnu.");
        }
        $this->touite($idTouite); // leve DataNotFoundException si le touite n'existe pas

        $stmt = $this->pdo->prepare("SELECT valeur FROM note WHERE id_utilisateur = ? AND id_touite = ?");
        $stmt->execute([$idUtilisateur, $idTouite]);
        if ($stmt->fetch(\PDO::FETCH_ASSOC) !== false) {
            return false;
        }
        $stmt = $this->pdo->prepare(
            "INSERT INTO note (id_utilisateur, id_touite, valeur) VALUES (?, ?, ?)");
        $stmt->execute([$idUtilisateur, $idTouite, $valeur]);
        return true;
    }

    /* --------------------------------------------------------- statistiques */

    // F12 : pseudos des utilisateurs qui suivent $idUtilisateur
    public function suiveursDe(int $idUtilisateur): array
    {
        $stmt = $this->pdo->prepare("SELECT u.pseudo FROM suit_utilisateur s
            INNER JOIN utilisateur u ON u.id = s.id_suiveur WHERE s.id_suivi = ? ORDER BY u.pseudo");
        $stmt->execute([$idUtilisateur]);
        $resultat = [];
        while ($ligne = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $resultat[] = $ligne['pseudo'];
        }
        return $resultat;
    }

    // F12 : moyenne des notes recues par les touites d'un utilisateur (null s'il n'a aucune note)
    public function scoreMoyen(int $idUtilisateur): ?float
    {
        $stmt = $this->pdo->prepare("SELECT AVG(n.valeur) AS moyenne FROM note n
            INNER JOIN touite t ON t.id = n.id_touite WHERE t.id_utilisateur = ?");
        $stmt->execute([$idUtilisateur]);
        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $ligne['moyenne'] === null ? null : (float) $ligne['moyenne'];
    }

    // F14 : utilisateurs et nombre de suiveurs, par ordre croissant du nombre de suiveurs
    public function influenceurs(): array
    {
        $stmt = $this->pdo->prepare("SELECT u.pseudo, COUNT(s.id_suiveur) AS abonnes
            FROM utilisateur u LEFT JOIN suit_utilisateur s ON s.id_suivi = u.id
            GROUP BY u.id, u.pseudo ORDER BY abonnes ASC, u.pseudo");
        $stmt->execute();
        $resultat = [];
        while ($ligne = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $resultat[] = ['pseudo' => $ligne['pseudo'], 'abonnes' => (int) $ligne['abonnes']];
        }
        return $resultat;
    }

    // F15 : tags et nombre de touites, par ordre croissant du nombre de mentions
    public function tendances(): array
    {
        $stmt = $this->pdo->prepare("SELECT g.libelle, COUNT(tt.id_touite) AS nb
            FROM tag g INNER JOIN touite_tag tt ON tt.id_tag = g.id
            GROUP BY g.id, g.libelle ORDER BY nb ASC, g.libelle");
        $stmt->execute();
        $resultat = [];
        while ($ligne = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $resultat[] = ['tag' => $ligne['libelle'], 'nb' => (int) $ligne['nb']];
        }
        return $resultat;
    }

    /* ------------------------------------------------------- outils prives */

    // Execute une requete SELECT_TOUITE et renvoie une ListeTouites (cles = identifiants)
    private function liste(string $sql, array $params = []): ListeTouites
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $lignes = [];
        while ($ligne = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $lignes[] = $ligne;
        }
        $touites = [];
        foreach ($lignes as $ligne) {
            $touites[(int) $ligne['id']] = $this->versTouite($ligne);
        }
        return new ListeTouites($touites);
    }

    private function versTouite(array $ligne): Touite
    {
        $image = $ligne['image'] === null ? '' : $ligne['image'];
        return new Touite($ligne['texte'], $ligne['pseudo'], $ligne['date_pub'],
            $this->tagsDuTouite((int) $ligne['id']), $image);
    }

    private function tagsDuTouite(int $idTouite): array
    {
        $stmt = $this->pdo->prepare("SELECT g.libelle FROM touite_tag tt
            INNER JOIN tag g ON g.id = tt.id_tag WHERE tt.id_touite = ? ORDER BY g.libelle");
        $stmt->execute([$idTouite]);
        $tags = [];
        while ($ligne = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tags[] = new Tag($ligne['libelle']);
        }
        return $tags;
    }

    // Identifiant d'un tag : on le cree s'il n'existe pas encore
    private function idDuTag(string $libelle): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM tag WHERE libelle = ?");
        $stmt->execute([$libelle]);
        $ligne = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($ligne !== false) {
            return (int) $ligne['id'];
        }
        $stmt = $this->pdo->prepare("INSERT INTO tag (libelle) VALUES (?)");
        $stmt->execute([$libelle]);
        return (int) $this->pdo->lastInsertId();
    }
}
