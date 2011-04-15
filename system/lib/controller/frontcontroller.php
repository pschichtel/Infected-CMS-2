<?php

    /**
     * Events
     */
    EventManager::registerEvent(new Event('onBeforeControllerExec'));
    EventManager::registerEvent(new Event('onAfterControllerExec'));

    /**
     * The Frontcontroller which runs the requested page
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

        public function run(IRequest $request, IResponse $response)
        {
            $controller = $request->getController();
            $action = 'action_' . $request->getAction();
            $controllerPath = '';
            
            if (!empty($this->controllerPath))
            {
                $controllerPath = $this->controllerPath;
            }
            elseif (Registry::exists('paths.controllers'))
            {
                $controllerPath = rtrim(Registry::get('paths.controllers'), '/\\') . '/';
            }
            else
            {
                throw new ControllerException('No valid controller path was found!', 404);
            }

            $controllerPath .= $controller . '/controller.php';
            $controller = ucfirst(strtolower($controller)) . 'Controller';

            if (is_readable($controllerPath))
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
                            unset($controller);
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
                        throw new ControllerException("Invalid controller!\ncontrollers have to extend AbstractController");
                    }
                }
                else
                {
                    throw new ControllerException("Controller class not found!\n$controller");
                }
                
            }
            else
            {
                throw new ControllerException("The controller was not found or is not readable!\n$controllerPath");
            }
        }
    }
?>
