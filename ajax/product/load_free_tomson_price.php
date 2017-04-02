<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new FrontCommonUtil();
$dao = new ProductCommonDAO();
$fb = new FormBean();

$fb = $fb->getForm();

// $Pp_id -> 종이관련 정보
$paper_name = $fb["paper_name"];
// $S_Code -> 사이즈 유형 정보
$stan_name  = $fb["stan_name"];
// $Qty -> 수량
$amt        = intval($fb["amt"]);
// $chkW, chkH
$size_width = doubleval($fb["size_width"]);
$size_vert  = doubleval($fb["size_vert"]);

//$conn->debug = 1;

$param = array();
$param["amt"] = $amt;

$price_per = $dao->selectAfterTomsonPricePer($conn, $param);

$basic_price = doubleval($price_per["basic_price"]);
$knife_per   = doubleval($price_per["knife_price_per"]);
$paper_per   = doubleval($price_per["stick_paper_price_per"]);

if (strpos($paper_name, "아트지") === false &&
        strpos($paper_name, "모조지") === false) {
    $paper_per = doubleval($price_per["especial_paper_price_per"]);
}

//echo "b_p : $basic_price / k_p : $knife_per / p_p : $paper_per\n";

$area = (($size_width + 5.0) * ($size_vert + 5.0)) / 100.0;
$area = round($area, 2);

//echo "area : $area\n";

if ($area < 30.0) {
    $area = 30.0;
}

//echo "area : $area\n";

$price_1 = $area * $paper_per * $amt;

//echo "price_1 : $price_1\n";

if ($price_1 < $basic_price) {
    $price_1 = $basic_price;
}

//echo "price_1 : $price_1\n";

$col_name = null;

switch ($stan_name) {
case "유형1":
    $col_name = "typ1_price";
    break;
case "유형2":
    $col_name = "typ2_price";
    break;
case "유형3":
    $col_name = "typ3_price";
    break;
case "유형4":
    $col_name = "typ4_price";
    break;
}

//echo "col_name : $col_name\n";

$chk_area = $size_width * $size_vert / 100.0;
$chk_area = round($chk_area, 1);

$param["col_name"]   = $col_name;
$param["size_start"] = $chk_area;
$param["size_end"]   = $chk_area;

$price_fields = $dao->selectAfterTomsonPrice($conn, $param);

$basic_price = doubleval($price_fields["basic_price"]);
$typ_price   = doubleval($price_fields[$col_name]);

//echo "basic_price : $basic_price / typ_price : $typ_price\n";

$price_2 = ($basic_price + $typ_price) * $knife_per * $amt / 1000;

//echo "price2 : $price_2\n";

$calc_price = round($price_1 + $price_2, -2) * 1.1;

//echo "calc_price : $calc_price\n";

echo $calc_price;
?>
