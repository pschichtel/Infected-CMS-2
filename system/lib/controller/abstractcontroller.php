<?php
    /**
     *
     */
    abstract class AbstractController
    {
        abstract function __construct(Request $request, Response $response);

        abstract function action_index();
    }
?>
