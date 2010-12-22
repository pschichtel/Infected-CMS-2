<?php
    class TestController extends AbstractController
    {
        public function __construct(IRequest $request, Response $response)
        {
            
        }

        public function action_index()
        {
            echo 'Ich bin die Indexaktion :) !';
        }
    }
?>
