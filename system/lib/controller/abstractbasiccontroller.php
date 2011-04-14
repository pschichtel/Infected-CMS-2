<?php
    /**
     * Abstract base class for basic controller which provides basic functionality
     */
    abstract class AbstractBasicController extends AbstractController
    {
        protected $design;
        protected $db;
        protected $session;
        
        public function  __construct(IRequest $request, Response $response)
        {
            $this->design = new Design('TEST');
            $this->db = Registry::get('database');
            $this->session =& Session::instance();
        }
    }
?>
