<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/service/FileListDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new FileListDAO();

$check = 1;
$conn->StartTrans();

$seqno = $fb->form("seqno");

$param = array();
$param["table"] = "share_library_file";
$param["col"] = "file_path, save_file_name";
$param["where"]["share_library_seqno"] = $seqno;

$rs = $dao->selectData($conn, $param);

unlink($_SERVER["DOCUMENT_ROOT"] . 
       $rs->fields["file_path"] . 
       $rs->fields["save_file_name"]);

$param = array();
$param["table"] = "share_library_file";
$param["prk"] = "share_library_seqno";
$param["prkVal"] = $seqno;

$rs = $dao->deleteData($conn,$param);

if (!$rs) {
    $check = 0;
}

$param = array();
$param["table"] = "share_library";
$param["prk"] = "share_library_seqno";
$param["prkVal"] = $fb->form("seqno");

$rs = $dao->deleteData($conn,$param);

if (!$rs) {
    $check = 0;
}

$conn->CompleteTrans();
$conn->Close();
echo $check;
?>
