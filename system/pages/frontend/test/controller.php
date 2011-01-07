<?php
    class TestController extends AbstractController
    {
        private $request;
        private $response;

        public function __construct(IRequest $request, Response $response)
        {
            $this->request = $request;
            $this->response = $response;
        }

        public function action_index()
        {
            echo 'Ich bin die Indexaktion :) !' . "\n\n";
            var_dump($this->request->getAll('get'));
        }

        public function action_test()
        {
            echo 'ich bin die Testaktion :D !' . "\n\n";
            var_dump($this->request->getAll('get'));
        }
    }
?>
