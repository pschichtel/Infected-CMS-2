<?php
    final class BasicAuthentication extends AbstractHttpAuthentication
    {
        /**
         * Returns the name of the authentication method when the object is in string context
         *
         * @access public
         * @return string the game of the authentication method
         */
        public function __toString()
        {
            return 'Basic';
        }

        /**
         * Returns the authentication header for this header
         *
         * @param Http $http the current Http object
         * @return string the authentication header
         */
        public function getAuthHeader(Http $http)
        {
            return 'Authorization: Basic ' . base64_encode($http->getAuthUser() . ':' . $http->getAuthPass());
        }
    }
?>
