<?
/*
 * Copyright (c) 2015 Nexmotion, Inc
 * All rights reserved.
 *
 * REVISION HISTORY (reverse chronological order)
 * =============================================================================
 * 2015/09/18 김관우 수정(utf-8)
 * =============================================================================
 */

/**
 * 첨부파일 관련 클래스
 * @version : 0.0.1
 */

class UploadDAO {

	var $defaultpath = "/home/dprinting/front";
	var $recordpath = "/tmp/";

	function UploadDAO() {
	}

	/**
	 * 파일을 저장할 절대경로를 반환한다
	 * @param targetdir : 기본경로 하위경로
	 * @param ymd : true 일경우 년/월/일/폴더생성
	 */
	function getFilePath($targetdir, $ymd = "y") {

		$dest = "/" . $targetdir;

		if ( $ymd == "y" ) $dest .= "/" . date("Y"). "/" . date("m") . "/" . date("d");

		return $dest;
	}

	/**
	 * 중복되지않는 파일명생성
	 * @param path : 파일의 절대경로
	 */
	function getUniqueNm($path){

		$filename = substr(time() . md5(uniqid()) , 0 , 20);

		if ( is_file($path . $filename) ) {
			getUniqueNm($path);
		}

		return $filename;
	}

	/**
	 * 폴더의 권한을 777로 변경한다
	 * @param path : 파일업로드 절대 경로
	 */
	function chkChmod($path){

		try {
			@exec("chmod 777 " . $path);
		} catch ( Exception $e ) {
			echo "\n path authority grant failure \n";
			return false;
		}
		/*
		if ( !chmod($path , 0777) ) {
			echo "\n path authority grant failure \n";
			return false;
		}
		*/
		return true;

	}

	/**
	 * 파일저장할 경로가 존재하는지 알아보고
	 * 없을경우 생성한 후 문제없으면 true 반환
	 * @param path : 파일을 저장할 상대경로
	 */
	function chkPath($path){

		if ( @is_dir($this->defaultpath . $path) )  return true;

		@exec("mkdir -p " . $this->defaultpath . $path);

		if ( !is_dir($this->defaultpath . $path) )  return false;

		return true;

	}

	/**
	 * 업로드 한 파일/실제파일명을 tmp폴더 내에 특별한 값을 파일명으로 기록한다
	 * @param uniquename : 업로드 되는 때마다 고유하게 주어지는 값
	 * @param visualname : 사용자가 올린 파일명
	 * @param realname : 업로드 된 절대경로및 파일명
	 */
	function recordFilename($uniquename, $visualname, $realname) {

		$handle = @fopen($this->recordpath . $uniquename, "a+");

		if ( !$handle ) return false;

		@fwrite($handle, $visualname . "|" . $realname . "\n");
		@fclose($handle);

		return true;
	}

	/**
	 * 파일의 확장자를 구한다
	 */
	function getExt($filename) {

		if ( !strstr($filename, ".") ) return false;

		$tmp = explode(".", $filename);
		return $tmp[1];
	}

	/**
	 * 파일 업로드
	 * @param targetdir : 서버기본경로 하위의 경로
	 * @param tmpnm : 임시저장된 파일명
	 * @param visualname : 다운로드시 보여줄 파일명
	 * @param ymd : 년/월/일 폴더 생성여부
	 * @param uniquename : tmp파일에 기록할 파일명
	 */
	function upLoadFile($targetdir , $tmpnm , $visualname , $ymd , $uniquename){

		$path = $this->getFilePath($targetdir, $ymd);

		$ext = $this->getExt($visualname);

		if ($ext == "php" || $ext == "html" || $ext == "inc") 
            return false;

		if ($this->chkPath($path) !== true) {
            return false;
        }

		$realname = $this->defaultpath . $path 
			. "/" . $this->getUniqueNm($path) . "." . $ext;

		if (move_uploaded_file($tmpnm , $realname)) {
			return $this->recordFilename($uniquename, $visualname, $realname);
		}

        echo "\n error on moving file \n";
        return false;
	}

	/**
	 * 해당파일의 첨부파일목록을 배열로반환
	 * @param uniqueno : 업로드시 생성한 고유값
	 */
	function getFileList($uniqueno) {

		$handle = @fopen($this->recordpath . $uniqueno, "r");

		if (!$handle) 
            return false;

		$buff = array(); 

		$i = 0;
		while ( !feof($handle) ) {
			$line = fgets($handle, 8192);

			$tmp = explode("|", $line);
			$buff[$i]["dnloadname"] = trim($tmp[0]);
			$buff[$i]["realname"] = trim($tmp[1]);

			$i++;
		}

		@fclose($handle);

		return $buff;

	}

	/**
	 * tmp 폴더에 임시로 저장했던 파일을 지운다
	 * @param uniqueno : 업로드시 생성한 고유값
	 */
	function removeTmpFile($uniqueno){
		@unlink($this->recordpath . $uniqueno);
	}

}
?>
