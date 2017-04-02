<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new ProductCommonDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

$cate_sortcode = $fb->form("cate_sortcode");
$print_name    = $fb->form("val");

$param = array();
$param["cate_sortcode"] = $cate_sortcode;
$param["name"]          = $print_name;

echo $dao->selectCatePrintPurpHtml($conn, $param);

$conn->Close();
?>
