<?
/*
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016/10/13 엄준현 생성
 * 2016/12/01 엄준현 수정(쿼리 order by 추가, 배열값 이상한부분 수정)
 *============================================================================
 *
 */
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/order/SheetDAO.php");

if ($is_login === false) {
    echo "<script>alert('로그인이 필요합니다.'); return false;</script>";
    exit();
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new SheetDAO();

$fb = $fb->getForm();

$param = array();
$param["member_seqno"]     = $fb->session("org_member_seqno");
$param["order_seqno"]      = $fb->form("order_seqno");
$param["order_file_seqno"] = $fb->form("file_seqno");

$rs = $dao->selectOrderFile($conn, $param);

$save_path = $rs["file_path"];
$save_file_name = $rs["save_file_name"];
$down_file_name = $rs["origin_file_name"];

$file_size = filesize($file_path);

if ($util->isIe()) {
    $down_file_name = $util->utf2euc($down_file_name);
}

header("Pragma: public");
header("Expires: 0");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$down_file_name\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $file_size");

ob_clean();
flush();
readfile($file_path);
?>
