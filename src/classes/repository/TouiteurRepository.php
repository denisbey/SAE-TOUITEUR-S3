<?php

namespace iutnc\touiteur\repository;

class TouiteurRepository
{
    private \PDO $pdo;

    private static ?TouiteurRepository $instance = null;
    private static array $config = [];

    private function __construct()
    {
        $conf = self::$config;

        $dsn = "mysql:host={$conf['host']};"
             . "dbname={$conf['database']};charset=utf8mb4";

        $this->pdo = new \PDO(
            $dsn,
            $conf['username'],
            $conf['password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }

    public static function setConfig(string $file): void
    {
        $conf = parse_ini_file($file);

        if ($conf === false) {
            throw new \Exception(
                "Impossible de lire la configuration BD"
            );
        }

        self::$config = $conf;
        self::$instance = null;
    }

    public static function getInstance(): TouiteurRepository
    {
        if (self::$instance === null) {
            self::$instance = new TouiteurRepository();
        }

        return self::$instance;
    }

    
    public function findInfluencers(): array
    {
    $sql = "SELECT u.pseudo,
                   COUNT(s.id_suiveur) AS abonnes
            FROM utilisateur u
            LEFT JOIN suit_utilisateur s
                ON s.id_suivi = u.id
            GROUP BY u.id, u.pseudo
            ORDER BY abonnes ASC, u.pseudo ASC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

}
