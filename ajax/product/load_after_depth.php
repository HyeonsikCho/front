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
$after_name    = $fb->form("after_name");
$depth1        = $fb->form("depth1");
$depth2        = $fb->form("depth2");
$size          = $fb->form("size");

$flag          = $fb->form("flag");

if ($flag === 'Y') {
    $flag = true;
} else {
    $flag = false;
}

$param = array();
$param["cate_sortcode"] = $cate_sortcode;
$param["after_name"]    = $after_name;
$param["depth1"]        = $depth1;
$param["depth2"]        = $depth2;
$param["size"]          = $size;

// 카테고리와 후공정명으로 후공정 하위 depth html 검색
echo $dao->selectCateAfterDepthHtml($conn, $param, $flag);

$conn->Close();
?>
