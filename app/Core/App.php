<?php

class App
{
    protected $controller = 'Dashboard';
    protected $method = 'index';
    protected $params = [];

    public static $current_url = '';

    public function __construct()
    {
        $url = $this->parseUrl();

        self::$current_url =
            rtrim($_GET['url'] ?? '', '/');

     spl_autoload_register(function ($class) {
    $paths = [
        '../app/Controllers/',
        '../app/Models/',
        '../app/Services/'
    ];

    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});
        // --------------------
        // ROUTING
        // --------------------

        $controller_name =
            !empty($url[0])
            ? $this->controllerFromUrl($url[0])
            : 'Dashboard';

        $controller_file =
            '../app/Controllers/' . $controller_name . '.php';

        require_once '../app/Core/Controller.php';

        if (file_exists($controller_file)) {

            require_once $controller_file;

            $this->controller =
                new $controller_name();
        } else {

            require_once '../app/Controllers/Dashboard.php';

            $this->controller =
                new Dashboard();
        }

        if (
            isset($url[1]) &&
            method_exists($this->controller, $url[1])
        ) {

            $this->method = $url[1];

            unset($url[0], $url[1]);
        } else {

            $this->method = 'index';

            unset($url[0]);
        }

        $this->params =
            $url ? array_values($url) : [];

        call_user_func_array(
            [$this->controller, $this->method],
            $this->params
        );
    }

    private function controllerFromUrl($segment)
    {
        return str_replace(
            ' ',
            '',
            ucwords(
                str_replace('-', ' ', $segment)
            )
        );
    }

    public function parseUrl()
    {
        if (isset($_GET['url'])) {
            return array_filter(
                explode('/', rtrim($_GET['url'], '/'))
            );
        }

        return [];
    }

}
