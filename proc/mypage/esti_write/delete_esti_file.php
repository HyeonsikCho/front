<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/Template.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/EstiInfoDAO.php"); 
$frontUtil = new FrontCommonUtil();

if ($is_login === false) {
    $frontUtil->errorGoBack("로그인 상태가 아닙니다.");
    exit;
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new EstiInfoDAO ();

$esti_file_seqno = $fb->form("esti_file_seqno");

$conn->StartTrans();

$param = array();
$param["esti_file_seqno"] = $esti_file_seqno;

$rs = $dao->selectEstiFileList($conn, $param);
$fields = $rs->fields;

$file_path = $root_path .
             $fields["file_path"] .
             $fields["save_file_name"];

if (@unlink($file_path) === false) {
    if (@is_file($file_path) === true) {
        goto ERR;
    }
}

$param = array();
$param["table"] = "esti_file";
$param["prk"] = "esti_file_seqno";
$param["prkVal"] = $esti_file_seqno;

$dao->deleteData($conn, $param);

if ($conn->HasFailedTrans() === true) {
    $conn->FailTrans();
    $conn->RollbackTrans();
    goto ERR;
}

$conn->CompleteTrans();

echo 'T';
$conn->Close();
exit;

ERR:
    echo 'F';
    $conn->Close();
    exit;
?>
