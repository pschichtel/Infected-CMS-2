<?php
    final class PostRequestMethod extends AbstractHttpRequestMethod
    {
        public function __toString()
        {
            return 'POST';
        }

        public function getHeader(Http $http)
        {
            $http->setConnectionKeepAlive(false);
            $requestBody = $http->getRequestBody();
            $headerLines[] = 'POST ' . $http->getFile() . ' HTTP/1.1';
            $headerLines[] = 'Host: ' . $http->getHost();
            $headerLines[] = 'Content-type: application/x-www-form-urlencoded';
            $headerLines[] = 'Content-Length: ' . strlen($requestBody);
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
            $headerLines[] = '';
            $headerLines[] = $requestBody;

            return implode(Http::LINE_ENDING, $headerLines);
        }

        public function content()
        {
            return true;
        }

        public function fileUpload()
        {
            return true;
        }
    }
?>
