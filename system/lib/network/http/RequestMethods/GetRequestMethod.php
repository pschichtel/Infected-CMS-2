<?php
    final class GetRequestMethod extends AbstractHttpRequestMethod
    {
        public function __toString()
        {
            return 'GET';
        }

        public function getHeader(Http $http)
        {
            $http->setConnectionKeepAlive(true);
            $headerLines[] = 'GET ' . $http->getFile() . ' HTTP/1.1';
            $headerLines[] = 'Host: ' . $http->getHost();
            foreach ($http->getHeaders() as $index => $header)
            {
                if ($index == 'connection')
                {
                    if (strcasecmp($header->value, 'keep-alive') === 0)
                    {
                        $http->setConnectionKeepAlive(true);
                    }
                    else
                    {
                        $http->setConnectionKeepAlive(false);
                    }
                }
                $headerLines[] = strval($header);
            }
            if ($http->countCookies() > 0)
            {
                $headerLines[] = $this->buildCookieHeader($http);
            }
            if ($http->getAuthUse())
            {
                $headerLines[] = $http->getAuthMethod()->getAuthHeader($http);
            }

            return implode(Http::LINE_ENDING, $headerLines);
        }

        public function content()
        {
            return true;
        }
    }
?>
