<?
/**
 * @param 장바구니 리스트 html 생성
 *
 * @param $conn = connection identifier
 * @param $dao  = 후공정, 사진경로 검색용
 * @param $rs   = 검색결과
 * @param $price_info_arr = 가격정보배열
 *
 * return 장바구니 리스트 html
 */
function makeCartOrderListHtml($conn, $dao, $rs, &$price_info_arr) {
    $ret = "";

    $upper_tr  = "<tr>";
    $upper_tr .= "    <td><input type=\"checkbox\" name=\"seq\" class=\"_individual\" value=\"%s\"></td>"; //#0 주문공통일련번호
    $upper_tr .= "    <td>%s</td>"; //#1 번호
    $upper_tr .= "    <td>%s</td>"; //#2 담은날
    $upper_tr .= "    <td id=\"title_td_%s\">%s</td>"; //#3 순번, 인쇄물제목
    $upper_tr .= "    <td class=\"subject\">";
    $upper_tr .= "        <ul class=\"information\">";
    $upper_tr .= "            %s"; //#4 상품정보
    $upper_tr .= "        </ul>";
    $upper_tr .= "    </td>";
    $upper_tr .= "    <td>%s%s(%s건)</td>"; //#5 수량
    $upper_tr .= "    <td>%s 원</td>"; //#6
    $upper_tr .= "    <td>";
    $upper_tr .= "\n      <button class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\" onclick=\"openOrderDetail('%s', '%s', '8');\"><img src=\"/design_template/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>"; //#7 번호, seqno
    $upper_tr .= "\n      <button class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\" onclick=\"closeOrderDetail('%s');\"><img src=\"/design_template/images/common/btn_table_circle_top_green.png\" alt=\"▲\"></button>"; //#8 번호
    $upper_tr .= "    </td>";
    $upper_tr .= "</tr>";

    $lower_tr .= "<tr class=\"_orderDetails\" id=\"detail%s\">";
    $lower_tr .= "</tr>";

    $sum_sell_price      = 0;
    $sum_grade_sale_price = 0;
    $sum_event_price      = 0;

    $cate_photo_arr = array();
    $i = 1;
    while ($rs && !$rs->EOF) {
        $fields = $rs->fields;

        $cate_sortcode = $fields["cate_sortcode"];

        // 사진 경로 검색
        if ($cate_photo_arr[$cate_sortcode] === null) {
            $cate_photo_arr[$cate_sortcode] = 
                $dao->selectCatePhoto($conn, $cate_sortcode);
        }

        /*
        $amt          = '';
        $amt_unit_dvs = '';
        $count        = '';
        $order_detail_dvs_num = '';

        $s_amt = doubleval($fields["s_amt"]);
        if (empty($s_amt) === false) {
            if ($s_amt < 1) {
                $amt = number_format($s_amt, 1);
            } else {
                $amt = number_format($s_amt);
            }

            $amt_unit_dvs = $fields["s_amt_unit"];
            $count        = $fields["s_count"];
            $order_detail_dvs_num = $fields["s_order_detail_dvs_num"];
        }

        $b_amt = doubleval($fields["b_amt"]);
        if (empty($b_amt) === false) {
            if (empty($amt) === true) {
                if ($b_amt < 1) {
                    $amt = number_format($b_amt, 1);
                } else {
                    $amt = number_format($b_amt);
                }

                $amt_unit_dvs = $fields["b_amt_unit"];
                $count        = '1';
                $order_detail_dvs_num = $fields["b_order_detail_dvs_num"];
            } else {
                $amt = "혼합형";
            }
        }
        */

        $order_common_seqno = $fields["order_common_seqno"];
        $order_regi_date    = $fields["order_regi_date"];
        $title              = $fields["title"];
        $order_detail       = $fields["order_detail"];
        $sell_price         = doubleval($fields["sell_price"]);
        $grade_sale_price   = doubleval($fields["grade_sale_price"]);
        $add_opt_price      = doubleval($fields["add_opt_price"]);
        $add_after_price    = doubleval($fields["add_after_price"]);
        $event_price        = doubleval($fields["event_price"]);
        $expec_weight       = $fields["expec_weight"];
        $amt                = $fields["amt"];
        $amt_unit_dvs       = $fields["amt_unit_dvs"];
        $count              = $fields["count"];

        if (empty($expec_weight) === true || $expec_weight === '0') {
            $expec_weight = '-';
        } else {
            $expec_weight .= "Kg";
        }

        // 가격합산 정보 생성
        $sum_sell_price       += $sell_price;
        $sum_grade_sale_price += $grade_sale_price * -1;
        $sum_event_price      += $event_price;
        $sum_add_after_price  += $add_after_price;
        $sum_add_opt_price    += $add_opt_price;

        $sell_price += $grade_sale_price;
        $sell_price += $add_after_price;
        $sell_price += $add_opt_price;

        // 후공정 html 부분 생성
        /*
        $param = array("order_detail_dvs_num" => $order_detail_dvs_num);
        $after_rs =
            $dao->selectOrderAfterHistory($conn, $param);
        $after_ul = "";

        while ($after_rs && !$after_rs->EOF) {
            $after_fields = $after_rs->fields;

            $name = $after_fields["name"];
            $depth1 = $after_fields["depth1"];
            $depth2 = $after_fields["depth2"];
            $depth3 = $after_fields["depth3"];

            $after_ul .= "<ul class=\"information\">";
            $after_ul .= "    <li>" . $name . "</li>";

            if ($depth1 !== '-') {
                $after_ul .= "    <li>" . $depth1 . "</li>";
            }
            if ($depth2 !== '-') {
                $after_ul .= "    <li>" . $depth2 . "</li>";
            }
            if ($depth3 !== '-') {
                $after_ul .= "    <li>" . $depth3 . "</li>";
            }

            $after_ul .= "</ul>";

            $after_rs->MoveNext();
        }
        */

        // 전체 html 생성
        $ret .= "<tbody>";

        $ret .= sprintf($upper_tr, $order_common_seqno               //#0
                                 , $i                                //#1
                                 , explode(' ', $order_regi_date)[0] //#2
                                 , $order_common_seqno               //#3
                                 , $title                            //#3
                                 , $order_detail                     //#4
                                 , $amt                              //#5
                                 , $amt_unit_dvs                     //#5
                                 , $count                            //#5
                                 , number_format($sell_price)        //#6
                                 , $i                                //#7
                                 , $order_common_seqno               //#7
                                 , $i);                            //#8

        $ret .= sprintf($lower_tr, $i++);

        $ret .= "</tbody>";

        $rs->MoveNext();
    }

    $price_info_arr["sell"]  = $sum_sell_price +
                               $sum_add_after_price +
                               $sum_add_opt_price;
    $price_info_arr["grade"] = $sum_grade_sale_price;
    $price_info_arr["event"] = $sum_event_price;
    $price_info_arr["sum"] = $sum_sell_price +
                             $sum_add_after_price +
                             $sum_add_opt_price -
                             $sum_grade_sale_price -
                             $sum_event_price;

    return $ret;
}
?>
