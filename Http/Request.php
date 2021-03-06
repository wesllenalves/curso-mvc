<?php

namespace SIGA\Http;

/**
 * Description of Request
 *
 * @author Claudio Campos
 */
class Request {

    protected $namespace = APP_PATH;
    protected $subfoder = "Home";
    protected $controller = "IndexController";
    protected $action = "indexAction";
    public $params = [];

    public function __construct(string $baseURI = '', string $controller_dir = '', string $controller_notFound = '') {
        $this->init($baseURI, $controller_dir, $controller_notFound);
    }

    public function init(string $baseURI = '', string $controller_dir = '', string $controller_notFound = '') {
        if (!empty($baseURI) && !empty($controller_dir)):
            $requestURI = array_values(array_filter(explode("/", filter_input(INPUT_SERVER, "REQUEST_URI"))));
            $this->namespace = sprintf("\\%s\\%s\\Controllers\\", APP_PATH, \SIGA\Utils::getName($this->subfoder));

            $baseURICOUNT = count(array_filter(explode("/", $baseURI)));

            if (count($requestURI) == $baseURICOUNT):
                return $this;
            endif;

            if (count($requestURI) != $baseURICOUNT):
                for ($i = 0; $i < $baseURICOUNT; $i++) {
                    unset($requestURI[$i]);
                }
                $requestURI = array_values($requestURI);
            endif;
            $controller_dir = sprintf($controller_dir, \SIGA\Utils::getName($requestURI[0]));
            $this->setController($requestURI);
            $this->namespace = sprintf("\\%s\\%s\\Controllers\\", APP_PATH, \SIGA\Utils::getName($requestURI[0]));
            $module = sprintf("%s/%s", ROOT_PATH, $controller_dir);
            /*
             * Verifica se existe o modulo
             */
            if (!is_dir($module)):
                throw new \SIGA\Exception\PHPErrorException(1,
                        "O Modulo {$controller_dir} not found!",
                                __FILE__,'47');
            endif;
            $this->subfoder = \SIGA\Utils::getName($requestURI[0]);
            /*
             * Verificar se o controller existe
             */
            if (!file_exists(sprintf("%s/%s/%s.php", ROOT_PATH, $controller_dir, $this->controller))):
                $this->controller = $controller_notFound;
            endif;
            /*
             * Verificar se existe uma ação
             */
            $this->setAction($requestURI);
            $this->params = array_values($requestURI);
            return $this;
        endif;
    }

    protected function setController($requestURI) {
        if (isset($requestURI[1])):
            $this->controller = sprintf("%sController", $requestURI[1]);
        endif;
    }

    public function getController() {
        return $this->controller;
    }

    public function setAction($requestURI) {
        if (isset($requestURI[2])) {
            $this->action = lcfirst(sprintf("%sAction", \SIGA\Utils::getName($requestURI[2])));
        }
    }

    public function getAction() {
        return $this->action;
    }

    public function getNameSpace() {
        return $this->namespace;
    }

    public function getSubFolder() {
        return $this->subfoder;
    }

    public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        echo 'Into ' . __FUNCTION__ . '() at line ' . __LINE__ .
        "\n\n---ERRNO---\n" . print_r($errno, true) .
        "\n\n---ERRSTR---\n" . print_r($errstr, true) .
        "\n\n---ERRFILE---\n" . print_r($errfile, true) .
        "\n\n---ERRLINE---\n" . print_r($errline, true) .
        "\n\n---ERRCONTEXT---\n" . print_r($errcontext, true) .
        "\n\nBacktrace of errorHandler()\n" .
        print_r(debug_backtrace(), true);
    }

}
