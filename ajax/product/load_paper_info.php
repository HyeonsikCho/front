<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new ProductCommonDAO();

$fb = $fb->getForm();

$cate_sortcode = $fb["cate_sortcode"];
$paper_sort    = $fb["sort"];
$paper_name    = $fb["name"];

$param = array();
$param["cate_sortcode"] = $cate_sortcode;
$param["name"] = $paper_name;

$temp = array();

$paper = $dao->selectCatePaperHtml($conn, $param, $temp)["info"];

echo $paper;
?>
