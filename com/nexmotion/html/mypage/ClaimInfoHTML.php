<?
//클레임 리스트
function makeClaimListHtml($rs, $param) {

    $ret = "";
    $html  = "\n        <tbody class name=\"claim_list\">";
    $html .= "\n            <tr class=\"waiting %s\">";
    $html .= "\n                <td>%d</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td class=\"subject\"><a href=\"/mypage/claim_view.html?order_claim_seqno=%s\" target=\"_self\">%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>";
    $html .= "\n                    <button class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\" onclick=\"openOrderDetail('%d', '%s');\"><img src=\"/design_template/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>";
    $html .= "\n                    <button class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\" onclick=\"closeOrderDetail('%s');\"><img src=\"/design_template/images/common/btn_table_circle_top_green.png\" alt=\"▲\"></button>";
    $html .= "\n                </td>";
    $html .= "\n            </tr>";
    $html .= "\n            <tr class=\"_orderDetails\" id=\"detail%d\">";
    $html .= "\n            </tr>";
    $html .= "\n        </tbody>";
    $i = $param["count"] - $param["s_num"];
    while ($rs && !$rs->EOF) {

        $regi_date = $rs->fields["regi_date"];
        $today = date("Y-m-d", time());

        $class_style = "";
        if ($regi_date == $today) {
            $class_style = "class=\"new\"";
        }

        $ret .= sprintf($html, $class_style, $i
                , substr($regi_date, 0,10)
                , $rs->fields["order_num"]
                , $rs->fields["order_title"]
                , $rs->fields["order_claim_seqno"]
                , $rs->fields["claim_title"]
                , $rs->fields["dvs"]
                , $rs->fields["state"]
                , $i
                , $rs->fields["order_common_seqno"]
                , $i
                , $i);
        $i--;
        $rs->moveNext();
    }

    return $ret;
}

//기업회원을 제외한 주문리스트
function makeOrderClaimListHtml($conn, $dao, $rs, $param) {

    $ret = "";
    $html  = "\n        <tbody class name=\"claim_list\">";
    $html .= "\n            <tr>";
    $html .= "\n    <td><input type=\"radio\" value=\"%d\"";
    $html .= "name=\"claimChk\"></td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td class=\"subject\">%s</td>";
    $html .= "\n                <td>%s%s(%s)</td>";
    $html .= "\n                <td>%s원</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>";
    $html .= "\n                    <button class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\" onclick=\"openOrderDetail('%d', '%s');\"><img src=\"/design_template/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>";
    $html .= "\n                    <button class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\" onclick=\"closeOrderDetail('%s');\"><img src=\"/design_template/images/common/btn_table_circle_top_green.png\" alt=\"▲\"></button>";
    $html .= "\n                </td>";
    $html .= "\n            </tr>";
    $html .= "\n            <tr class=\"_orderDetails\" id=\"detail%d\">";
    $html .= "\n            </tr>";
    $html .= "\n        </tbody>";
    $i = $param["count"] - $param["s_num"];
    while ($rs && !$rs->EOF) {

        $param["order_common_seqno"] = $rs->fields["order_common_seqno"];
        $claim_count = $dao->selectClaimCount($conn, $param)->fields["cnt"];

        $ret .= sprintf($html, $rs->fields["order_common_seqno"]
                , substr($rs->fields["order_regi_date"], 0,10)
                , $rs->fields["order_num"]
                , $rs->fields["title"]
                , $rs->fields["order_detail"]
                , number_format($rs->fields["amt"])
                , $rs->fields["amt_unit_dvs"]
                , number_format($rs->fields["count"])
                , number_format($rs->fields["pay_price"])
                , number_format($claim_count)
                , $i
                , $rs->fields["order_common_seqno"]
                , $i
                , $i);
        $i--;
        $rs->moveNext();
    }

    return $ret;
}

//기업회원 주문리스트
function makeBusiOrderClaimListHtml($conn, $dao, $rs, $param) {

    $ret = "";
    $html  = "\n        <tbody class name=\"claim_list\">";
    $html .= "\n            <tr>";
    $html .= "\n    <td><input type=\"radio\" value=\"%d\"";
    $html .= "name=\"claimChk\"></td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td class=\"subject\">%s</td>";
    $html .= "\n                <td>%s%s(%s)</td>";
    $html .= "\n                <td>%s원</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>";
    $html .= "\n                    <button class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\" onclick=\"openOrderDetail('%d', '%s');\"><img src=\"/design_template/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>";
    $html .= "\n                    <button class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\" onclick=\"closeOrderDetail('%s');\"><img src=\"/design_template/images/common/btn_table_circle_top_green.png\" alt=\"▲\"></button>";
    $html .= "\n                </td>";
    $html .= "\n            </tr>";
    $html .= "\n            <tr class=\"_orderDetails\" id=\"detail%d\">";
    $html .= "\n            </tr>";
    $html .= "\n        </tbody>";
    $i = $param["count"] - $param["s_num"];
    while ($rs && !$rs->EOF) {

        $param["order_common_seqno"] = $rs->fields["order_common_seqno"];
        $claim_count = $dao->selectClaimCount($conn, $param)->fields["cnt"];

        $ret .= sprintf($html, $rs->fields["order_common_seqno"]
                , substr($rs->fields["order_regi_date"], 0,10)
                , $rs->fields["order_num"]
                , $rs->fields["member_name"]
                , $rs->fields["title"]
                , $rs->fields["order_detail"]
                , number_format($rs->fields["amt"])
                , $rs->fields["amt_unit_dvs"]
                , number_format($rs->fields["count"])
                , number_format($rs->fields["pay_price"])
                , number_format($claim_count)
                , $i
                , $rs->fields["order_common_seqno"]
                , $i
                , $i);
        $i--;
        $rs->moveNext();
    }

    return $ret;
}

//주문상세
function makeOrderClaimdetail($param) {

    $html .= "\n                 <td colspan=\"%d\">";
    $html .= "\n                     <div class=\"wrap\" style=\"display: block; height: 193px; padding-top: 0px; margin-top: 0px; padding-bottom: 0px; margin-bottom: 0px;\">";
    $html .= "\n                         <figure><img src=\"%s%s\" alt=\"%s\"></figure>";
    $html .= "\n                         <dl>";
    $html .= "\n                             <dt>주문일</dt>";
    $html .= "\n                             <dd>%s</dd>";
    $html .= "\n                             <dt>주문번호</dt>";
    $html .= "\n                             <dd>%s</dd>";
    $html .= "\n                             <dt>접수구분</dt>";
    $html .= "\n                             <dd>%s</dd>";
    $html .= "\n                         </dl>";
    $html .= "\n                         <dl>";
    $html .= "\n                             <dt>인쇄물 제목</dt>";
    $html .= "\n                             <dd>%s</dd>";
    $html .= "\n                         </dl>";
    $html .= "\n                         <dl>";
    $html .= "\n                             <dt>상품내역</dt>";
    $html .= "\n                             <dd>%s</dd>";
    $html .= "\n                         </dl>";
    $html .= "\n                         <dl>";
    $html .= "\n                             <dt>후공정</dt>";
    $html .= "\n                             <dd><ul class=\"information\">%s</ul></dd>";
    $html .= "\n                         </dl>";
    $html .= "\n                         <dl>";
    $html .= "\n                             <dt>수량/건</dt>";
    $html .= "\n                             <dd>%d x %d 건</dd>";
    $html .= "\n                         </dl>";
    $html .= "\n                         <dl>";
    $html .= "\n                             <dt>할인내역</dt>";
    $html .= "\n                             <dd>회원등급할인 %d / 이벤트할인 %d</dd>";
    $html .= "\n                         </dl>";
    $html .= "\n                         <dl>";
    $html .= "\n                             <dt>예상무게</dt>";
    $html .= "\n                             <dd>%sKg</dd>";
    $html .= "\n                             <dt>배송</dt>";
    $html .= "\n                             <dd>%s</dd>";
    $html .= "\n                         </dl>";
    $html .= "\n                     </div>";
    $html .= "\n                 </td>";

    $ret = sprintf($html, $param["colspan"]
            , $param["file_path"]
            , $param["save_file_name"]
            , $param["cate_name"]
            , substr($param["order_regi_date"], 0,10)
            , $param["order_num"]
            , $param["receipt_dvs"]
            , $param["title"]
            , $param["order_detail"]
            , $param["after_html"]
            , number_format($param["amt"])
            , number_format($param["count"])
            , number_format($param["grade_sale_price"])
            , number_format($param["event_price"])
            , $param["expec_weight"]
            , $param["dlvr_way"]);

    return $ret;
}

function makeClaimComment($result) {

    $list = "";

    while ($result && !$result->EOF) {

        $comment = $result->fields["comment"];
        $regi_date = $result->fields["regi_date"];
        $cust_yn = $result->fields["cust_yn"];

        if ($cust_yn == "Y") {

            $list .= "\n  <dl class=\"reply customer\">";
            $list .= "\n    <dt>고객님</dt>";
            $list .= "\n    <dd>" . $comment . "</dd>";
            $list .= "\n  </dl>";

        } else {

            $list .= "\n  <dl class=\"reply cs\">";
            $list .= "\n    <dt>담당자</dt>";
            $list .= "\n    <dd>" . $comment . "</dd>";
            $list .= "\n  </dl>";
        }
        $result->moveNext();
    }
    return $list;
}
?>
