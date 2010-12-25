<?php
    require_once dirname(__FILE__) . '/imageexception.php';
    
    /**
     *
     */
    class ImageManipulate
    {
        private $image;
        private $imageWidth;
        private $imageHeight;
        private $imageType;
        private $imageBits;
        private $imageChannels;
        private $imageMime;

        
        /**
         *
         */
        public function __construct($imagepath)
        {
            $this->image = imagecreatefromstring(file_get_contents($imagepath));
            if ($this->image === false)
            {
                throw new ImageException('the given image could not be processed!', 501);
            }
            $info = getimagesize($imagepath);
            $this->imageWidth = $info[0];
            $this->imageHeight = $info[1];
            $this->imageType = $info[2];
            $this->imageBits = $info['bits'];
            $this->imageChannels = $info['channels'];
            $this->imageMime = $info['mime'];
        }

        /**
         *
         */
        public function __destruct()
        {
            imagedestroy($this->image);
        }

        public function getWidth()
        {
            return $this->imageWidth;
        }

        public function getHeight()
        {
            return $this->imageHeight;
        }

        public function getType()
        {
            return $this->imageType;
        }

        public function getBits()
        {
            return $this->imageBits;
        }

        public function getChannels()
        {
            return $this->imageChannels;
        }

        public function getMime()
        {
            return $this->imageMime;
        }

        /**
         *
         * @param int $width
         * @param int $height
         */
        public function rescale($width, $height)
        {
            
        }

        /**
         *
         * @param int $width
         * @param bool $keepratio
         */
        public function rescaleByWidth($width, $keepratio = true)
        {

        }

        /**
         *
         * @param int $height
         * @param bool $keepratio
         */
        public function rescaleByHeight($height, $keepratio = true)
        {

        }

        public function dither($colors)
        {
            $logger = Log::factory(DITHER_LOG);

            sort($colors, SORT_NUMERIC);
            $colorcount = count($colors);

            $logger->write(0, 'counts', 'ColorCount: ' . $colorcount);

            for ($i = 0; $i < $colorcount; $i++)
            {
                $parts = explode(',', $colors[$i]);
                if (count($parts) != 3)
                {
                    throw new ImageException('there was a wrong color given', 401);
                }
                $colors[$i] = imagecolorallocate($this->image, intval($parts[0]), intval($parts[1]), intval($parts[2]));
            }

            $counter = 0;
            $black = imagecolorallocate($this->image, 255, 255, 255);
            for ($y = 0; $y < $this->imageHeight; $y++)
            {
                for ($x = 0; $x < $this->imageWidth; $x++)
                {
                    $pixelColor = imagecolorat($this->image, $x, $y);
                    $currentColor = $black;

                    for ($z = 0; $z < $colorcount; $z++)
                    {
                        $counter++;
                        $currentColorAverage = ($currentColor + $colors[$z]) / 2;
                        $logger->write(0, 'values', "currentColorAverage=$currentColorAverage, currentColor=$currentColor, pixelColor=$pixelColor");
                        if ($pixelColor > $currentColor && $pixelColor < $currentColorAverage)
                        {
                            imagesetpixel($this->image, $x, $y, $colors[$z]);
                            $logger->write(0, 'choise', "Farbe ändern: {$colors[$z]}");
                            break;
                        }
                        else
                        {
                            $currentColor =& $colors[$z];
                            $logger->write(0, 'choise', "Farbe beibehalten: $currentColor");
                        }
                        $logger->write(0, 'Z-loop', '-----------------------------------------');
                    }
                    /*/ prüfen, ob Pixel auf weiß oder schwarz gesetzt wird
                    if($prevVal < ($black + $white / 2))
                    {
                        imagesetpixel($image, $i, $j, $black);  //dunkler
                    }
                    else
                    {
                        imagesetpixel($image, $i, $j, $white);   //heller
                    }*/
                    $error = $pixelColor - imagecolorat($this->image, $x, $y);

                    // Fehlerpropagation und
                    // Abfangen der letzten Zeile und Spalte
                    if($x < $this->imageWidth - 1)
                    {
                        imagesetpixel($this->image, $x + 1, $y, imagecolorat($this->image, $x + 1, $y) + (7 * $error / 16));
                    }

                    if($y < $this->imageHeight - 1)
                    {
                        imagesetpixel($this->image, $x, $y + 1, imagecolorat($this->image, $x, $y + 1) + (5 * $error / 16));
                    }

                    if($x < $this->imageWidth - 1 && $y < $this->imageHeight -1)
                    {
                        imagesetpixel($this->image, $x + 1, $y + 1, imagecolorat($this->image, $x + 1, $y + 1) + (1 * $error / 16));
                    }

                    if($x > 0 && $y < $this->imageHeight - 1)
                    {
                        imagesetpixel($this->image, $x - 1, $y + 1, imagecolorat($this->image, $x - 1, $y + 1) + (3 * $error / 16));
                    }
                    $logger->write(0, 'X-loop', '-----------------------------------------');
                }
                $logger->write(0, 'Y-loop', '-----------------------------------------');
            }

            $logger->write(0, 'counts', 'Schleifendurchläufe: ' . $counter);

            imagecolordeallocate($this->image, $black);
            foreach ($colors as $color)
            {
                imagecolordeallocate($this->image, $color);
            }
        }

        public function render($type = null)
        {
            if ($type === null)
            {
                $type =& $this->imageType;
            }
            
            switch ($this->imageType)
            {
                case IMAGETYPE_GIF:
                    imagegif($this->image);
                    break;
                case IMAGETYPE_JPEG:
                    imagejpeg($this->image);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($this->image);
                    break;
                case IMAGETYPE_BMP:
                    imagewbmp($this->image);
                    break;
                default:
                    break;
            }
        }
    }
?>
