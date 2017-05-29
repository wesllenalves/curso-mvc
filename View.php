<?php

namespace SIGA;

/**
 * Description of View
 *
 * @author Claudio Campos
 */
class View {

    private $action;
    protected $configs;

    /**
     * $view
     * @var Services\Container
     */
    protected $view;
    protected $subfolder;
    protected $controller;
    protected $layout = "layout";
    protected $vars;
    protected $html;

    public function __construct($config, $subfolder, $controller, $action) {
        $this->view = new Services\Container();
        $this->html = $this->view->resolveClass('App\Base\Helpers\Views\Tags\Html');
        $this->subfolder = $subfolder;
        $this->configs = $config;
        $this->action = str_replace("Action", "", $action);
        $this->controller = strtolower(str_replace([
            "App", "Controllers", "Controller", $subfolder, "\\"
                        ], '', $controller));
        if ($this->controller == "notfound"):
            $this->subfolder = "Base";
            $this->action = "404";
        endif;
    }

    public function render() {
        if ($this->layout && file_exists(sprintf("%s/%s/Base/views/layout/%s.phtml", ROOT_PATH, APP_PATH, $this->layout))):
            include sprintf("%s/%s/Base/views/layout/%s.phtml", ROOT_PATH, APP_PATH, $this->layout);
        else:
            $this->content();
        endif;
    }

    protected function content() {
        include sprintf("%s/%s/%s/views/%s/%s.phtml", ROOT_PATH, APP_PATH, $this->subfolder, $this->controller, $this->action);
    }

    public function getAction() {
        return $this->action;
    }

    public function getLayout() {
        return $this->layout;
    }

    public function getVars() {
        return $this->vars;
    }

    public function setAction($action) {
        $this->action = $action;
        return $this;
    }

    public function setLayout($layout) {
        $this->layout = $layout;
        return $this;
    }

    public function setVars($vars) {
        $this->vars = $vars;
        return $this;
    }

    protected function getView($class = "App\Base\Helpers\Views\Render", $dependecies_inject = []) {
        return $this->view->resolveClass($class, $dependecies_inject);
    }

}
