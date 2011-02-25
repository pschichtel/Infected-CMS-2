<?php
    /**
     * Abstract base class which must be implemented by every controller
     */
    abstract class AbstractController
    {
        abstract function __construct(IRequest $request, Response $response);

        abstract function action_index();
    }
?>
