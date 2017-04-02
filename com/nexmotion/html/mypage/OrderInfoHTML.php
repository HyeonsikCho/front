<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/common_info.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_lib/CommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/mypage/OrderInfoDAO.php");

//기업회원을 제외한 주문리스트
function makeOrderListHtml($rs, $param) {

    $util = new CommonUtil();

    $ret = "";
    $html  = "\n        <tbody class name=\"order_list\">";
    $html .= "\n            <tr>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td class=\"subject\">%s</td>";
    $html .= "\n                <td>%s%s%s</td>";
    $html .= "\n                <td class='minus'>%s원</td>";
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
        if (empty($rs->fields["order_common_seqno"])) {
            $rs->MoveNext();
            continue;
        }

        if (!empty($rs->fields["amt"]))  {
            $amt = doubleval($rs->fields["amt"]);

            if ($amt < 1) {
                $amt = number_format($amt, 1);
            } else {
                $amt = number_format($amt);
            }
        } else {
            $amt = "";
        }

        if (!empty($rs->fields["count"])) {
            $count = "(" . number_format($rs->fields["count"]) . ")";
        } else {
            $count = "";
        }

        $pay_price  = doubleval($rs->fields["pay_price"]);

        $ret .= sprintf($html, $i
                , substr($rs->fields["order_regi_date"], 0,10)
                , $rs->fields["order_num"]
                , $rs->fields["title"]
                , $rs->fields["order_detail"]
                , $amt
                , $rs->fields["amt_unit_dvs"]
                , $count
                , number_format($pay_price)
                , $util->statusCode2status($rs->fields["order_state"])
                , $i
                , $rs->fields["order_common_seqno"]
                , $i
                , $i);
        $i--;
        $rs->moveNext();
    }

    if ($rs->recordCount() == 0) {
        $ret  = "\n        <tbody class name=\"order_list\">";
        $ret .= "\n            <tr>";
        $ret .= "\n                <td colspan=\"9\">주문이 없습니다.</td>";
        $ret .= "\n            </tr>";
        $ret .= "\n        </tbody>";
    }

    return $ret;
}

//기업회원 주문리스트
function makeBusiOrderListHtml($rs, $param) {

    $util = new CommonUtil();

    $ret = "";
    $html  = "\n        <tbody class name=\"order_list\">";
    $html .= "\n            <tr>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td class=\"subject\">%s</td>";
    $html .= "\n                <td>%s%s%s</td>";
    $html .= "\n                <td class='minus'>%s원</td>";
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
        if (empty($rs->fields["order_common_seqno"])) {
            $rs->MoveNext();
            continue;
        }

        if (!empty($rs->fields["amt"]))  {
            $amt = doubleval($rs->fields["amt"]);

            if ($amt < 1) {
                $amt = number_format($amt, 1);
            } else {
                $amt = number_format($amt);
            }
        } else {
            $amt = "";
        }

        if (!empty($rs->fields["count"])) {
            $count = "(" . number_format($rs->fields["count"]) . ")";
        } else {
            $count = "";
        }

        $pay_price  = doubleval($rs->fields["pay_price"]);
        $dlvr_price = doubleval($rs->fields["dlvr_price"]);

        $ret .= sprintf($html, $i
                , substr($rs->fields["order_regi_date"], 0,10)
                , $rs->fields["order_num"]
                , $rs->fields["member_name"]
                , $rs->fields["title"]
                , $rs->fields["order_detail"]
                , $amt
                , $rs->fields["amt_unit_dvs"]
                , $count
                , number_format($pay_price + $dlvr_price)
                , $util->statusCode2status($rs->fields["order_state"])
                , $i
                , $rs->fields["order_common_seqno"]
                , $i
                , $i);
        $i--;
        $rs->moveNext();
    }

    return $ret;
}

//기업회원을 제외한 미결제주문리스트
function makeOrderUnpaidListHtml($rs, $param) {

    $util = new CommonUtil();

    $ret = "";
    $html  = "\n        <tbody class name=\"order_list\">";
    $html .= "\n            <tr>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td class=\"subject\">%s</td>";
    $html .= "\n                <td>%s%s%s</td>";
    $html .= "\n                <td class='minus'>%s원</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td><button class=\"tableFunction\" onclick=\"orderPopup('l_orderCancel', '/design_template/mypage/popup/l_ordercancel.html', %d, '')\"><img src=\"/design_template/images/mypage/btn_text_ordercancel.png\" alt=\"주문취소\"></button></button></td>";
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
        if (empty($rs->fields["order_common_seqno"])) {
            $rs->MoveNext();
            continue;
        }

        if (!empty($rs->fields["amt"]))  {
            $amt = doubleval($rs->fields["amt"]);

            if ($amt < 1) {
                $amt = number_format($amt, 1);
            } else {
                $amt = number_format($amt);
            }
        } else {
            $amt = "";
        }

        if (!empty($rs->fields["count"])) {
            $count = "(" . number_format($rs->fields["count"]) . ")";
        } else {
            $count = "";
        }

        $ret .= sprintf($html, $i
                , substr($rs->fields["order_regi_date"], 0,10)
                , $rs->fields["order_num"]
                , $rs->fields["title"]
                , $rs->fields["order_detail"]
                , $amt
                , $rs->fields["amt_unit_dvs"]
                , $count
                , number_format($rs->fields["pay_price"])
                , $util->statusCode2status($rs->fields["order_state"])
                , $rs->fields["order_common_seqno"]
                , $i
                , $rs->fields["order_common_seqno"]
                , $i
                , $i);
        $i--;
        $rs->moveNext();
    }

    return $ret;
}

//기업회원 미결제주문리스트
function makeBusiOrderUnpaidListHtml($rs, $param) {

    $util = new CommonUtil();

    $ret = "";
    $html  = "\n        <tbody class name=\"order_list\">";
    $html .= "\n            <tr>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td class=\"subject\">%s</td>";
    $html .= "\n                <td>%s%s%s</td>";
    $html .= "\n                <td class='minus'>%s원</td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td><button class=\"tableFunction\" onclick=\"orderPopup('l_orderCancel', '/design_template/mypage/popup/l_ordercancel.html', %d, '')\"><img src=\"/design_template/images/mypage/btn_text_ordercancel.png\" alt=\"주문취소\"></button></button></td>";
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
        if (empty($rs->fields["order_common_seqno"])) {
            $rs->MoveNext();
            continue;
        }

        if (!empty($rs->fields["amt"]))  {
            $amt = doubleval($rs->fields["amt"]);

            if ($amt < 1) {
                $amt = number_format($amt, 1);
            } else {
                $amt = number_format($amt);
            }
        } else {
            $amt = "";
        }

        if (!empty($rs->fields["count"])) {
            $count = "(" . number_format($rs->fields["count"]) . ")";
        } else {
            $count = "";
        }

        $ret .= sprintf($html, $i
                , substr($rs->fields["order_regi_date"], 0,10)
                , $rs->fields["order_num"]
                , $rs->fields["member_name"]
                , $rs->fields["title"]
                , $rs->fields["order_detail"]
                , $amt
                , $rs->fields["amt_unit_dvs"]
                , $count
                , number_format($rs->fields["pay_price"])
                , $util->statusCode2status($rs->fields["order_state"])
                , $rs->fields["order_common_seqno"]
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
function makeOrderdetail($param, $util, $opt_rs, $aft_rs, $file_rs) {
    $conn = $param["conn"];
    $dao  = $param["dao"];
    $btn_flag = $param["btn_flag"];
    $order_state_arr = $param["order_state_arr"];

    $order_common_seqno = $param["order_common_seqno"];
    $order_state        = $param["order_state"];
    $dlvr_way           = DLVR_TYP[$param["dlvr_way"]];

    $html .= "\n <td colspan=\"" . $param["colspan"] . "\">"; //#1 colspan
    /*
    $html .= "\n <td colspan=\"%s\">"; //#1 colspan
    $html .= "\n     <div class=\"wrap\" style=\"display: block; padding-top: 0px; margin-top: 0px; padding-bottom: 0px; margin-bottom: 0px;\">";
    $html .= "\n         <figure><img src=\"%s%s\"></figure>";
    $html .= "\n         <dl>";
    $html .= "\n             <dt>인쇄물 제목</dt>";
    $html .= "\n             <dd>%s</dd>"; //#2 title
    $html .= "\n         </dl>";
    $html .= "\n     </div>";

    $html  = sprintf($html, $param["colspan"]
                          , $param["file_path"]
                          , $param["save_file_name"]
                          , $param["title"]);
    */
    $detail_base  = "\n     <div class=\"wrap\" style=\"display: block; padding-top: 0px; margin-top: 0px; padding-bottom: 0px; margin-bottom: 0px;\">";
    $detail_base .= "\n         <figure><img src=\"\"></figure>";
    $detail_base .= "\n         <dl>";
    $detail_base .= "\n             <dt>상품내역</dt>";
    $detail_base .= "\n             <dd>%s</dd>"; //#1 order_detail
    $detail_base .= "\n         </dl>";
    $detail_base .= "\n         <dl>";
    $detail_base .= "\n             <dt>옵션</dt>";
    $detail_base .= "\n             <dd><ul class=\"information\">%s</ul></dd>"; //#2-1 option
    $detail_base .= "\n         </dl>";
    $detail_base .= "\n         <dl>";
    $detail_base .= "\n             <dt>후공정</dt>";
    $detail_base .= "\n             <dd><ul class=\"information\">%s</ul></dd>"; //#2-2 after
    $detail_base .= "\n         </dl>";
    $detail_base .= "\n         <dl>";
    $detail_base .= "\n             <dt>주문파일</dt>";
    $detail_base .= "\n             <dd><ul class=\"information\">";
    $detail_base .= "\n             %s"; //#2-3 order_file
    $detail_base .= "\n             </ul></dd>";
    $detail_base .= "\n         </dl>";
    $detail_base .= "\n         <dl>";
    $detail_base .= "\n             <dt>수량/건</dt>";
    $detail_base .= "\n             <dd>%s%s X %s건</dd>"; //#3 amt, amt_unit_dvs, count
    $detail_base .= "\n         </dl>";
    $detail_base .= "\n         <dl>";
    $detail_base .= "\n             <dt>할인내역</dt>";
    $detail_base .= "\n             <dd>회원등급할인 %s원</dd>"; //#4 member_grade_sale, 차후에 이벤트랑 포인트 추가 필요
    $detail_base .= "\n         </dl>";
    $detail_base .= "\n         <dl>";
    $detail_base .= "\n             <dt>예상무게</dt>";
    $detail_base .= "\n             <dd>%sKg</dd>"; //#5 expec_weight
    $detail_base .= "\n         </dl>";
    $detail_base .= "\n         <dl>";
    $detail_base .= "\n             <dt>배송</dt>";
    $detail_base .= "\n             <dd>[%s] %s %s %s</dd>"; //#6 dlvr_way, zipcode, addr, addr_detail
    $detail_base .= "\n         </dl>";
    $detail_base .= "\n         <dl>";
    $detail_base .= "\n             <dt>결제금액</dt>";
    $detail_base .= "\n             <dd class=\"minus\">판매가 %s원 + 후공정 %s원 + 옵션 %s원 + 배송비 %s원</dd>"; //#7 pay_price
    $detail_base .= "\n         </dl>";
    $detail_base .= "\n     </div>";

    $after_li_base = "\n            <li>%s</li>";

    $file_base = "\n                 <span onclick=\"downOrderFile('%s', '%s');\" style=\"cursor:pointer;\">%s<span><br/>";

    $after_html = '';
    while ($aft_rs && !$aft_rs->EOF) {
        $fields = $aft_rs->fields;

        // 16-12-17 ujh 기본후공정 안보여주게 수정
        if ($fields["basic_yn"] === 'Y') {
            $aft_rs->MoveNext();
            continue;
        }

        $aft = sprintf("%s(%s)", $fields["after_name"]
                               , $util->getOptAfterFullName($fields));

        $after_html .= sprintf($after_li_base, $aft);

        $aft_rs->MoveNext();
    }

    $opt_html = '';
    while ($opt_rs && !$opt_rs->EOF) {
        $fields = $opt_rs->fields;

        // 16-12-17 ujh 기본옵션 안보여주게 수정
        if ($fields["basic_yn"] === 'Y') {
            $opt_rs->MoveNext();
            continue;
        }

        $opt = sprintf("%s(%s)", $fields["opt_name"]
                               , $util->getOptAfterFullName($fields));

        $opt_html .= sprintf($after_li_base, $opt);

        $opt_rs->MoveNext();
    }

    $file_html = '';
    while ($file_rs && !$file_rs->EOF) {
        $fields = $file_rs->fields;

        $file_html .= sprintf($file_base, $order_common_seqno
                                        , $fields["order_file_seqno"]
                                        , $fields["origin_file_name"]);

        $file_rs->MoveNext();
    }

    $amt = doubleval($param["amt"]);
    $count = number_format(doubleval($param["count"]));

    if ($amt < 1) {
        $amt = number_format($amt, 1);
    } else {
        $amt = number_format($amt);
    }


    $sell_price       = doubleval($param["sell_price"]);
    $grade_sale_price = doubleval($param["grade_sale_price"]);

    $pay_price        = number_format(doubleval($param["pay_price"]));
    $event_price      = number_format(doubleval($param["event_price"]));
    $use_point_price  = number_format(doubleval($param["use_point_price"]));
    $add_after_price  = number_format(doubleval($param["add_after_price"]));
    $add_opt_price    = number_format(doubleval($param["add_opt_price"]));
    $dlvr_price       = number_format(doubleval($param["dlvr_price"]));

    $html .= sprintf($detail_base, $param["order_detail"] //#1
                                 , $opt_html //#2-1
                                 , $after_html //#2-2
                                 , $file_html //#2-3
                                 , $amt //#3
                                 , $param["amt_unit_dvs"] //#3
                                 , $count //#3
                                 , number_format($grade_sale_price) //#4
                                 , $param["expec_weight"] //#5
                                 , $dlvr_way //#6
                                 , $param["zipcode"] //#6
                                 , $param["addr"] //#6
                                 , $param["addr_detail"] //#6
                                 , number_format($sell_price + $grade_sale_price) //#7
                                 , $add_after_price //#7
                                 , $add_opt_price   //#7
                                 , $dlvr_price  //#7
                                 );

    $html .= "\n     <div class=\"wrap\" style=\"display: block; padding-top: 0px; margin-top: 0px; padding-bottom: 0px; margin-bottom: 0px;\">";
    $html .= "\n         <figure></figure>";
    /*
    $html .= "\n         <dl>";
    $html .= "\n             <dt>공통할인내역</dt>";
    $html .= "\n             <dd>이벤트할인 %s</dd>";
    $html .= "\n         </dl>";
    */

    $btn_base  = "\n         <dl>";
    $btn_base .= "\n             <dt>관리</dt>";
    $btn_base .= "\n             <dd>";
    $btn_base .= "\n               <button onclick=\"showOrderCancelPop('%s');\"><img src=\"/design_template/images/mypage/btn_text_ordercancel.png\" alt=\"주문취소\"></button>"; //#1 order_common_seqno
    $btn_base .= "\n               <button onclick=\"showDraftPop('%s');\"><img src=\"/design_template/images/mypage/btn_text_draft.png\" alt=\"시안보기\"></button>"; //#2 order_detail_seqno
    $btn_base .= "\n               <button><img src=\"/design_template/images/mypage/btn_text_deliverytracking.png\" alt=\"배송조회\"></button>";
    //$btn_base .= "\n               <button onclick=\"showReOrderPop('%s');\"><img src=\"/design_template/images/mypage/btn_text_reorder.png\" alt=\"재주문\"></button>"; //#3 order_common_seqno
    $btn_base .= "\n               <button onclick=\"showReUploadPop('%s', '%s');\"><img src=\"/design_template/images/mypage/btn_text_resend.png\" alt=\"파일재전송\"></button>"; //#4 order_common_seqno, order_state
    $btn_base .= "\n               <button onclick=\"reqClaim('%s', '%s');\"><img src=\"/design_template/images/mypage/btn_text_claim.png\" alt=\"클레임요청\"></button>"; //#5 order_common_sqeno, order_state
    $btn_base .= "\n               <button onclick=\"showOrderMemoPop('%s');\"><img src=\"/design_template/images/mypage/btn_text_memo.png\" alt=\"메모\"></button>";
    $btn_base .= "\n               </dd>";
    $btn_base .= "\n         </dl>";

    if ($btn_flag === true) {
        $html .= sprintf($btn_base, $order_common_seqno #1
                                  , $order_common_seqno #2
                                  //, $order_common_seqno #3
                                  , $order_common_seqno #4
                                  , $order_state        #4
                                  , $order_common_seqno #5
                                  , $order_state        #5
                                  , $order_common_seqno); #6
    }

    $html .= "\n     </div>";
    $html .= "\n </td>";

    //$html  = sprintf($html, number_format($param["event_price"]));

    return $html;
}

//관심상품리스트
function makePrdtListHtml($conn, $rs, $param) {

    $util = new CommonUtil();

    $ret = "";
    $html  = "\n        <tbody class name=\"prdt_list\">";
    $html .= "\n            <tr>";
    $html .= "\n                <td><input type=\"checkbox\" name=\"chk[]\" value=\"%s\" class=\"_individual\"></td>";
    $html .= "\n                <td>%s</td>";
    $html .= "\n                <td class=\"subject\">";
    $html .= "\n                    <span onclick=\"moveProduct('%s');\" style=\"cursor:pointer;\">%s</span>";
    $html .= "\n                </td>";
    /*
    $html .= "\n                <td>%s(%s)</td>";
    $html .= "\n                <td>%s</td>";
    */
    $html .= "\n            </tr>";
    $html .= "\n        </tbody>";

    while ($rs && !$rs->EOF) {

        $ret .= sprintf($html
                , $rs->fields["interest_prdt_seqno"]
                , substr($rs->fields["regi_date"], 0,10)
                , $rs->fields["cate_sortcode"]
                , $rs->fields["cate_name"]);
                /*
                , number_format($rs->fields["amt"])
                , number_format($rs->fields["count"])
                , $rs->fields["interest_prdt_seqno"]);
                */

        $rs->moveNext();
    }

    return $ret;
}
?>
