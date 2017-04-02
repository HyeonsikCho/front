<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/product/ProductNcDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/define/product_info_class.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$frontUtil = new FrontCommonUtil();
$dao = new ProductNcDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

$fb = $fb->getForm();

$dvs      = $fb["dvs"];
$typ      = $fb["typ"];
$mono_yn  = $fb["mono_yn"];
$tmpt_dvs = $fb["tmpt_dvs"];
$affil    = $fb["affil"];

$cate_sortcode = $fb["cate_sortcode"];
$stan_mpcode   = $fb["stan_mpcode"];
$amt           = $fb["amt"];

$paper_mpcode = $fb["paper_mpcode"];
$page_info    = explode('!', $fb["page_info"]);
$page         = $page_info[0];
$page_detail  = $page_info[1];
$page_dvs     = ProductInfoClass::TYP_ARR[$typ];

$conn->debug = 1;

$price_tb = $dao->selectPriceTableName($conn, $mono_yn, $sell_site);

$param = array();
$param["table_name"]    = $price_tb;
$param["cate_sortcode"] = $cate_sortcode;
$param["stan_mpcode"]   = $stan_mpcode;
$param["amt"]           = $amt;
$param["affil"]         = $affil;

//$conn->debug = 1;

// 인쇄 맵핑코드 검색
// 도수구분에 따라서 변경
if ($tmpt_dvs === '0') {
    $print_name   = $fb["bef_print_name"];
    $print_purp   = $fb["print_purp"];

    $param["cate_sortcode"] = $cate_sortcode;
    $param["name"]          = $print_name;
    $param["purp_dvs"]      = $print_purp;

    $print_mpcode = $dao->selectCatePrintMpcode($conn, $param);

    $param["bef_print_mpcode"]     = $print_mpcode;
    $param["bef_add_print_mpcode"] = '0';
    $param["aft_print_mpcode"]     = '0';
    $param["aft_add_print_mpcode"] = '0';
} else {
    $print_mpcode_arr = $frontUtil->getPrintMpcode($conn, $dao, $fb, "inner");

    $param["bef_print_mpcode"]     = $print_mpcode_arr["bef"];
    $param["bef_add_print_mpcode"] = $print_mpcode_arr["bef_add"];
    $param["aft_print_mpcode"]     = $print_mpcode_arr["aft"];
    $param["aft_add_print_mpcode"] = $print_mpcode_arr["aft_add"];
}

// 가격 검색
$param["paper_mpcode"] = $paper_mpcode;
$param["page"]         = $page;
$param["page_dvs"]     = $page_dvs;
$param["page_detail"]  = $page_detail;

$rs = $dao->selectPrdtCalcPrice($conn, $param);

$price_json  = '{';
$price_json .= " \"paper\"  : \"%s\",";
$price_json .= " \"print\"  : \"%s\",";
$price_json .= " \"output\" : \"%s\",";
$price_json .= " \"price\"  : \"%s\"";
$price_json .= '}';

$outer  = '{';
$outer .= " \"%s\"  : %s";

$outer  = sprintf($outer, $dvs, $price_json);
$outer  = sprintf($outer, $frontUtil->ceilVal($rs["paper_price"])
                        , $frontUtil->ceilVal($rs["print_price"])
                        , $frontUtil->ceilVal($rs["output_price"])
                        , $frontUtil->ceilVal($rs["sum_price"]));
$outer .= '}';

echo $outer;

$conn->Close();
?>
