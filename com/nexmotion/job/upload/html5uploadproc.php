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

$targetdir = $_REQUEST["targetdir"];
$ymd = $_REQUEST["ymd"];
$uniqueno = $_REQUEST["uniqueno"];

$upload->upLoadFile($targetdir, $_FILES["upload"]["tmp_name"], $_FILES["upload"]["name"], $ymd, $uniqueno);

?>
