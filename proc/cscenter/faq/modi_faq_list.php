<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/cscenter/FaqDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new FaqDAO();

$seqno = $fb->form("seqno");

$param = array();
$param["table"] = "faq";
$param["col"] = "hits";
$param["where"]["faq_seqno"] = $seqno;

$rs = $dao->selectData($conn, $param);

$hits = $rs->fields["hits"];

$check = 1;
$conn->StartTrans();

$param = array();
$param["table"] = "faq";
$param["col"]["hits"] = $hits + 1;
$param["prk"] = "faq_seqno";
$param["prkVal"] = $seqno;

$rs = $dao->updateData($conn, $param);

if (!$rs) {
    $check = 0;
}

$conn->CompleteTrans();
$conn->close();
echo $check;
?>
