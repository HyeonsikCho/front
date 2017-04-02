<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/product/common/calc_opt_price.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new FrontCommonUtil();
$dao = new ProductCommonDAO();
$fb = new FormBean();

$sell_site = $fb->session("sell_site");

if (empty($sell_site)) {
    echo $_SERVER["HTTP_REFERER"];
    exit;
}

$cate_sortcode  = $fb->form("cate_sortcode");
$name           = $fb->form("name");
$mpcode         = $fb->form("mpcode");
$amt            = $fb->form("amt");
$sell_price     = intval($util->rmComma($fb->form("sell_price")));
$paper_mpcode   = $fb->form("paper_mpcode");
$paper_info     = $fb->form("paper_info");
$affil          = $fb->form("affil");

//$conn->debug = 1;

$param = array();
$param["cate_sortcode"] = $cate_sortcode;
$param["name"]          = $name;
$param["basic_yn"]      = 'N';
if ($name !== "빠른생산요청") {
    $param["mpcode"] = $mpcode;
}

// 카테고리와 옵션명으로 맵핑코드 검색
$opt_info_rs = $dao->selectCateOptInfo($conn, $param);

if ($opt_info_rs->EOF) {
    goto BLANK;
}


$price = 0;

$out_flag = false;

while ($opt_info_rs && !$opt_info_rs->EOF) {
    $fields = $opt_info_rs->fields;

    $mpcode = $fields["mpcode"];
    $depth1 = $fields["depth1"];
    $depth2 = $fields["depth2"];
    $depth3 = $fields["depth3"];

    switch ($name) {
    case "당일판" :
        $price = getDayBoardPrice($depth1);
        break;
    case "시안요청" :
        $price = getDraftRequestPrice($depth1);
        break;
    case "빠른생산요청" :
        if (0 < $sell_price && $sell_price < 100001 &&
                $depth1 === "판매가의 10%추가") {
            $price  = getQuickProductionPrice($sell_price);
            $out_flag = true;
        } else if (100000 < $sell_price && $sell_price < 200001 &&
                $depth1 === "판매가의 8%추가") {
            $price  = getQuickProductionPrice($sell_price);
            $out_flag = true;
        } else if (200000 < $sell_price && $sell_price < 300001 &&
                $depth1 === "판매가의 6%추가") {
            $price  = getQuickProductionPrice($sell_price);
            $out_flag = true;
        } else if (300000 < $sell_price && $sell_price < 500001 &&
                $depth1 === "판매가의 5%추가") {
            $price  = getQuickProductionPrice($sell_price);
            $out_flag = true;
        } else if (500000 < $sell_price &&
                $depth1 === "판매가의 3%추가") {
            $price  = getQuickProductionPrice($sell_price);
            $out_flag = true;
        }
        break;
    case "정매생산요청" :
        unset($param);
        $param["sell_site"]         = $sell_site;
        $param["paper_info"]        = $paper_info;
        $param["cate_paper_mpcode"] = $paper_mpcode;
        $param["affil"]             = $affil;
        $param["amt"]               = $amt;
        $price = getCorrectCountProductionPrice($conn,
                                                $dao,
                                                $param);
        break;
    case "포장방법" :
        // 종이 분류에 따라서 일반용지는 500장 고급용지는 200장 기준
        // 170321 무료로 번경
        //$chunk = getAmtChunk($conn, $dao, $util, $fb->getForm());
        //$price = getPackPrice($depth2, $chunk);
        $price = 0;
        break;
    case "동판/목형관리" :
        $price = getCopperWoodPrice($depth1);
        break;
    case "색견본참고" :
        $price = getColorSamplePrice($depth1);
        break;
    case "교정디지털출력" :
        $price = getCorrectionPrintPrice($depth1, $depth2);
        break;
    case "감리요청" :
        $price = getInspectionRequestPrice($depth1);
        break;
    case "판비추가" :
        $price = getAddPlatePrice($depth1);
        break;
    case "베다인쇄" :
        $price = getBackgroundPrice($depth1);
        break;
    }

    if ($out_flag) {
        break;
    }

    $opt_info_rs->MoveNext();
}

unset($opt_info_rs);
unset($param);

// json 생성
$ret  = '{';
$ret .=   "\"price\" :\"%s\",";
$ret .=   "\"mpcode\":\"%s\"";
$ret .= '}';

$price = $util->ceilVal($price);

echo sprintf($ret, $price, $mpcode);
$conn->Close();
exit;

BLANK:
    echo sprintf($ret, '0', $mpcode);
    $conn->Close();
    exit;
?>
