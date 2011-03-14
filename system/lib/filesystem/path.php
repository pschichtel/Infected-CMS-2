<?php

    /**
     *
     */
    class Path
    {
        private $path;

        public function __construct($path, $allowrelativpaths = true, $mustexist = true)
        {
            $path = preg_replace('/^[a-z]:/i', '', trim($path));
            if ($mustexist && !file_exists($path))
            {
                throw new FileSystemException('[Path::__construct] path was not found!', 404);
            }
            if (!$allowrelativpaths && !preg_match('/^(\\|\/)/', $path))
            {
                throw new FileSystemException('[Path::__construct] path was not found!', 401);
            }
        }
        
        public function addToIncludePath()
        {
            $iPath = get_include_path();
            set_include_path($iPath . PATH_SEPARATOR . $this->path);
        }


    }
?>
