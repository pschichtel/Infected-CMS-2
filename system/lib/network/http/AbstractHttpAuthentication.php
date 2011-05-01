<?php
    abstract class AbstractHttpAuthentication
    {
        public abstract function __toString();
        public abstract function getAuthHeader(HttpClient $http);
    }
?>
