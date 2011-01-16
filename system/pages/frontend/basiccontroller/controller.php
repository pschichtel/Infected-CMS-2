<?php
    /**
     *
     */
    class BasiccontrollerController extends AbstractBasicController
    {
        public function __construct(IRequest $request, Response $response)
        {
            parent::__construct($request, $response);
        }

        public function action_index()
        {
            $this->design->render();
            if (Session::instance()->exists('text'))
            {
                echo "Session text:\n" . Session::instance()->get('text') . "\n";
            }
            else
            {
                echo 'No Session text found!';
            }
        }
    }
?>
