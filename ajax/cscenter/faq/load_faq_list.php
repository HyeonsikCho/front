<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/cscenter/FaqDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/pageLib.php');

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new FaqDAO();

//한페이지에 출력할 게시물 갯수
$list_num = $fb->form("list_num"); 

//현재 페이지
$page = $fb->form("page");

//리스트 보여주는 갯수 설정
if (!$fb->form("list_num")) {
    $list_num = 10;
}

// 페이지가 없으면 1 페이지
if (!$page) {
    $page = 1; 
}

$s_num = $list_num * ($page-1);

$param = array();
$param["s_num"] = $s_num;
$param["list_num"] = $list_num;
$param["cont"] = $fb->form("cont");
$param["type"] = $fb->form("type");
$param["dvs"] = "SEQ";
$rs = $dao->selectFaqList($conn, $param);

$param["dvs"] = "COUNT";
$count_rs = $dao->selectFaqList($conn, $param);
$rsCount = $count_rs->fields["cnt"];
$param["count"] = $rsCount;
$list = makeFAQListHtml($rs, $param);
$paging = mkDotAjaxPage($rsCount, $page, $list_num, "movePage");

echo $list . "♪" . $paging;
$conn->close();
?>
