<?php
    /*
    Binary Hashing Method (Version : 1.3.5) 
    Copyright (C) 2010-2011  Daymon Schroeder (Champloo11*)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
    
    Contact : daymondesign@gmail.com
    
    When contacting about this product, please be sure to correctly title 
    the e-mail. do NOT contact me in attempt to ask me to help
    insert this software to your program. Instead only contact me if there
    is a bug, an update or suggestion in which you would like me to add.
  
    === HOW TO USE === 
     Include Class Into Project                // include("file/path/to/bhm.php");
     $handler = new bhm();                     // create handler object    
     $handler->bhm1File('test.php');           // Hashing a file, forced at 127 characters
     $handler->length = 127;                   // setting length of hash, default is 127
     $handler->bhm($string,[OPTIONAL] $salt ); // creating hash of a string,float,int,long,char etc...
     echo $handler->hash;                      // echo just made hash   
   
    === UPDATES (1.3.5) ===
     * Welcome to 1.3.5!!

    === TO DO ==
    
    === CREDITS ===
    Thank you to the following people for the helping in testing and contributing your time
    : Zak W.
    : Johnson Wilmhere
    : Andrew S.
    : Michaella R.
    : Jonathan Y.
    : CompGeek*
    : Tailee*
    : Population0*
    : ButcheR*
    : Ben121*

    
       * Online Alias
    */
    class bhm {
        public $length = 127;
        private $textLength;
        private $sum;
        public $hash;
        public function bhm1($text,$salt=""){
            $this->textLength = strlen($text.$salt);
            $this->ascDec($text.$salt);
        }
        
        private function baseMakerFunction($x){
            $number = 33+round(94*pow(cos($x*($x/sqrt($x+1))),2));
            if($number != 127){
              return $number;
            }else{
              return $number - 1;
            }
        }
        
        private function decBaseMaker($array) {
            /* Setting the base number for each part of the array to the default for that array spot */
            for($x = 0; $x < $this->length; ++$x){
                $baseArray[$x] = $x;
            }
            
            $arraySize = count($array); // Preventing calling count function every time loop completes
            $length = round($this->length/2);
            $base = 0;
            $secondBase = 0;
            $multiplier = 1;
            $sumAverage = $this->sum/$this->length; 
            
            for($x = $length; $x < $this->length; ++$x){
                $baseArray[$x] +=  round($array[$secondBase]  +$x + $this->sum * $this->length + $sumAverage * $multiplier);
                ++$base;
                ++$secondBase;
                if($base >= $length / $arraySize){
                    $multiplier += 1+($sumAverage / $secondBase);     
                }
                if($secondBase >= $arraySize){
                    $secondBase = 0;
                }
            }
            
            for($x = 0; $x < $length - 1; ++$x){
                $baseArray[$x] +=  round($array[$secondBase]  +$x + $this->sum * $this->length + $sumAverage * $multiplier);
                
                ++$base;
                ++$secondBase;
                if($base > $length / $arraySize){
                    $multiplier += .5*$x ;
                }
                if($secondBase >= $arraySize){
                    $secondBase = 0;
                }          
            }
            $this->decAsc($baseArray);
        }

        
        private function decAsc($var){
            if(gettype($var) == "array"){
                $this->decAscArray($var);
            }elseif(gettype($var) == "integer"){
                $this->decAscInt($var);
            }else{
                $this->error("Unknown data type in private function decAsc '\$var'");
            }
        }
        
        private function getDecArraySum($array){
            $arrayLength = count($array);
            $sum = 0;
            for($x = 0; $x < $arrayLength; ++$x){
                $sum += $array[$x];
            }
            $this->sum = $sum % 127000000;
            
        }
        private function decAscArray($var){
            $arraySize = count($var); // Prevent calling count function every time loop completes
            $hash = "";
            for($x = 0; $x < $arraySize; ++$x){
                if($this->baseMakerFunction($var[$x]) < 127){
                  $hash .= chr($this->baseMakerFunction($var[$x]));
                }else{
                  $hash .= chr($this->baseMakerFunction($this->baseMakerFunction($var[$x])));
                }
            }
            $this->hash = str_replace("<","-",$hash);
            $this->hash = str_replace(">","(",$this->hash);
            $this->hash = str_replace("'","+",$this->hash);
            $this->hash = str_replace('"',"*",$this->hash);
            $this->hash = str_replace('\\',"d",$this->hash);
        }
        private function ascDec($var){
            if(gettype($var) == "string"){
                $this->ascDecString($var);
            }elseif(gettype($var) == "array"){
                $this->ascDecArray($var);
            }elseif(gettype($var) == "integer"){
                $var = (string) $var;
                $this->ascDecString($var);
            }else{
                $this->error("Unknown data type in private function ascDec '\$var'");
            }
        }
       

        private function ascDecString($var){
              $array = str_split($var,1);
              $this->ascDec($array);
        }
        
        private function ascDecArray($var){
            
            $arraySize = count($var); // Prevent calling count function every time loop completes
            for($x = 0; $x < $arraySize; ++$x){
                $array[$x] = ord($var[$x]);
            }
            $this->getDecArraySum($array);
            $this->decBaseMaker($array); 
        }
        
        public function bhm1File($file){
            ini_set('memory_limit', '1000M');
            $this->length = 127;
            $string = file_get_contents($file);
            $strlen = strlen($string);
            
            $sum = 0;
            if($strlen == 1){
                $sum += ord($string{0});
            }
            for($x = 0; $x < (($strlen & 100000) + ( $strlen *0.10)) ; ++$x){
                if($x < $strlen){
                    $sum += ord($string{$x});
                }
            }
            $average = $sum/($strlen + 1);
            $this->bhm1(round($sum/($average+1)), filesize($file) + $strlen);                      
        }
        private function error($error){
            die("<b>Error </b> : ".$error);
        }        
    }
?>