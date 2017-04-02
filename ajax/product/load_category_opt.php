<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/common_config.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/message.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/html/product/QuickEstimate.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductNcDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");

//include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");
//$dao = new ProductCommonDAO();

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new ProductNcDAO();
$frontUtil = new FrontCommonUtil();
$fb = new FormBean();

$cate_sortcode = $fb->form("cate_sortcode");

$sortcode_arr = $frontUtil->getTMBCateSortcode($conn, $dao, $cate_sortcode);

$sortcode_t = $sortcode_arr["sortcode_t"];
$sortcode_m = $sortcode_arr["sortcode_m"];
$sortcode_b = $sortcode_arr["sortcode_b"];

// 카테고리 셀렉트박스 생성
$cate_top = $dao->selectCateHtml($conn, $sortcode_t);
$cate_mid = $dao->selectCateHtml($conn, $sortcode_m, $sortcode_t);
$cate_bot = $dao->selectCateHtml($conn, $sortcode_b, $sortcode_m);

echo $cate_bot; 
$conn->Close();
?>
