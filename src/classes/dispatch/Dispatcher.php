<?php

namespace iutnc\touiteur\dispatch;

class Dispatcher
{
    private string $action;
    private array $routes = [];

    public function __construct()
    {
        $this->action = $_GET['action'] ?? 'default';
    }

    public function addRoute(string $action, string $class): self
    {
        $this->routes[$action] = $class;
        return $this;
    }

    public function run(): void
    {
        if (!isset($this->routes[$this->action])) {
            http_response_code(404);
            $this->renderPage("<h1>Page introuvable</h1>");
            return;
        }

        $class = $this->routes[$this->action];
        $action = new $class();

        $html = $action->execute();

        $this->renderPage($html);
    }

    private function renderPage(string $html): void
    {
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Touiteur.app</title>
            <link rel="stylesheet" href="css/style.css">
        </head>
        <body>
            <header>
                <h1>Touiteur.app</h1>
                <nav>
                    <a href="index.php?action=default">Accueil</a>
                </nav>
            </header>

            <main>
                $html
            </main>
        </body>
        </html>
        HTML;
    }
}
