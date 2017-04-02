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

$cate_sortcode = $fb->form("cate_sortcode");
$size_name     = $fb->form("size_name");
$size_typ      = $fb->form("size_typ");

$param = array();
$param["cate_sortcode"] = $cate_sortcode;
$param["default"]       = $size_name;
$param["typ"]           = $size_typ;

$temp = array();

$html = $util->convJsonStr($dao->selectCateSizeHtml($conn,
                                                    $param,
                                                    $temp,
                                                    false,
                                                    false,
                                                    false));
$gap  = $temp["size_gap"];

echo sprintf("{\"html\" : \"%s\", \"gap\" : \"%s\"}", $html, $gap);

$conn->Close();
?>
