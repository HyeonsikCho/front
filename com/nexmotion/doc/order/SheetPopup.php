<?
/**
 * @brief 상품 선택 팝업 html 생성
 *
 * @param $rs = 검색결과
 *
 * @return 팝업 html
 */
function productListPopup($rs, $to, $selected) {
    $tr_base  = "<tr>";
    $tr_base .= "<td><input id=\"available_%s\" type=\"checkbox\" name=\"product_ck\" value=\"%s\" class=\"popupProducts\" %s></td>";
    $tr_base .= "<td>%s</td>";
    $tr_base .= "<td>%s</td>";
    $tr_base .= "<td>%s%s</td>";
    $tr_base .= "</tr>";

    $tr = "";
    while ($rs && !$rs->EOF) {
        $fields = $rs->fields;

        $amt   = '';
        $count = '';

        if (empty($fields["s_amt"]) === false) {
            $amt   = $fields["s_amt"];
            $count = '('. $fields["s_count"] . ')';
        }

        if (empty($fields["b_amt"]) === false) {
            if (empty($amt) === true) {
                $amt   = $fields["b_amt"];
                $count = '(1)';
            } else {
                $amt   = "혼합형";
            }
        }

        $tr .= sprintf($tr_base
            , $fields["order_common_seqno"]
            , $fields["order_common_seqno"]
            , ""
            , $fields["title"]
            , $fields["order_detail"]
            , $amt
            , $count);

        $rs->MoveNext();
    }

    $table = "<strong><button onclick=\"setProducts('%s');\">적용</button></strong>";
    $table1 = sprintf($table, $to);

    $html = <<<html
        <header>
            <h2>선택한 주문 목록</h2>
            <button class="close" title="닫기"><img src="/design_template/images/common/btn_circle_x_white.png" alt="X"></button>
        </header>
        <article>
            <table class="list thead">
                <colgroup>
                    <col width="150">
                    <col width="80">
                    <col width="260">
                    <col>
                </colgroup>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="cb_chooseorder" onclick="changestate()"></th>
                        <th>인쇄물제목</th>
                        <th>상품정보</th>
                        <th>수량(건)</th>
                    </tr>
                </thead>
            </table>
            <div class="tableScroll">
                <div class="wrap">
                    <table class="list">
                        <colgroup>
                            <col width="150">
                            <col width="80">
                            <col width="260">
                            <col>
                        </colgroup>
                        <tbody>
                            $tr
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="function center">
                    $table1
                <button class="close">취소</button>
            </div>
        </article>
html;

    return $html;
}



/**
 * @brief 나의배송지 선택 팝업 html 생성
 *
 * @param $rs = 검색결과
 *
 * @return 팝업 html
 */
function addressListPopup($rs) {
    $tr_base  = "<tr>";
    $tr_base .= "    <th scope=\"row\">%s</th>"; // 별칭
    $tr_base .= "    <td>%s</td>"; // 받으시는분
    $tr_base .= "    <td class=\"address\">%s %s</td>"; // 주소
    $tr_base .= "    <td class=\"btn\">";
    $tr_base .= "       <button name=\"cb_order\" onclick=\"setMemberAddrInfo.exec(this);\">선택</button>";
    $tr_base .= "       <input type=\"hidden\" name=\"name\" value=\"%s\" />";
    $tr_base .= "       <input type=\"hidden\" name=\"tel_num\" value=\"%s\" />";
    $tr_base .= "       <input type=\"hidden\" name=\"cell_num\" value=\"%s\" />";
    $tr_base .= "       <input type=\"hidden\" name=\"zipcode\" value=\"%s\" />";
    $tr_base .= "       <input type=\"hidden\" name=\"addr\" value=\"%s\" />";
    $tr_base .= "       <input type=\"hidden\" name=\"addr_detail\" value=\"%s\" />";
    $tr_base .= "    </td>";
    $tr_base .= "</tr>";

    $tr = "";

    if ($rs->EOF) {
        $tr = "<tr><td colspan=\"4\">나의배송지 정보가 없습니다.</td></tr>";
    }

    while ($rs && !$rs->EOF) {
        $fields = $rs->fields;

        $tr .= sprintf($tr_base, $fields["dlvr_name"]
                               , $fields["recei"]
                               , $fields["addr"]
                               , $fields["addr_detail"]
                               , $fields["recei"]
                               , $fields["tel_num"]
                               , $fields["cell_num"]
                               , $fields["zipcode"]
                               , $fields["addr"]
                               , $fields["addr_detail"]);
        $rs->MoveNext();
    }

    $html = <<<html
        <header>
            <h2>나의 배송지 목록</h2>
            <button class="close" title="닫기"><img src="/design_template/images/common/btn_circle_x_white.png" alt="X"></button>
        </header>
        <article>
            <table class="list thead">
                <colgroup>
                    <col width="150">
                    <col width="80">
                    <col width="260">
                    <col>
                </colgroup>
                <thead>
                    <tr>
                        <th>배송지 별칭</th>
                        <th>받으시는 분</th>
                        <th>주소</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
            <div class="tableScroll">
                <div class="wrap">
                    <table class="list">
                        <colgroup>
                            <col width="150">
                            <col width="80">
                            <col width="260">
                            <col>
                        </colgroup>
                        <tbody>
                            $tr
                        </tbody>
                    </table>
                </div>
            </div>
        </article>
html;
    
    return $html;
}

/**
 * @brief 포인트 사용 팝업 html 생성
 *
 * @param $own_point = 보유포인트
 *
 * @return 팝업 html
 */
function pointPopup($own_point, $pay_price) {
    $pay_price = str_replace(",", "", $pay_price);
    $own_point = str_replace(",", "", $own_point);

    if ($own_point > $pay_price) {
        $max_use_point = number_format(doubleval($pay_price));
    } else {
        $max_use_point = number_format(doubleval($own_point));
    }
    $own_point = number_format(doubleval($own_point));

    $html = <<<html
        <header>
            <h2>포인트 현황</h2>
            <button class="close" title="닫기"><img src="/design_template/images/common/btn_circle_x_white.png" alt="X"></button>
        </header>
        <article>
            <ul class="amount">
                <li><label><h3>보유 포인트</h3> <input type="text" id="own_point" readonly value="$own_point"></label> P</li>
                <li><label><h3>최대 사용 가능 포인트</h3> <input type="text" id="max_use_point" readonly value="$max_use_point"></label> P</li>
                <li><label><h3>사용 포인트</h3> <input type="text" id="use_point" value="0"></label> P</li>
                <li><p class="note">포인트는 백원 단위로 사용이 가능합니다.</p></li>
            </ul>
            <div class="function center">
                <strong><button onclick="setPointPrice();">사용</button></strong>
                <button class="close">취소</button>
            </div>
        </article>
html;

    return $html;
}

/**
 * @brief 쿠폰 사용 팝업 html 생성
 *
 * @param $tbody = 쿠폰목록
 *
 * @return 팝업 html
 */
function couponPopup($tbody) {
    $html = <<<html
        <header>
            <h2>쿠폰 현황</h2>
            <button class="close" title="닫기"><img src="/design_template/images/common/btn_circle_x_white.png" alt="X"></button>
        </header>
        <article>
            <table class="list thead">
                <colgroup>
                    <col width="60">
                    <col width="220">
                    <col width="105">
                    <col width="150">
                    <col>
                </colgroup>
<!--
                <caption class="legend">○ : 적용 가능 / △ 조건 만족 시 사용 가능 / X : 적용 불가</caption>
-->
                <thead>
                    <tr>
                        <th><input type="checkbox" class="_general"></th>
                        <th>쿠폰명</th>
                        <th>할인금액</th>
                        <th>기간</th>
                    </tr>
                </thead>
            </table>
            <div class="tableScroll">
                <div class="wrap">
                    <table class="list">
                        <colgroup>
                    <col width="60">
                    <col width="220">
                    <col width="105">
                    <col>
                        </colgroup>
                        <tbody>
                            $tbody
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="function center">
                <strong><button type="button" onclick="aplyCoupon();">적용</button></strong>
                <button type="button" class="close">취소</button>
            </div>
        </article>
html;

    return $html;
}

/**
 * @brief 관리사업자 팝업 html 생성
 *
 * @param
 *
 * @return 팝업 html
 */
function organizerPopup($list) {

    $html = <<<html
<header>
    <h2>관리사업자 등록 리스트</h2>
    <button class="close" title="닫기"><img src="/design_template/images/common/btn_circle_x_white.png" alt="X"></button>
</header>
<article>
    <ul class="list">
        $list
    </ul>
    <div class="function center">
        <strong><button type="button" onclick="getOrganizerInfo();">확인</button></strong>
        <button class="close">취소</button>
    </div>
</article>
html;

    echo $html;
}
?>
