<?php
    final class HeadRequestMethod extends AbstractHttpRequestMethod
    {
        public function __toString()
        {
            return 'HEAD';
        }

        public function getHeader(HttpClient $http)
        {
            $http->setConnectionKeepAlive(false);
            $headerLines[] = 'HEAD ' . $http->getFile() . ' HTTP/1.1';
            $headerLines[] = 'Host: ' . $http->getHost();
            foreach ($http->getHeaders() as $header)
            {
                $headerLines[] = strval($header);
            }
            $headerLines[] = 'Connection: close';
            if ($http->countCookies() > 0)
            {
                $headerLines[] = $this->buildCookieHeader($http);
            }
            if ($http->getAuthUse())
            {
                $headerLines[] = $http->getAuthMethod()->getAuthHeader($http);
            }

            return implode(HttpClient::LINE_ENDING, $headerLines);
        }
    }
?>
