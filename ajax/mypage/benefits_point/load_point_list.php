<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/MemberInfoDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/pageLib.php');

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new MemberInfoDAO();

//한페이지에 출력할 게시물 갯수
$list_num = $fb->form("list_num"); 

//현재 페이지
$page = $fb->form("page");

//리스트 보여주는 갯수 설정
if (!$fb->form("list_num")) $list_num = 10;
// 페이지가 없으면 1 페이지
if (!$page) $page = 1; 

$type = $fb->form("type");
$s_num = $list_num * ($page-1);
$session = $fb->getSession();
$seqno = $session["org_member_seqno"];

$param = array();
$param["s_num"] = $s_num;
$param["list_num"] = $list_num;
$param["from"] = $fb->form("from");
$param["to"] = $fb->form("to");
$param["dvs"] = $fb->form("dvs");
$param["seqno"] = $session["org_member_seqno"];
$param["type"] = "SEQ";

$rs = $dao->selectPointList($conn, $param);

$param["type"] = "COUNT";
$count_rs = $dao->selectPointList($conn, $param);
$rsCount = $count_rs->fields["cnt"];

$param["count"] = $rsCount;

$list = makePointListHtml($conn, $rs, $param, $type);
$paging = mkDotAjaxPage($rsCount, $page, $list_num, "movePage");
$html = "총<em>" . $rsCount . "</em>건의 포인트내역이 있습니다.";

echo $list . "♪" . $paging . "♪" . $html;
$conn->close();
?>
