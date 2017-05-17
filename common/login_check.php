<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/doc/common/common.php");

if ($is_login) {
    $rs = $dao->selectPrepayPrice($conn, $fb->session("member_seqno"));

    $fb->addSession("prepay_price"    , $rs->fields["prepay_price"]);
    $fb->addSession("order_lack_price", $rs->fields["order_lack_price"]);

    $session = $fb->getSession();

    $date = date("Y-m-d");
    $start_date = date("Y-m-d", strtotime($date . "-7day")) . " 00:00:00";
    $end_date   = $date . " 23:59:59";

    $param = array();
    $param["seqno"] = $session["member_seqno"];
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

    $template->reg("header_login_class", "memberInfo");
    $template->reg("header_login", getLoginHtml($session));
    $template->reg("side_menu", getAsideHtml($session, $summary, $order_list, $order_btn));
    $template->reg("side_style", "");
    $template->reg("member_page", "");
    $template->reg("cart_count", $session["cart_count"]);
} else {
    $template->reg("header_login_class", "login");
    $template->reg("header_login", getLogoutHtml($_COOKIE));
    $template->reg("side_menu", "");
    $template->reg("side_style", "display:none;");


           //<li><a href="#" onclick="alert('홈페이지 오픈전입니다.');" target="_self" title="회원가입으로 이동"><img src="/design_template/images/common/header_util_icon_join_re.png" alt="회원가입"></a></li>
    $html = <<<HTML
        <ul class="orange">
           <li style="float:left"><a href="/member/join_1.html" target="_self" title="회원가입으로 이동"><img src="/design_template/images/common/header_util_icon_join_re.png" alt="회원가입"></a></li>
           <li style="float:left"><a href="/member/find_id.html" target="_self" title="아이디/패스워드 찾기로 이동"><img src="/design_template/images/common/header_util_icon_find_re.png" alt="아이디/패스워드 찾기"></a></li>
        </ul>

HTML;

    $template->reg("member_page", $html);
}

$template->reg("is_login", intval($is_login));
?>
