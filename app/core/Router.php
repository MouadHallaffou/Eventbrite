<?php
namespace App\core;

use App\core\Controller;

class Router extends Controller {
    protected $routes = [];

    private function addRoute($route, $controller, $action, $method) {
        $this->routes[$method][$route] = ['controller' => $controller, 'action' => $action];
    }

    public function get($route, $controller, $action) {
        $this->addRoute($route, $controller, $action, "GET");
    }

    public function post($route, $controller, $action) {
        $this->addRoute($route, $controller, $action, "POST");
    }

    public function dispatch()
    {
    $uri = strtok($_SERVER['REQUEST_URI'], '?');
    $method = $_SERVER['REQUEST_METHOD'];

    foreach ($this->routes[$method] as $route => $handler) {
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>\d+)', $route);
        $pattern = "@^" . $pattern . "$@";

        if (preg_match($pattern, $uri, $matches)) {
            $controller = $handler['controller'];
            $action = $handler['action'];

            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

            (new $controller())->$action(...array_values($params));
            return;
        }
    }
    echo "Page non trouvée";
    }
    
}
