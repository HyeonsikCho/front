<?
/**
 *
 * Template.php
 * version       : 2.1
 * Last modified : 2007.06.04
 *
 */

include_once('Configuration.php');

class Template {

    var $prefix;
    var $suffix;
    var $prefixEreg;
    var $suffixEreg;
    var $prefixLength;
    var $suffixLength;
    var $hashtable;

    function Template() {

        $this->prefix = "[TPH_";
        $this->suffix = "]";
        $this->prefixEreg = "\[TPH_";
        $this->suffixEreg = "\]";
        $this->prefixLength = strlen($this->prefix);
        $this->suffixLength = strlen($this->suffix);

    }

    function reg($key = null, $value = "") {

        if($key == null || $key == "") {
            echo ("key cannot be null.");
            exit;
        }

        $this->hashtable[$key] = $value;
    }

    function get($key) {
        return $this->hashtable[$key];
    }

    function parseLine($htmlLine) {

        $parsedLine = "";
        $key = "";
        $value = "";

        while(true) {

            $linePos = strpos($htmlLine, $this->prefix);

            if(preg_match("/".$this->prefixEreg."/", $htmlLine) == false) {
                break;
            }

            $parsedLine .= substr($htmlLine, 0, $linePos);
            $htmlLine = substr($htmlLine, $linePos);
            
            $linePos = strpos($htmlLine, $this->suffix);

            if(preg_match("/".$this->suffixEreg."/", $htmlLine) == false) {
                break;
            }

            $key = substr($htmlLine, 0 , $linePos + $this->suffixLength);
            $htmlLine = substr($htmlLine, $linePos + $this->suffixLength);

            if(substr($key, $this->prefixLength, 1) == 'V') {
                //[TPH_V
                $key = substr($key,1);
                $value = $this->hashtable[substr($key, $this->prefixLength, strlen($key) - ($this->suffixLength + $this->prefixLength))];                
            } else {
                //[TPH_I include
                echo $parsedLine; //버퍼에 있는 parsedLine을 먼저 출력해준다
                $parsedLine = ""; //초기화
                $value = ""; 
                $key = substr($key,1);
                //require(substr($key, $this->prefixLength, strlen($key) - ($this->suffixLength + $this->prefixLength)));
                $this->htmlPrint(substr($key, $this->prefixLength, strlen($key) - ($this->suffixLength + $this->prefixLength)));
            }

            $parsedLine .= $value;
            
        }

        $parsedLine .= $htmlLine;

        return($parsedLine);

    }

    function htmlPrint($filePath) {

        $configurations = new Configuration(); 
        $fileHandle = fopen($configurations->templatePath . $filePath, "r");

	if ( !$fileHandle ) {
		echo $configurations->templatePath . $filePath . " is not found\n";
		exit;
	}

        while(!feof($fileHandle)) {
            $buffer = fgets($fileHandle, 4096);
            echo $this->parseLine($buffer);
        }

        fclose($fileHandle);

    }
    
    function getHTML($filepath) {
    	
    	$configurations = new Configuration(); 
    	$retHTML = "";
        $fileHandle = fopen($configurations->templatePath . $filepath, "r");

	if ( !$fileHandle ) {
		echo $configurations->templatePath . $filepath . " is not found\n";
		exit;
	}

        while(!feof($fileHandle)) {
            $buffer = fgets($fileHandle, 4096);
            $retHTML .= $this->parseLine($buffer);
        }

        fclose($fileHandle);
        
        return $retHTML;
        
    }

    function getHashtable() {
        return $this->hashtable;
    }
}
?>
