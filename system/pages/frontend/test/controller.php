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

        public function action_database()
        {
            echo "ich bin die datenbank-testaktion!\n\n";
            $db = Registry::get('database');
            $statement = $db->query('SHOW TABLES');
            $result = $statement->execute();
            var_dump($result);
        }

        public function action_session()
        {
            echo "ich bin die session-testaktion!\n\n";
            if (Session::instance()->exists('text'))
            {
                echo "Test: " . Session::instance()->get('text') . "\n";
            }
            else
            {
                Session::instance()->set('text', 'Zufall: ' . mt_rand());
                echo "text geschrieben!";
            }
            echo "\n\nSession-ID: " . Session::id();
        }
    }
?>
