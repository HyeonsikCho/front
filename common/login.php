<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/common_info.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/CommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/common/common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/PasswordEncrypt.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$frontUtil = new FrontCommonUtil();
$commonUtil = new CommonUtil();
$dao = new CommonDAO();

$seqno = $fb->form("seqno");
$flag = $fb->form("flag");
$isadmin = $fb->form("isadmin");

$success = "true";

if($isadmin == "Y") {
    $fb->addSession("isadmin", true);
}

if (empty($seqno)) {
    $id = $fb->form("id");
    $pw = $fb->form("pw");
    $id_save = $fb->form("id_save");

    $rs = $dao->selectMember($conn, array("id" => $id));

    $pw_hash = $rs->fields["passwd"];

    if (password_verify($pw, $pw_hash) === false) {
        $success = "false";
        goto END;
    }
} else {
    $rs = $dao->selectMember($conn, array("seqno" => $seqno));

    if (password_verify(ADMIN_FLAG[0], $flag) === false) {
        $success = "false";
        goto END;
    }

    $id = $rs->fields["member_id"];
}

if ($id_save === "Y") {
    //expire 차후 조정
    setcookie("id", $id, time()+864000, "/");
} else {
    setcookie("id","",0, "/");
}

$session = $fb->getSession();

// 판매채널 정보
$sell_site = $dao->selectCpnAdmin($conn, $session["sell_site"]);

// 로그인 한 사람에 대한 정보
$member_name = $rs->fields["member_name"];
$group_name  = $rs->fields["group_name"];
$member_dvs  = $rs->fields["member_dvs"];

// 그룹 아이디 있는지 체크함 -> 기업개인인지 확인 함
$group_id          = $rs->fields["group_id"];
$org_member_seqno  = $rs->fields["member_seqno"]; 
if (empty($group_id) === false) {
    $rs = $dao->selectMember($conn, array("seqno" => $group_id));
}

//마지막 로그인 시간 변경
$param = array();
//$param["member_seqno"] = $rs->fields["member_seqno"];
$param["member_seqno"] = $org_member_seqno;

//USER 로그인시 로그인 시간 변경
if (strpos($_SERVER["HTTP_REFERER"],"erp.yesprinting.co.kr") === false) { 
    $dao->updateMemberFinalLoginDate($conn, $param);
} 

// 기본정보 변수에 저장(그룹일련번호 존재할 경우 해당 그룹의 정보임)
$member_seqno      = $rs->fields["member_seqno"];
$grade             = $rs->fields["grade"];
$grade_name_ko     = GRADE_KO[$grade];
$grade_name_en     = GRADE_EN[$grade];
$grade_image       = GRADE_IMAGE[$grade];
$bank_name         = $rs->fields["bank_name"];
$ba_num            = $rs->fields["ba_num"];
$member_typ        = $rs->fields["member_typ"];
$own_point         = $rs->fields["own_point"];
$prepay_price      = $rs->fields["prepay_price"];
$order_lack_price  = $rs->fields["order_lack_price"];
$cumul_sales_price = $rs->fields["cumul_sales_price"];
$onefile_etprs_yn  = $rs->fields["onefile_etprs_yn"];
$card_pay_yn       = $rs->fields["card_pay_yn"];
$nc_release_resp   = $rs->fields["nc_release_resp"];
$bl_release_resp   = $rs->fields["bl_release_resp"];
$a_board_yn        = $rs->fields["A_board_yn"];

// 쿠폰 매수 검색
$cp_count = $dao->selectMemberCpCount($conn, $member_seqno);

// 전화번호 접두사
$tel_prefix = "02.2260.";

// 명함출고담당 전화번호, 이름 검색
$param = array();
$param["table"] = "empl";
$param["col"] = "exten_num, name";
$param["where"]["empl_seqno"] = $nc_release_resp;

$nc_release_rs = $dao->selectData($conn, $param);

$nc_release_name = $nc_release_rs->fields["name"];
if ($nc_release_name === "미분류") {
    $nc_release_tel = "미분류";
} else {
    $nc_release_tel = $tel_prefix . $nc_release_rs->fields["exten_num"];
}

// 전단출고담당 전화번호, 이름 검색
$param = array();
$param["table"] = "empl";
$param["col"] = "exten_num, name";
$param["where"]["empl_seqno"] = $bl_release_resp;

$bl_release_rs = $dao->selectData($conn, $param);

$bl_release_name = $bl_release_rs->fields["name"];
if ($bl_release_name === "미분류") {
    $bl_release_tel = "미분류";
} else {
    $bl_release_tel = $tel_prefix . $bl_release_rs->fields["exten_num"];
}

// 통화접수담당자 전화번호, 이름 검색
$param = array();
$param["table"] = "member_mng";
$param["col"] = "tel_mng";
$param["where"]["mng_dvs"] = "일반";
$param["where"]["member_seqno"] = $rs->fields["member_seqno"];

$tel_mng = $dao->selectData($conn, $param)->fields["tel_mng"];

$param = array();
$param["table"] = "empl";
$param["col"] = "exten_num, name";
$param["where"]["empl_seqno"] = $tel_mng;

$tel_mng_rs = $dao->selectData($conn, $param);

$member_mng_name = $tel_mng_rs->fields["name"];
if ($member_mng_name === "미분류") {
    $member_mng_tel = "미분류";
} else {
    $member_mng_tel = $tel_prefix . $tel_mng_rs->fields["exten_num"];
}

$cart_count_html = "";
$cart_count = $dao->selectCartCount($conn, $member_seqno)->fields["cnt"];
if ($cart_count) {
    $cart_count_html = "<span class=\"num\"><span>" . $cart_count . "</span></span>";
}

// 주문상태값 배열 저장
$state_arr = array();
$state_rs  = $dao->selectStateAdmin($conn);
while ($state_rs && !$state_rs->EOF) {
    $fields = $state_rs->fields;

    $state_arr[$fields["front_state_name"]] = $fields["state_code"];

    $state_rs->MoveNext();
}
unset($state_rs);

// 세션에 값 저장
$fb->addSession("sell_site_name"    , $sell_site);
$fb->addSession("id"                , $id);
$fb->addSession("org_member_seqno"  , $org_member_seqno);
$fb->addSession("member_seqno"      , $member_seqno);
$fb->addSession("member_name"       , $member_name);
$fb->addSession("member_dvs"        , $member_dvs);
$fb->addSession("member_typ"        , $member_typ);
$fb->addSession("group_id"          , $group_id);
$fb->addSession("group_name"        , $group_name);
$fb->addSession("grade"             , $grade);
$fb->addSession("grade_name_ko"     , $grade_name_ko);
$fb->addSession("grade_name_en"     , $grade_name_en);
$fb->addSession("grade_image"       , $grade_image);
$fb->addSession("bank_name"         , $bank_name);
$fb->addSession("ba_num"            , $ba_num);
$fb->addSession("own_point"         , $own_point);
$fb->addSession("prepay_price"      , $prepay_price);
$fb->addSession("order_lack_price"  , $order_lack_price);
$fb->addSession("nc_release_tel"    , $nc_release_tel);
$fb->addSession("nc_release_name"   , $nc_release_name);
$fb->addSession("bl_release_tel"    , $bl_release_tel);
$fb->addSession("bl_release_name"   , $bl_release_name);
$fb->addSession("member_mng_tel"    , $member_mng_tel);
$fb->addSession("member_mng_name"   , $member_mng_name);
$fb->addSession("cp_count"          , $cp_count);
$fb->addSession("cumul_sales_price" , $cumul_sales_price);
$fb->addSession("onefile_etprs_yn"  , $onefile_etprs_yn);
$fb->addSession("card_pay_yn"       , $card_pay_yn);
$fb->addSession("cart_count"        , $cart_count_html);
$fb->addSession("a_board_yn"        , $a_board_yn);
$fb->addSession("state_arr"         , $state_arr);

if ($seqno) {
    header("Location: /main/main.html");
    exit;
}

/*
// 헤더부분 처리
$header_html = $commonUtil->convJsonStr(getLoginHtml($session));

// 사이드 메뉴 처리
unset($param);
$date = date("Y-m-d");
$start_date = date("Y-m-d", strtotime($date . "-7day")) . " 00:00:00";
$end_date   = $date . " 23:59:59";

$param["seqno"] = $member_seqno;
$param["start_date"] = $start_date;
$param["end_date"]   = $end_date;

$summary = $dao->selectOrderSummary($conn, $param);
$summary = $frontUtil->makeOrderSummaryArr($summary);

$rs = $dao->selectRecentOrderList($conn, $param);

$html  = "\n<tr>";
$html .= "\n    <td><input type=\"checkbox\" name=\"order_chk\" value=\"%s\"></td>";
$html .= "\n    <td>%s</td>";
$html .= "\n</tr>";

$order_list = "";

while ($rs && !$rs->EOF) {

    $order_list .= sprintf($html, $rs->fields["order_common_seqno"]
            , $rs->fields["order_detail"]);
    $rs->moveNext();
}

$order_btn = "";
if ($order_list) {

    $order_btn = <<<HTML
        <div class="function">
        <button type="button" class="_selectAll white">전체선택</button>
        <div class="purchase">
        <strong><button type="button">즉시주문</button></strong>
        <button type="button">장바구니</button>
        </div>
        </div>
HTML;
} else {
    $order_btn = <<<HTML
        <div style="margin:18px;">
        <span>주문한 상품이 없습니다.</span>
        </div>
HTML;
}

$aside_html = $commonUtil->convJsonStr(getAsideHtml($session, $summary, $order_list, $order_btn));

*/

END:
    $referer = $_SERVER["HTTP_REFERER"];

    if (strpos($referer, "login.html") !== false) {
        $referer = "/main/main.html";
    }

    $ret  = '{';
    $ret .= " \"success\" : %s,";
    $ret .= " \"ref\"     : \"%s\"";
    $ret .= '}';

    echo sprintf($ret, $success, $referer);

    $conn->Close();
    exit;
?>
