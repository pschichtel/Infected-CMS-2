<?php

    class HTTP
    {
        const DEBUG             = true;
        const OK                =  0;
        const NO_DIR            = -1;
        const NO_HOST           = -2;
        const NO_FILE           = -3;
        const DATA_MISSING      = -4;
        const DIFF_PARAM_COUNT  = -5;
        const WRONG_PARAM_TYPE  = -6;

        private $ERRNO;
        private $ERRSTR;
        private $TIMEOUT;

        private $host;
        private $hostIp;
        private $port;
        private $file;
        private $dir;
        private $ssl;

        private $postData;
        private $cookies;
        private $requestHeaders;
        private $requestMethod;
        
        private $responseHeaders;
        private $responseStatusCode;

        public function __construct()
        {
            $this->ERRNO = 0;
            $this->ERRSTR = '';
            $this->TIMEOUT = 3.0;
            
            $this->host = '';
            $this->hostIp = '';
            $this->port = 80;
            $this->file = '';
            $this->dir = '';
            $this->ssl = false;
            
            $this->postData = '';
            $this->cookies = array();
            $this->requestHeaders = array();
            $this->responseHeaders = array();
            $this->responseStatusCode = 0;
        }
        
        public function __get($name)
        {
            return $this->{$name};
        }

        public function getPage(IRequestMethod $requestMethod)
        {
            $this->requestMethod = $requestMethod;
            $this->executeRequest();
            return $this->responseContent;
        }
        
        public function addHeader($name, $header)
        {
            $this->requestHeaders[$name] = $header;
        }
        
        public function addHeaders($names, $headers)
        {
            if (count($names) !== count($headers))
            {
                return HTTP_DIF_PARAM_COUNT;
            }
            for ($i = 0; $i < count($names); $i++)
            {
                $this->requestHeaders[$names[$i]] = $headers[$i];
            }
        }
        
        public function removeHeader($name)
        {
            unset($this->requestHeaders[$name]);
        }
        
        public function removeHeaders($names)
        {
            foreach ($names as $name)
            {
                unset($this->requestHeaders[$name]);
            }
        }
        
        public function addCookie($name, $cookie)
        {
            $this->cookies[urlencode($name)] = urlencode($cookie);
        }
        
        public function addCookies($names, $cookies)
        {
            if (count($names) !== count($cookies))
            {
                return DIF_PARAM_COUNT;
            }
            for ($i = 0; $i < count($names); $i++)
            {
                $this->cookies[urlencode($names[$i])] = urlencode($cookies[$i]);
            }
        }
        
        public function removeCookie($name)
        {
            unset($this->cookies[$name]);
        }
        
        public function removeCookies($names)
        {
            foreach ($names as $name)
            {
                unset($this->cookies[$name]);
            }
        }
        
        public function setTimeout($timeout)
        {
            $this->TIMEOUT = (float) $timeout;
        }
        
        public function setHost($host)
        {
            $this->host = trim($host);
            $this->hostIp = gethostbyname(trim($host));
        }
        
        public function setPort($port)
        {
            $port = (int) $port;
            if ($port >= 0 && $port < 65537)
            {
                $this->port = $port;
            }
        }
        
        public function setFile($file, $getDirFromFile = false)
        {
            $this->file = trim($file);
            if ($getDirFromFile)
            {
                $this->dir = substr($this->file, 0, strrpos($this->file, '/') + 1);
            }
        }
        
        public function setDir($dir, $getFromFile = false)
        {
            if ($getFromFile)
            {
                $this->dir = substr($this->file, 0, strrpos($this->file, '/') + 1);
            }
            else
            {
                $this->dir = trim($dir);
            }
        }
        
        public function setSsl($ssl)
        {
            $this->ssl = (bool) $ssl;
        }
        
        public function setTarget($target)
        {
            if (preg_match('/^https?:\/\//si', trim($target)))
            {
                if (self::DEBUG)
                {
                    echo '<div style="color:red;">HTTP::setTarget() => absolutes Ziel (mit Protokoll)</div>';
                }
                $target = strtolower(trim($target));
                $pos = strpos($target, '://');
                $this->port = 80;
                if (substr($target, 0, $pos) == 'https')
                {
                    $this->ssl = true;
                    $this->port = 443;
                }
                $target = substr($target, $pos + 3);
                if (!strrpos($target, '/'))
                {
                    return self::NO_FILE;
                }
                $pos = strpos($target, '/');
                $this->host = substr($target, 0, $pos);
                $this->hostIp = gethostbyname(substr($target, 0, $pos));
                $this->file = substr($target, $pos);
                $this->dir = substr($this->file, 0, strrpos($this->file, '/') + 1);
            }
            elseif (strpos(trim($target), '/') === 0)
            {
                if (self::DEBUG)
                {
                    echo '<div style="color:red;">HTTP::setTarget() => absolutes Ziel (ohne Protokoll)</div>';
                }
                if (!$this->host || !$this->hostIp)
                {
                    return self::NO_HOST;
                }
                $this->file = trim($target);
                $this->dir = substr($this->file, 0, strrpos($this->file, '/') + 1);
            }
            else
            {
                if (self::DEBUG)
                {
                    echo '<div style="color:red;">HTTP::setTarget() => relatives Ziel</div>';
                }
                if ($this->dir)
                {
                    $this->file = $this->dir . $target;
                }
                else
                {
                    return self::NO_DIR;
                }
            }
        }
        
        public function preparePostData($data)
        {
            if (!is_array($data))
            {
                return self::WRONG_PARAM_COUNT;
            }
            $dataStr = '';
            foreach ($data as $index => $value)
            {
                $dataStr .= '&' . urlencode(trim($index)) . '=' . urlencode(trim($value));
            }
            return substr($dataStr, 1);
        }
        
        public function setPostData($postData)
        {
            $this->postData = $postData;
        }
        



        private function makeCookieHeader()
        {
            $headerStr = '';
            foreach ($this->cookies as $name => $value)
            {
                $headerStr .= '; ' . $name . '=' . $value;
            }
            return 'Cookie: ' . substr($headerStr, 2);
        }
        
        private function getCookiesFromHeader(&$responseHeaderLines)
        {
            $cookies = array();
            foreach ($responseHeaderLines as $index => $line)
            {
                $line = trim($line);
                if (preg_match('/^set\-cookie/si', $line))
                {
                    $cookies[] = trim(preg_replace('/^set-cookie:/si', '', $line));
                    unset($responseHeaderLines[$index]);
                }
            }
            foreach ($cookies as $cookie)
            {
                $posE = strpos($cookie, '=');
                $posS = strpos($cookie, ';');
                $this->cookies[substr($cookie, 0, $posE)] = substr($cookie, $posE + 1, $posS - ($posE + 1));
            }
        }
        
        private function validateVars()
        {
            if (
                !$this->host ||
                !$this->hostIp ||
                !$this->port ||
                !$this->file ||
                !$this->dir
            )
            {
                return false;
            }
            return true;
        }
        
        private function parseResponseHeader($responseHeader)
        {
            if (self::DEBUG)
            {
                echo '<p><pre><b>ResponseHeader</b><br />' . $responseHeader . '</pre></p>';
            }
            $responseHeaderLines = explode("\r\n", $responseHeader);
            $this->getCookiesFromHeader($responseHeaderLines);
            $responseHeaderData = array();
            foreach ($responseHeaderLines as $responseHeaderLine)
            {
                $tmp = explode(': ', $responseHeaderLine);
                $name = $tmp[0];
                unset($tmp[0]);
                $content = trim(implode(': ', $tmp));
                $responseHeaderData[$name] = $content;
            }
            unset($responseHeaderData[0]);
            unset($responseHeaderData[count($responseHeaderData) - 1]);
            $this->responseHeaders = $responseHeaderData;
            return $responseHeaderLines;
        }
        
        private function readResponseContent(&$sock)
        {
            $responseContent = '';
            if ($this->responseStatusCode == 200)
            {
                if (isset($this->responseHeaders['Content-Length']))
                {
                    while (strlen($responseContent) != $this->responseHeaders['Content-Length'])
                    {
                        $responseContent .= fgets($sock, 4096);
                    }
                }
                elseif (isset($this->responseHeaders['Transfer-Encoding']) && $this->responseHeaders['Transfer-Encoding'] == 'chunked')
                {
                    do
                    {
                        $chunkLen = hexdec(trim(fgets($sock, 32)));
                        $tmp = '';
                        while (strlen($tmp) != $chunkLen + 2)
                        {
                            $tmp .= fgets($sock, 4096);
                        }
                        $responseContent .= $tmp;
                    }
                    while ($chunkLen != 0);
                }
                else
                {
                    while( $tmp = fgets($sock, 4096))
                    {
                        $responseContent .= $tmp;
                    }
                }
                fclose($sock);
                return trim($responseContent);
            }
            elseif ($this->responseStatusCode >= 300 && $this->responseStatusCode <= 399)
            {
                $this->setTarget($this->responseHeaders['Location']);
                return $this->http_get(true);
            }
            else
            {
                fclose($sock);
                return $this->responseStatusCode;
            }
        }
        
        protected function validateVars()
        {
            if (
                empty($this->host)   ||
                empty($this->hostIp) ||
                $this->port == 0     ||
                empty($this->file)   ||
                empty($this->dir)
            )
            {
                return false;
            }
            return true;
        }

        /**
         * parses the repsonse header
         *
         * @access protected
         * @param string $responseHeader the response header
         */
        protected function parseResponseHeader($responseHeader)
        {
            $this->debug_print_block('Response-Header', $responseHeader);
            
            $responseHeaderLines = explode("\r\n", trim($responseHeader));

            $this->debug_print_block('raw response header lines array', print_r($responseHeaderLines, true));

            $responseHeaders = array();
            $cookieHeaders = array();
            $count = count($responseHeaderLines);
            for ($i = 1; $i < $count; $i++)
            {
                $strpos = strpos($responseHeaderLines[$i], ':');
                if ($strpos ===  false)
                {
                    continue;
                }
                $name = trim(substr($responseHeaderLines[$i], 0, $strpos));
                $value = trim(substr($responseHeaderLines[$i], $strpos + 1));
                if (strcasecmp($name, 'set-cookie') == 0)
                {
                    $this->debug_print_important('Cookie erkannt!');
                    $cookieHeaders[] = $value;
                }
                else
                {
                    $responseHeaders[$i - 1] = $value;
                    $responseHeaders[$name] = &$responseHeaders[$i - 1];
                    $responseHeaders[strtolower($name)] = &$responseHeaders[$i - 1];
                }
            }

            $this->parseCookies($cookieHeaders);
            $this->responseHeaders = $responseHeaders;

            $proto_end = strpos($responseHeaderLines[0], ' ');
            $code_end = strpos($responseHeaderLines[0], ' ', $proto_end + 1);

            $this->responseProtocol = substr($responseHeaderLines[0], 0, $proto_end);
            $this->responseStatusCode = intval(substr($responseHeaderLines[0], $proto_end + 1, $code_end - $proto_end));
            $this->responseStatusText = substr($responseHeaderLines[0], $code_end + 1);

            $this->debug_print_block('Protocol-Header', "responseProtocoll: {$this->responseProtocol}\nresponseStatusCode: {$this->responseStatusCode}\nresponseStatusText: {$this->responseStatusText}");
        }

        /**
         * reads the response content
         *
         * @access protected
         * @param resource& $sock the connecten handle
         */
        protected function readResponseContent(&$sock)
        {
            $this->responseContent = '';
            $BUFSIZE = 256;
            if (in_array($this->responseStatusCode, $this->readCodes))
            {
                $this->debug_print_important('HTTP::readResponseContent() => readCode: ' . $this->responseStatusCode);
                if (isset($this->responseHeaders['content-length']))
                {
                    $this->debug_print_important('HTTP::readResponseContent() => Content-Length given: ' . $this->responseHeaders['content-length']);

                    $this->responseContent = stream_get_contents($sock, $this->responseHeaders['content-length']);
                }
                elseif (isset($this->responseHeaders['transfer-encoding']) && $this->responseHeaders['transfer-encoding'] == 'chunked')
                {
                    $this->debug_print_important('HTTP::readResponseContent() => Transefer-encoding is chunked');

                    do
                    {
                        $chunkLen = hexdec(trim(fgets($sock, 32)));
                        $tmp = '';
                        while (strlen($tmp) != $chunkLen + 2)
                        {
                            $tmp .= fgets($sock, $BUFSIZE);
                        }
                        $this->responseContent .= $tmp;
                    }
                    while ($chunkLen != 0);
                }
                else
                {
                    $this->debug_print_important('HTTP::readResponseContent() => Just reading everything i can get');
                    
                    $this->responseContent = stream_get_contents($sock);
                }
            }
            elseif (in_array($this->responseStatusCode, $this->redirectCodes))
            {
                $this->debug_print_important('HTTP::readResponseContent() => redirectCode: ' . $this->responseStatusCode);
                if ($this->handleRedirects)
                {
                    $this->setTarget($this->responseHeaders['Location']);
                    $this->responseContent = $this->getPage('GET');
                }
                else
                {
                    return $this->responseStatusCode;
                }
            }
        }

        /**
         * performes the request with the set data
         *
         * @access public
         * @return int HTTP return code
         */
        protected function executeRequest()
        {
            if (!$this->validateVars())
            {
                return self::DATA_MISSING;
            }

            $this->debug_print_block('HTTP-Members', "Host: {$this->host}\nHost-IP: {$this->hostIp}\nSSL: " . ($this->ssl ? 'true' : 'false') . "\nPort: {$this->port}\nVerzeichnis: {$this->dir}\nDatei: {$this->file}\nCookies:\n" . print_r($this->cookies, true));


            $request = $this->requestMethod->getHeader($this) . "\r\n\r\n";
            $responseHeader = '';
            $responseContent = '';

            $this->debug_print_block('Request-Header', $request);
            
            $sock = fsockopen(($this->ssl ? 'ssl://' : '') . $this->hostIp, $this->port, $this->ERRNO, $this->ERRSTR, $this->TIMEOUT);
            fputs($sock, $request);

            while (($tmp = fgets($sock, 256)))
            {
                $responseHeader .= $tmp;
                if (trim($tmp) == '')
                {
                    break;
                }
            }
            $this->parseResponseHeader($responseHeader);

            $this->responseContent = '';
            if ($method->content())
            {
                $this->readResponseContent($sock);
            }
            fclose($sock);

            return self::OK;
        }

        /**
         * prints out a pre-formated box with the given title and text
         *
         * @access protected
         * @param string $title the title
         * @param string $text the text
         */
        protected function debug_print_block($title, $text)
        {
            if (self::DEBUG)
            {
                echo '<div style="background-color:white !important;"><pre><strong>' . $title . '</strong><br />' . $text . '</pre></div>';
            }
        }

        /**
         * prints out the given text in red
         *
         * @access protected
         * @param <type> $text the text
         */
        protected function debug_print_important($text)
        {
            if (self::DEBUG)
            {
                echo '<div style="color:red !important;">' . $text . '</div>';
            }
        }
    }

    interface IRequestMethod
    {
        public function getHeader(HTTP $http);
        public function content();
        public function fileUpload();
    }


    final class HTTP_GET implements IRequestMethod
    {
        public function getHeader(HTTP $http)
        {
            $headerLines[] = 'GET ' . $http->file . ' HTTP/1.1';
            $headerLines[] = 'Host: ' . $http->host;
            $headerLines[] = 'Connection: close';
            $headerLines = array_merge($headerLines, $http->requestHeaders);
            if (count($http->cookies) > 0)
            {
                $headerLines[] = $http->buildCookieHeader();
            }
            if (!empty($http->requestAuthUser))
            {
                $headerLines[] = $http->buildAuthHeader();
            }

            return implode("\r\n", $headerLines);
        }

        public function content()
        {
            return true;
        }

        public function fileUpload()
        {
            return false;
        }
    }

    final class HTTP_POST implements IRequestMethod
    {
        public function getHeader(HTTP $http)
        {
            $headerLines[] = 'POST ' . $http->file . ' HTTP/1.1';
            $headerLines[] = 'Host: ' . $http->host;
            $headerLines[] = 'Content-type: application/x-www-form-urlencoded';
            $headerLines[] = 'Content-Length: ' . strlen($http->postData);
            $headerLines = array_merge($headerLines, $http->requestHeaders);
            if (count($http->cookies) > 0)
            {
                $headerLines[] = $http->buildCookieHeader();
            }
            if (!empty($http->requestAuthUser))
            {
                $headerLines[] = $http->buildAuthHeader();
            }
            $headerLines[] = '';
            $headerLines[] = $http->postData;

            return implode("\r\n", $headerLines);
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

    final class HTTP_HEAD implements IRequestMethod
    {
        public function getHeader(HTTP $http)
        {
            $headerLines[] = 'HEAD ' . $http->file . ' HTTP/1.1';
            $headerLines[] = 'Host: ' . $http->host;
            $headerLines[] = 'Connection: close';
            $headerLines = array_merge($headerLines, $http->requestHeaders);
            if (count($http->cookies) > 0)
            {
                $headerLines[] = $http->buildCookieHeader();
            }
            if (!empty($http->requestAuthUser))
            {
                $headerLines[] = $http->buildAuthHeader();
            }

            return implode("\r\n", $headerLines);
        }

        public function content()
        {
            return false;
        }

        public function fileUpload()
        {
            return false;
        }
    }

    final class HTTP_TRACE implements IRequestMethod
    {
        public function getHeader(HTTP $http)
        {
            $headerLines[] = 'TRACE ' . $http->file . ' HTTP/1.1';
            $headerLines[] = 'Host: ' . $http->host;

            return implode("\r\n", $headerLines);
        }

        public function content()
        {
            return true;
        }

        public function fileUpload()
        {
            return false;
        }
    }

    final class HTTP_OPTIONS implements IRequestMethod
    {
        public function getHeader(HTTP $http)
        {
            $headerLines[] = 'OPTIONS ' . $http->file . ' HTTP/1.1';
            $headerLines[] = 'Host: ' . $http->host;
            $headerLines[] = 'Connection: close';

            return implode("\r\n", $headerLines);
        }

        public function content()
        {
            return true;
        }

        public function fileUpload()
        {
            return false;
        }
    }
?>