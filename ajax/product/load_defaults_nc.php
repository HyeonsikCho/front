<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/Template.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/product/info/SheetCutInfo.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$template = new Template();
$fb = new FormBean();

$cate_sortcode = $fb->form("cate_sortcode");

$info = new SheetCutInfo($conn,
                         $template,
                         $cate_sortcode,
                         "nc",
                         $fb->session("sell_site"),
                         false);

echo json_encode($template->getHashtable());

$conn->Close();
?>
