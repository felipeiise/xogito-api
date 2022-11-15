<?php

declare(strict_types=1);

namespace App;

use Exception;

/**
 * @author Felipe Iise Mendes
 * Class to handle all the requests to the application
 */
class Router
{
    private object $request;

    /**
     * @param object $request
     */
    public function __construct(object $request)
    {
        $this->request = $request;
    }

    /**
     * @throws Exception
     */
    public function matchRouteUri($routes)
    {
        $request_uri = rtrim($_SERVER['REQUEST_URI'], '/') . '/';
        $method = $_SERVER['REQUEST_METHOD'];
        $allowed_vars = ['id', 'name'];
        $search = ['/\//', '/{id}/', '/{id\?}/', '/{name}/', '/{name\?}/'];
        $replace = ['\/', '(\d+)', '?(\d+)?', '([a-z]*)', '?([a-z]*)?'];

        $not_found = true;
        foreach ($routes as $pattern => $route) {
            $pattern = explode('::', $pattern);
            $action = $pattern[0];
            $pattern_uri = $pattern[1];
            $route_pattern = preg_replace($search, $replace, rtrim($pattern_uri, '/'));
            if (preg_match('/^' . $route_pattern . '.$/im', $request_uri) &&
                (strtoupper($method) === strtoupper($action) || (strtoupper($method) === 'GET' && $action === 'view'))
            ) {
                $not_found = false;
                $uri_vars = array_filter(explode('/', $request_uri));
                $route_vars = array_filter(explode('/', $pattern_uri));
                $extracted_vars = $this->getCustomVars(
                    $uri_vars,
                    $route_vars,
                    $allowed_vars
                );
                $this->dispatch($action, $route, $extracted_vars);
            }
        }
        if ($not_found) {
            $this->dispatch('view', ['controller' => '/404'], []);
        }
    }

    /**
     * Extract custom variables passed in API calls
     *
     * @param array $uri_vars
     * @param array $route_vars
     * @param array $allowed_vars
     * @return array
     */
    private function getCustomVars(
        array $uri_vars,
        array $route_vars,
        array $allowed_vars
    ): array
    {
        $variables = [];
        foreach ($uri_vars as $uri_key => $uri_value) {
            $variable_name = preg_replace('/[^a-z]/','', $route_vars[$uri_key]);
            if (in_array($variable_name, $allowed_vars)) {
                $variables[$variable_name] = $uri_value;
            }
        }
        return $variables;
    }

    /**
     * @param string $action
     * @param array $route
     * @param array $extracted_variables
     */
    private function dispatch(string $action, array $route, array $extracted_variables)
    {
        if ($action === 'view') {
            $protected = isset($route['protected']) && $route['protected'];
            if ($protected) {
                $this->protectRoute();
            }
            $view_file = 'views/' . $route['controller'] . '.php';
            try {
                if (!file_exists($view_file)) {
                    throw new Exception ($view_file .' does not exist.');
                }
                require_once($view_file);
                exit();
            } catch(Exception $exception) {
                echo 'Message: ' . $exception->getMessage();
            }
        }
        $controller = explode('@', $route['controller']);
        $class = '\\App\\' . $controller[0];
        $method = $controller[1];
        $controller = new $class();
        echo $controller->$method($this->request, $extracted_variables);
    }

    /**
     * Validates request with header JWT
     *
     * @return void
     */
    private function protectRoute(): void
    {
        $user_object = new User();
        $response = $user_object->getUser();

        if ($response['status'] === 'invalid_login') {
            header('Location: /signin');
            exit();
        }

        if ($response['roles']) {
            define('USER_ROLES', $response['roles']);
        }
    }
}