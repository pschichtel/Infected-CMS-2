<?php
    /**
     * Abstract base class for a basic controller which provides basic functionality
     */
    abstract class AbstractBasicPage extends AbstractController
    {
        protected $design;
        protected $db;
        protected $session;
        protected $request;
        protected $response;
        
        public function  __construct(IRequest $request, IResponse $response)
        {
            $this->design = new Design('TEST');
            $this->db = Registry::get('database');
            $this->session =& Session::instance();
            $this->request = $request;
            $this->response = $response;
        }
        
        public function __destruct()
        {
            $this->response->setContent($this->design->render());
        }
    }
?>
