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

include_once('/home/dprinting/front/com/nexmotion/common/entity/FormBean.php'); 
include_once('/home/dprinting/front/com/nexmotion/job/upload/UploadDAO.php');

$fb = new FormBean();
$upload = new UploadDAO();

$targetdir = $_GET["targetdir"];
$ymd = $_GET["ymd"];
$uniqueno = $_GET["uniqueno"];

for ($x = 0; $x < count($_FILES["upload"]["tmp_name"]); $x++) {
	$upload->upLoadFile($targetdir , $_FILES["upload"]["tmp_name"][$x], 
                        $_FILES["upload"]["name"][$x] , $ymd , $uniqueno);
}
?>
