<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/common_info.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/Template.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php"); 
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/MainDAO.php"); 
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/mypage/MainDOC.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$template = new Template();
$frontUtil = new FrontCommonUtil();
$dao = new MainDAO();

$typ = $fb->form("typ");
$session = $fb->getSession();
$seqno = $session["org_member_seqno"];
$member_dvs = $session["member_dvs"];

//오늘
$today = date("Y.m.d");
//일주일전
$a_week_ago = date("Y.m.d", mktime(0,0,0,date("m")  , date("d")-7, date("Y")));
//이번달 1일
$this_month = date("Y.m.d", mktime(0, 0, 0, intval(date('m')), 1, intval(date('Y'))));

//기간
//최근 1주
if ($typ == "W") {
    $period = $a_week_ago . " ~ " . $today;
    $from = $a_week_ago;
    $to = $today;
//이번달
} else if ($typ == "M") {
    if ($this_month == $today) {
        $period = $today;
    } else {
        $period = $this_month . " ~ " . $today;
    }
    $from = $this_month;
    $to = $today;
}

if ($member_dvs == "기업") {

    $param = array();
    $param["member_seqno"] = $seqno;

    $rs = $dao->getBuPerSeqno($conn, $param);

    $bu_seqno = $seqno;
    while ($rs && !$rs->EOF) {

        $bu_seqno .= "," . $rs->fields["member_seqno"];
        $rs->moveNext();
    }
}

//전체주문
$param = array();
if ($member_dvs == "기업") {
    $param["member_seqno"] = $bu_seqno;
} else {
    $param["member_seqno"] = $seqno;
}

$state_arr = $session["state_arr"];

$param["not"] = $state_arr["주문취소"];
$param["from"] = $from;
$param["to"] = $to;

$rs = $dao->selectOrderStatusCount($conn, $param);

$tot_cnt = $rs->fields["cnt"];

$param["not"] = "";

//상태
$state_rs = $dao->selectStateAdminDvs($conn);
$count_arr = array();
while ($state_rs && !$state_rs->EOF) {
    $dvs = $state_rs->fields["dvs"];

    $range = $dao->selectStateAdminRange($conn, $dvs);

    $param["state_min"] = $range["min"];
    $param["state_max"] = $range["max"];

    $rs = $dao->selectOrderStatusCount($conn, $param);
    $count_arr[$dvs] = $rs->fields["cnt"];

    $state_rs->MoveNext();
}

$param["dvs"] = "COUNT";
$count_rs = $dao->selectOrderList($conn, $param);
$param["count"] = $count_rs->fields["cnt"];
$param["dvs"] = "";
$rs = $dao->selectOrderList($conn, $param);

$param["member_dvs"] = $member_dvs;
//$list = makeOrderListHtml($conn, $rs, $param);
$list = "";

//주문 상태 HTML
$param = array();
$param["design_dir"] = "/design_template";
$param["tot_cnt"] = $tot_cnt;
$param["list"] = $list;
$param["state_arr"] = $count_arr;

echo orderStatus($param) . "♪" . $period . "♪" . $from . "♪" . $to;
$conn->Close();
?>
