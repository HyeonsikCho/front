<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/html/product/EstimatePop.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/prdt_default_info.php");

// 실제 html 생성부분
include_once($_SERVER["DOCUMENT_ROOT"] . "/product/common/esti_pop_common.php");

$html_top = getHtmlTop($param);
if ($booklet === 'Y') {
    $html_mid = getBookletHtmlMid($param);
} else {
    $html_mid = getHtmlMid($param);
}
$html_mid_bot = getHtmlMidBot($param, $aft_arr);
$html_bot = getHtmlBot($param);

echo $html_top . $html_mid . $html_mid_bot . $html_bot;

$conn->Close();
exit;
?>
