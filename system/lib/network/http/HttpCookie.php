<?php
    class HttpCookie implements Serializable
    {
        /**
         * Holds all the properties auf the cookie
         *
         * @access protected
         * @var string[] the properties of the cookie
         */
        protected $data;

        /**
         * Creates an HttpCookie object.
         * All parameters have default values.
         *
         * @access public
         * @param string $name the name of the cookie
         * @param string $value the value of the cookie
         * @param int $expires the UNIX timestamp of the expire date
         * @param string $path the path in whihc the cookie is valid
         * @param string $domain the domain on which the cookie is valid
         * @param bool $secure whether the cookie is only valid on SSL connections
         * @param bool $httponly whether this cookie can only be accessed to send it via HTTP (the Http-class will ignore this)
         */
        public function __construct($name = null, $value = null, $expires = null, $path = null, $domain = null, $secure = false, $httponly = false)
        {
            $this->data = array(
                'name'      => $name,
                'value'     => $value,
                'expires'   => $expires,
                'path'      => $path,
                'domain'    => $domain,
                'secure'    => $secure,
                'httponly'  => $httponly
            );
        }

        /**
         * Generates the UNIX timestamp of the expires-property of this cookie
         *
         * @access public
         * @staticvar long $cache the UNIX timestamp
         * @staticvar HashMap<string,int> $months an array which maps the month names to their number
         * @return long the UNIX timestamp
         */
        public function getExpiresAsLong()
        {
            static $cache = null;
            static $months = array(
                'Jan' =>  1,
                'Feb' =>  2,
                'Mar' =>  3,
                'Apr' =>  4,
                'May' =>  5,
                'Jun' =>  6,
                'Jul' =>  7,
                'Aug' =>  8,
                'Sep' =>  9,
                'Oct' => 10,
                'Nov' => 11,
                'Dec' => 12
            );

            if ($cache !== null)
            {
                return $cache;
            }

            if ($this->data['expires'] !== null)
            {
                echo "{$this->data['expires']} -> ";
                $stamp = preg_replace('/[a-z]{3}, (\d{2})\-([a-z]{3})\-(\d{4}) (\d{2})\:(\d{2})\:(\d{2}) GMT/i', '$1|$2|$3|$4|$5|$6', $this->data['expires']);
                echo $stamp . "\n";
                $parts = explode('|', $stamp);
                if (count($parts) != 6)
                {
                    throw new NetworkException('Failed to parse the expires value of this cookie!');
                }
                $cache = mktime($parts[3], $parts[4], $parts[5], $months[$parts[1]], intval($parts[0]), $parts[2]);
                return $cache;
            }
            else
            {
                return false;
            }
        }

        /**
         * Sets the given name-value-pair if the name is set in the internal data array
         *
         * @access public
         * @param string $name the name
         * @param mixed $value the value
         * @return HttpCookie fluent-interface
         */
        public function set($name, $value)
        {
            if (array_key_exists($name, $this->data))
            {
                $this->data[$name] = $value;
                return $this;
            }
            else
            {
                return false;
            }
        }

        /**
         * Gets the named value from the internal data array.
         * If it does not exist or is null the given default value will be returned.
         *
         * @access public
         * @param string $name the name
         * @param mixed $default the default value
         * @return mixed the named value or the default value
         */
        public function get($name, $default = null)
        {
            if (array_key_exists($name, $this->data) && $this->data[$name] !== null)
            {
                return $this->data[$name];
            }
            else
            {
                return $default;
            }
        }

        /**
         * Returns the key-value-pair if the object is in string context
         *
         * @access public
         * @return string the name-value-pair
         */
        public function __toString()
        {
            return $this->data['name'] . '=' . $this->data['value'];
        }

        public function serialize()
        {
            return serialize($this->data);
        }

        public function unserialize($serialized)
        {
            $this->data = unserialize($serialized);
        }
    }
?>
