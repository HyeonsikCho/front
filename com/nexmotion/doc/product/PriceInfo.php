<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/common_info.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");

/**
 * @brief 회원 등급 할인 dl html 반환
 *
 * @param $param = 회원등급, 할인율 파라미터
 *
 * @return dl html
 */
function getGradeSaleDl($param) {
    $util = new FrontCommonUtil();

    $rate  = $param["rate"];

    $grade = $param["grade"];
    $grade = GRADE_EN[$grade];

    $price = number_format(doubleval($param["price"]));

    $dscr  = $param["dscr"];
    if ($dscr === null) {
        $add_dvcr = '';

        if ($param["member_sale_rate"] > 0) {
            $add_dscr = " + 추가 " . $param["member_sale_rate"] . '%';
        }

        $dscr  = sprintf("%s (할인율 : %s%%%s)", $grade, $rate, $add_dscr);
    }

    $html = <<<html
        <dl style="height:23px;">
            <dt>회원등급 할인</dt>
            <dd>
                <button type="button"><img src="/design_template/images/product/discount_btn_grade.png" alt="등급할인혜택"></button>
            </dd>
            <dd class="description">$dscr</dd>
            <dd id="grade_sale" class="discountAmount">$price 원</dd>
        </dl>
html;

    return $html;
}

/**
 * @brief 회원 수량별 할인 dl html 반환
 *
 * @param $param = 할인요율, 할인금액
 *
 * @return dl html
 */
function getAmtMemberSale($param) {
    $util = new FrontCommonUtil();

    $rate  = $param["rate"];
    $ap= $param["aplc_price"];

    $price = doubleval($param["price"]);

    $html = <<<html
        <dl style="height:23px;">
            <dt>회원특별 할인</dt>
            <dd>
                <button type="button"><img src="/design_template/images/product/discount_btn_amt_member.png" alt="특별할인혜택"></button>
            </dd>
            <dd class="description">특별할인금액</dd>
            <dd id="amt_member_sale" class="discountAmount">$price 원</dd>
        </dl>
html;

    return $html;
}

/**
 * @brief 이벤트 할인 dl html 반환
 *
 * @param $param = 이벤트명, 할인 요율/가격 정보 파라미터
 *
 * @return dl html
 */
function getEventSaleDl($param) {
    return '';

    $name  = $param["name"];
    $price = $param["price"];
    $dscr  = $param["dscr"];

    $html = <<<html
        <dl style="height:23px;">
            <dt>이벤트 할인</dt>
            <dd>
                <button type="button"><img src="/design_template/images/product/discount_btn_event.png" alt="관련 이벤트"></button>
            </dd>
            <dd class="description">$dscr</dd>
            <dd id="event_sale" class="discountAmount"></dd>
        </dl>
html;

    return $html;
}
?>
