<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/ConnectionPool.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/CommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/EstiInfoDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/common_config.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new CommonDAO();

// 판매채널 정보
$sell_site = $dao->selectCpnAdmin($conn, $fb->session("sell_site"));

$base_path = $_SERVER["DOCUMENT_ROOT"] . EXCEL_TEMPLATE;
$file_name = $fb->form("filename");
$file_path = $base_path . $file_name . ".xlsx";
$file_size = filesize($file_path);

$down_file_name = $sell_site . '_';

if ($fb->form("dvs") === "esti") {
    $down_file_name .= "견적서.xlsx";
} else if ($fb->form("dvs") === "payment") {
    $down_file_name .= $fb->form("start") . '~';
    $down_file_name .= $fb->form("end") . "_거래명세표.xlsx";
}

header("Pragma: public");
header("Expires: 0");
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$down_file_name\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: $file_size");

ob_clean();
flush();
if (readfile($file_path) !== false) {
    unlink($file_path);
}
?>
