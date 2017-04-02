<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/define/product_info_class.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new ProductCommonDAO();

$frontUtil = new FrontCommonUtil();

$fb = new FormBean();

$page_arr = ProductInfoClass::PAGE_ARR;

$cate_sortcode = $fb->form("cs");
$sortcode_arr  = $frontUtil->getTMBCateSortcode($conn, $dao, $cate_sortcode);

$sortcode_t = $sortcode_arr["sortcode_t"];
$sortcode_m = $sortcode_arr["sortcode_m"];
$sortcode_b = $sortcode_arr["sortcode_b"];

if (is_array($page_arr[$sortcode_t]) === true) {
    $page_name = $page_arr[$sortcode_t][$sortcode_m];

    if (empty($page_arr[$sortcode_t][$sortcode_b]) === false) {
        $page_name = $page_arr[$sortcode_t][$sortcode_b];
    }

    if (empty($page_name) === true) {
        $page_name = $page_arr[$sortcode_t]["ELSE"];
    }
} else {
    $page_name = $page_arr[$sortcode_t];
}

$header_val = sprintf("Location: /product/%s?cs=%s&t=%s", $page_name
                                                        , $sortcode_b
                                                        , $fb->form('t'));
header($header_val);
?>
