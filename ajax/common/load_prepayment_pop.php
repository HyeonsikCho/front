<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/mypage/MemberInfoDOC.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/CommonDAO.php");

$frontUtil = new FrontCommonUtil();

if ($is_login === false) {
    echo noLoginPop();
    exit;
}

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$dao = new CommonDAO();

$fb = new FormBean();

$session = $fb->getSession();
unset($fb);

$member_info = $dao->selectMemberInfo($conn, $session["org_member_seqno"]);
$url = "http://" . $_SERVER['HTTP_HOST'];

$info = array(
    "group_name" => $session["group_name"],
    "member_name" => $session["member_name"],
    "email" => $member_info["mail"],
    "member_seqno" => $session["member_seqno"],
    "url" => $url
);

echo prepaymentPop($info);
exit;
?>
