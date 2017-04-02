<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/ConnectionPool.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/ErpCommonUtil.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/ClaimInfoDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new ErpCommonUtil();
$fb = new FormBean();
$dao = new ClaimInfoDAO();

$param = array();
$param["table"] = "order_claim";
$param["col"] = "sample_file_path, sample_save_file_name, sample_origin_file_name";
$param["where"]["order_claim_seqno"] = $fb->form("seqno");

$rs = $dao->selectData($conn, $param);

$file_path = $_SERVER["DOCUMENT_ROOT"] . $rs->fields["sample_file_path"] . $rs->fields["sample_save_file_name"];
$file_size = filesize($file_path);

if (!is_file($file_path)) {
    echo "<script>alert('파일이 존재 하지 않습니다.');</script>";
    exit;
}

$down_file_name = $rs->fields["sample_origin_file_name"];
if ($util->isIe() === true) {
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
