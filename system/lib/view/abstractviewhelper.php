<?php
    /**
     *
     */
    abstract class AbstractViewHelper
    {
        public function render($action)
        {
            $action = 'action_' . $action;
            if (is_callable(array($this, $action)))
            {
                return $this->{$action}();
            }
            else
            {
                return false;
            }
        }
    }
?>
