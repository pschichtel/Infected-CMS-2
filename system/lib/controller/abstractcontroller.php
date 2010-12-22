<?php
    /**
     *
     */
    abstract class AbstractController
    {
        abstract function __construct(IRequest $request, Response $response);

        abstract function action_index();
    }
?>
