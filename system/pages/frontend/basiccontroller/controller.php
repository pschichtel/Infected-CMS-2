<?php
    /**
     *
     */
    class BasiccontrollerController extends AbstractBasicPage
    {
        public function __construct(IRequest $request, Response $response)
        {
            parent::__construct($request, $response);
            $this->design->setTitle('BasicPage');
        }

        public function action_index()
        {
            if (Session::instance()->exists('text'))
            {
                echo "Session text:\n" . Session::instance()->get('text') . "\n";
            }
            else
            {
                echo 'No Session text found!';
            }
        }
        
        public function action_tpl()
        {
            Design::addMinorTitle('Templates tests');
            $tpl = new Template('test/index');
            $tpl->addVar('data', array(
                array(
                    'title' => 'Titel 1',
                    'text' => 'Text 1',
                    'author' => 'Autor 1'
                ),
                array(
                    'title' => 'Titel 2',
                    'text' => 'Text 2',
                    'author' => 'Autor 2'
                )
            ));
            $this->design->setContentTpl($tpl);
        }
    }
?>
