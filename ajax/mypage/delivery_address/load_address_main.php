<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/MemberDlvrDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/mypage/DeliveryDOC.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/pageLib.php');

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$fb = new FormBean();
$dao = new MemberDlvrDAO();

$dvs = $fb->form("dvs");

if ($dvs == 1) {

    //한페이지에 출력할 게시물 갯수
    $list_num = 10;
    //현재 페이지
    $page = 1;
    $s_num = $list_num * ($page-1);
    $session = $fb->getSession();
    $seqno = $session["member_seqno"];

    $param = array();
    $param["s_num"] = $s_num;
    $param["list_num"] = $list_num;
    $param["from"] = $fb->form("from");
    $param["to"] = $fb->form("to");
    $param["category"] = $fb->form("category");
    $param["searchkey"] = $fb->form("searchkey");
    $param["seqno"] = $seqno;
    $param["type"] = "SEQ";
    $rs = $dao->selectDlvrList($conn, $param);

    $param["type"] = "COUNT";
    $count_rs = $dao->selectDlvrList($conn, $param);
    $rsCount = $count_rs->fields["cnt"];
    $param["count"] = $rsCount;

    $list = makeDlvrListHtml($rs, $param);
    $paging = mkDotAjaxPage($rsCount, $page, $list_num, "movePage");
    $html = "총<em> " . $rsCount . "</em>건의 배송지가 있습니다.";

    $param = array();
    $param["list"] = $list;
    $param["paging"] = $paging;
    $param["html"] = $html;

    echo getAddressMain($param);

} else {

    echo getAddressTypeMain();
}
?>

