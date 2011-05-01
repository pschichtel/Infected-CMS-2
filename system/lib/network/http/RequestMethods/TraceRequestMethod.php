<?php
    final class TraceRequestMethod extends AbstractHttpRequestMethod
    {
        public function __toString()
        {
            return 'TRACE';
        }

        public function getHeader(HttpClient $http)
        {
            $http->setConnectionKeepAlive(false);
            $headerLines[] = 'TRACE ' . $http->getFile() . ' HTTP/1.1';
            $headerLines[] = 'Host: ' . $http->getHost();
            $headerLines[] = 'Connection: close';

            return implode(HttpClient::LINE_ENDING, $headerLines);
        }

        public function content()
        {
            return true;
        }
    }
?>
