<?php
    abstract class AbstractHttpAuthentication
    {
        public abstract function __toString();
        public abstract function getAuthHeader(Http $http);
    }
?>
