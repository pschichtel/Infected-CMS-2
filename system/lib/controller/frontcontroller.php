<?php

    /**
     * Events
     */
    EventManager::registerEvent(new Event('onBeforeControllerExec'));
    EventManager::registerEvent(new Event('onAfterControllerExec'));
    /**
     * Dependencies
     */
    require_once ICMS_SYS_PATH . 'lib/controller/abstractcontroller.php';

    /**
     *
     */
    final class Frontcontroller
    {
        //private static $instance = null;

        public $controllerPath;
        
        public function __construct()
        {
            $this->controllerPath = '';
        }

        public function __destruct()
        {}

        //private function  __clone()
        //{}

        public static function &getInstance()
        {
            if (self::$instance === null)
            {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function setControllerPath($path)
        {
            if (!preg_match('/(\/|\\\)$/', $path))
            {
                $path .= '/';
            }
            $this->controllerPath = $path;
        }

        public function getControllerPath()
        {
            return $this->controllerPath;
        }

        public function run(IRequest $request, Response $response)
        {
            $controller = $request->getController();
            $action = 'action_' . $request->getAction();

            $controllerPath = $this->controllerPath . $controller . '/controller.php';
            $controller = ucfirst(strtolower($controller)) . 'Controller';

            if (file_exists($controllerPath))
            {
                require_once $controllerPath;
                if (class_exists($controller))
                {
                    $controller = new $controller($request, $response);
                    if ($controller instanceof  AbstractController)
                    {
                        try
                        {
                            if (is_callable(array($controller, $action)))
                            {
                                $controller->$action();
                            }
                            else
                            {
                                $controller->action_index();
                            }
                        }
                        catch (ControllerException $e)
                        {
                            echo $e->getMessage();
                        }
                        catch (Exception $e)
                        {
                            throw new Exception($e->getMessage(), $e->getCode());
                        }
                    }
                    else
                    {
                        throw new Exception("invalid controller!\ncontrollers have to extend AbstractController");
                    }
                }
                else
                {
                    throw new Exception("controller class not found!\n$controller");
                }
                
            }
            else
            {
                throw new Exception("controller file not found!\n$controllerPath");
            }
        }
    }
?>
