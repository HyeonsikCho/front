<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new CommonUtil();
$dao = new ProductCommonDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

$fb = $fb->getForm();

$cate_sortcode = $fb["cate_sortcode"];
$paper_mpcode  = $fb["paper_mpcode"];
$mono_dvs      = $fb["mono_yn"];
$affil_yn      = empty($fb["affil_yn"]) ? false: true;
$pos_yn        = ($fb["pos_yn"] === '0') ? false : true;
$size_typ_yn   = empty($fb["size_typ_yn"]) ? true : false;

$price_tb = $dao->selectPriceTableName($conn, $mono_dvs, $sell_site);

$param = array();

$param["table_name"]    = $price_tb;
$param["cate_sortcode"] = $cate_sortcode;
$param["paper_mpcode"]  = $paper_mpcode;

// 사이즈, 해당 종이에 물려있는 규격 맵핑코드 검색
$stan_mpcode_rs = $dao->selectCateStanMpcodeByPrice($conn, $param);

$mpcode_arr = array();
while ($stan_mpcode_rs && !$stan_mpcode_rs->EOF) {
    $mpcode_arr[] = $stan_mpcode_rs->fields["cate_stan_mpcode"];

    $stan_mpcode_rs->MoveNext();
}

$temp = array();
$param["cate_mpcode"] = $dao->arr2paramStr($conn, $mpcode_arr);

$size = $dao->selectCateSizeHtml($conn,
                                 $param,
                                 $temp,
                                 $affil_yn,
                                 $pos_yn,
                                 $size_typ_yn);

echo $size;

$conn->Close();
?>
