<?php
    /**
     * 
     */
    abstract class AbstractBasicController extends AbstractController
    {
        protected $design;
        protected $db;
        protected $session;
        
        public function  __construct(IRequest $request, Response $response)
        {
            $this->design = new Design();
            $this->db = Registry::get('database');
            $this->session =& Session::instance();
            return;
        }
    }
?>
