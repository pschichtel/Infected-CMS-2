<?php

    /**
     * 
     */
    abstract class Text
    {
        /**
         * wraps the words in $text
         *
         * @access public
         * @static
         * @param string $text the text to chunk words in
         * @return string the chunked text
         */
        public static function simpleChunk($text, $maxLen = 50)
        {
            return preg_replace('/([\S]{' . $maxLen . '})/', '$1 ', $text);
        }


        /**
         * replaces newlines (\r\n, \n and \r) with <br /> in $text
         *
         * @access public
         * @static
         * @param string $text the text to replace newline charaters in
         * @return string the parsed text
         */
        public static function nl2br($text)
        {
            return preg_replace("/(\r?\n)|(\r)/", '<br />', $text);
        }

        /**
         * encodes $uri in a simpler way like urlencode()
         *
         * @access public
         * @static
         * @param string $uri the URI to encode
         * @return string the encodes URI
         */
        public static function simpleUriEncode($uri)
        {
            $uri = str_replace(' ', '%20', $uri);
            $uri = self::escapeAmp($uri);
            return $uri;
        }

        /**
         * entityencodes just the '&' in $text
         *
         * @access public
         * @static
         * @param string $text the text to encode the & in
         * @return string the encodes text
         */
        public static function escapeAmp($text)
        {
            return preg_replace('/&(?!amp;)/i', '&amp;', $text);
        }

        /**
         * encodes all or the characers in $text as HTML entities
         *
         * @access public
         * @static
         * @param string $text the text to encode characters in
         * @param string $chars the characters to encode. if empty, all characters will be encoded
         * @return string the encoded string
         */
        public static function entityEncode($text, $chars = '')
        {
            if ($chars === '')
            {
                $encoded = '';
                for ($i = 0; $i < mb_strlen($text); $i++)
                {
                    $encoded .= '&#' . ord(mb_substr($text, $i, 1)) . ';';
                }
                return $encoded;
            }
            else
            {
                for ($i = 0; $i < mb_strlen($chars); $i++)
                {
                    $text = str_replace($chars[$i], '&#' . ord($chars[$i]) . ';', $text);
                }
                return $text;
            }
        }

        /**
         * works as the PHP function explode, but uses the array value also as the index
         *
         * @access public
         * @static
         * @param string $delim the string to explode on
         * @param string $text the text to explode
         * @return array the array with the parts of the string
         */
        public static function explode2assoc($delim, $text)
        {
            $parts = explode($delim, $text);
            $assoc = array();
            foreach ($parts as $part)
            {
                $assoc[$part] = $part;
            }
            return $assoc;
        }

        /**
         * an mb_-version of chunk_split()
         *
         * @access public
         * @static
         * @param string $string the string to chunk
         * @param int $splitIndex the maximum length
         * @param string $delim the string to put in
         * @param string $encoding the character encoding
         * @return string the chunked string
         */
        public static function chunk_split($string, $splitIndex, $delim = ' ', $encoding = CI_CHARSET)
        {
            $chunks = array();
            for ($i = 0; $i < mb_strlen($string, $encoding); $i += $splitIndex)
            {
                $chunks[] = mb_substr($string, $i, $splitIndex, $encoding);
            }
            return implode($delim, $chunks);
        }

        /**
         * fills $string with leading and following characters until $length is reached
         *
         * @access public
         * @static
         * @param string $string the string to fill up
         * @param char $char the character to fill with
         * @param int $length the length to fill up to
         * @return string the zerofilled number as a string
         */
        public static function fill($string, $char, $length)
        {
            $length += strlen($string) - mb_strlen($string, 'UTF-8');
            return str_pad($string, $length, $char[0], STR_PAD_BOTH);
        }

        /**
         * fills $string with leading characters until $length is reached
         *
         * @access public
         * @static
         * @param string $string the string to fill up
         * @param char $char the character to fill with
         * @param int $length the length to fill up to
         * @return string the zerofilled number as a string
         */
        public static function lfill($string, $char, $length)
        {
            $length += strlen($string) - mb_strlen($string, 'UTF-8');
            return str_pad($string, $length, $char[0], STR_PAD_LEFT);
        }

        /**
         * fills $string with following characters until $length is reached
         *
         * @access public
         * @static
         * @param string $string the string to fill up
         * @param char $char the character to fill with
         * @param int $length the length to fill up to
         * @return string the zerofilled number as a string
         */
        public static function rfill($string, $char, $length)
        {
            $length += strlen($string) - mb_strlen($string, 'UTF-8');
            return str_pad($string, $length, $char[0], STR_PAD_RIGHT);
        }

        /**
         * checks whether the given string is a valid email address
         *
         * @access public
         * @static
         * @param string $string the string to validate
         * @return bool true if it is a valid email address
         */
        public static function is_email($string)
        {
            return (bool) preg_match('/[(\w\d\-\.]{3,}@((([a-z\d-]{2,}\.)+[a-z\d]{2,4})|localhost)/', mb_strtolower($string));
        }

        /**
         * checks whether the two strings are equal
         *
         * @param string $haystack the first string
         * @param string $needle the second string
         * @param bool $strict case-sensitiv or not (default: false)
         * @return bool true if the haystack and needle are equal
         */
        public static function equal($haystack, $needle, $strict = false)
        {
            $delim = '/';
            $regex = $delim . preg_quote($needle, $delim) . $delim;
            if ($strict === true)
            {
                $regex .= 'i';
            }
            return preg_match($regex, $haystack);
        }

        /**
         * url encodes all or the given chars in the string
         *
         * @param string $string the string
         * @param string $chars the chars to encode
         * @return string the encoded string
         */
        public static function urlEncode($string, $chars = '')
        {
            if ($chars === '')
            {
                $encoded = '';
                for ($i = 0; $i < mb_strlen($string); $i++)
                {
                    $encoded .= '%' . mb_strtoupper(dechex(ord(mb_substr($string, $i, 1))));
                }
                return $encoded;
            }
            else
            {
                for ($i = 0; $i < mb_strlen($chars); $i++)
                {
                    $char = mb_substr($chars, $i, 1);
                    $string = str_replace($char, '%' . mb_strtoupper(dechex(ord($chars))), $string);
                }
                return $string;
            }
        }

        /**
         * checks whether the given string is numeric
         *
         * @param string $numStr the string to check
         * @return bool true if it is numeric
         */
        public static function is_numeric($numStr)
        {
            return (bool) preg_match('/^[0-9]+$/s', $numStr);
        }

        /**
         * builds a random string of the ASCII charset (32-126)
         *
         * @param int $length the length of the random string
         * @return string the random string
         */
        public static function rand($length)
        {
            $string = '';
            for ($i = 0; $i < $length; $i++)
            {
                $string .= chr(mt_rand(32, 126));
            }
            return $string;
        }
    }
?>
