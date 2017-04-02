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

$sortcode_b = $fb->form("cate_sortcode");

// 상품 배너 정보 생성
$banner_rs = $dao->selectCateBanner($conn, $sortcode_b);

$url_addr  = $banner_rs->fields["url_addr"];
$target_yn = $banner_rs->fields["target_yn"];

$file_path = NIMDA_PATH . $banner_rs->fields["file_path"];
$file_name = $banner_rs->fields["save_file_name"];
$full_path = $file_path . $file_name;

if (is_file($full_path) === false) {

    $ret  = "{\"banner_display\" : \"none\"";
    $ret .= ",\"banner_url\" : \"#none\"";
    $ret .= ",\"banner_target\" : \"_self\"";
    $ret .= ",\"banner_src\" : \"#none\"}";

} else {

    $target_yn = ($target_yn === "Y") ? "_self" : "_blank";
    $banner_src = $full_path;

    $ret  = "{\"banner_display\" : \"block\"";
    $ret .= ",\"banner_url\" : \"%s\"";
    $ret .= ",\"banner_target\" : \"%s\"";
    $ret .= ",\"banner_src\" : \"%s\"}";

    $ret = sprintf($ret, ""
                       , $url_addr
                       , $target_yn
                       , $banner_src);
}

echo $ret;
$conn->Close();
?>
