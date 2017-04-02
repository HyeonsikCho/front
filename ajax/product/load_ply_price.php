<?
/**
 * 현재 확정형 책자상품은 반영 안되있는 코드임
 * 차후에 반영될 경우 dvs 값을 기준으로 분리필요
 */
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductNcDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$frontUtil = new FrontCommonUtil();
$dao = new ProductNcDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

$fb = $fb->getForm();

$dvs = $fb["dvs"];

$cate_sortcode = $fb["cate_sortcode"];
$stan_mpcode   = $fb["stan_mpcode"];
$amt           = $fb["amt"];
$paper_mpcode  = $fb["paper_mpcode"];

//$conn->debug = 1;

$price_tb = $dao->selectPriceTableName($conn, '0', $sell_site);

$param = array();

$param["table_name"]    = $price_tb;
$param["cate_sortcode"] = $cate_sortcode;
$param["paper_mpcode"]  = $paper_mpcode;
$param["bef_print_mpcode"]     = $fb["bef_print_mpcode"];
$param["aft_print_mpcode"]     = $fb["aft_print_mpcode"];
$param["bef_add_print_mpcode"] = $fb["bef_add_print_mpcode"];
$param["aft_add_print_mpcode"] = $fb["aft_add_print_mpcode"];
$param["stan_mpcode"]   = $stan_mpcode;
$param["amt"]           = $amt;

$price = $dao->selectPrdtPlyPrice($conn, $param);
$sell_price = $frontUtil->ceilVal($price["new_price"]);

$ret  = "{\"%s\" : {";
$ret .=              "\"sell_price\" : \"%s\"";
$ret .=            "}";
$ret .= "}";

echo sprintf($ret, $dvs, $sell_price);
$conn->Close();
?>
