<?php

namespace App;

class View
{
    public static function render(string $name, array $data = []): string
    {
        $baseDir    = dirname(__DIR__);
        $viewsDir   = $baseDir . '/views';
        $viewFile   = $viewsDir . '/' . str_replace('.', '/', $name) . '.php';
        $layoutFile = $viewsDir . '/layout.php';

        if (!is_file($viewFile)) {
            http_response_code(500);
            return 'View not found: ' . htmlspecialchars($name);
        }

        extract($data, EXTR_SKIP);

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        if (is_file($layoutFile)) {
            ob_start();
            include $layoutFile;
            return ob_get_clean();
        }

        return $content;
    }
}