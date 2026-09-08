<?php
require_once '../app/Core/ServiceContainer.php';
require_once '../config/constants.php';

class Controller
{
    protected ServiceContainer $services;

    protected $settings;

    // GLOBAL AUTH CHECK
   public function __construct()
{
    /*
    |---------------------------------------------
    | Initialize Service Container
    |---------------------------------------------
    */

    $this->services = new ServiceContainer();

    /*
    |-------------------------------------
    | Global Authentication Check
    |-------------------------------------
    */

    if (!isset($_SESSION['user_id'])) {

        $allowed = ['auth'];

        $currentController = strtolower($_GET['url'] ?? 'home');
        $controller = explode('/', $currentController)[0];

        if (!in_array($controller, $allowed)) {

            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }
}

    public function service(string $service)
    {
        return $this->services->make($service);
    }

    public function model($model)
    {

        $model_class = $model . 'Model';  // example, Inventory → InventoryModel
        $model_file = '../app/Models/' . $model_class . '.php';

        if (file_exists($model_file)) {
            require_once $model_file;
            return new $model_class();
        }

        // Fallback - try original name
        $model_file = '../app/Models/' . $model . '.php';
        if (file_exists($model_file)) {
            require_once $model_file;
            return new $model();
        }

        return null;
    }

    public function view($view, $data = [], $useLayout = true)
    {

        $viewPath = '../app/Views/' . $view . '.php';

        if (file_exists($viewPath)) {

            extract($data);

            // make cusomized company settings globally available
            $settingsModel = $this->model('Settings');
            $settings = $settingsModel->get();

            if ($useLayout) {
                require_once '../app/Views/header.php';
                require_once $viewPath;
                require_once '../app/Views/footer.php';
            } else {
                // ✅ LOAD VIEW ONLY (NO HEADER / FOOTER), print view 
                require_once $viewPath;
            }
        } else {
            require_once '../app/Views/errors/404.php';
        }
    }
}
