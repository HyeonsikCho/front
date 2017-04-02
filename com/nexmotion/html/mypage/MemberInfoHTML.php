<?
/* 
 * 회원정보 혜택 이벤트 list  생성 
 * return : list
 */
function makeEventListHtml($rs, $param) {
 
    if (!$rs) {
        return false;
    }

    $today = date("Y-m-d");

    $rs_html = "";
    $html  = "\n  <tr class='%s'>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class=\"subject\">%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n  </tr>";
    $i = $param["count"] - $param["s_num"];

    while ($rs && !$rs->EOF) {

        $class = "";
        $regi_date = date("Y-m-d", strtotime($rs->fields["regi_date"]));

        if ($today == $regi_date) {
            $class = "new";
        }

        $rs_html .= sprintf($html, $class, 
                $i,
                $regi_date,
                $rs->fields["event_typ"],
                $rs->fields["prdt_name"],
                $rs->fields["bnf"]);
        $i--;
        $rs->moveNext();
    }

    return $rs_html;
}

/* 
 * 주문 list  생성 
 * $result : $result->fields["order_regi_date"] = "주문등록일자" 
 * $result : $result->fields["order_num"] = "주문번호" 
 * $result : $result->fields["title"] = "인쇄물제목" 
 * $result : $result->fields["order_detail"] = "주문 상세" 
 * $result : $result->fields["amt"] = "수량" 
 * $result : $result->fields["pay_price"] = "결제금액" 
 * $result : $result->fields["order_state"] = "주문상태" 
 * $result : $result->fields["order_common_seqno"] = "주문 공통 일련번호" 
 * 
 * return : list
 */
function makeCpListHtml($result, $param) {

    $ret = "";
    
    $list .= "\n  <tr %s>";
    $list .= "\n    <td>%d</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td class=\"subject\">%s<span class=\"request\"> %s</span>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n  </tr>";

    $i = $param["count"] - $param["s_num"];

    if ($result) {
        $total_cnt = $result->recordCount();
    }

    while ($result && !$result->EOF) {

        $cp_name = $result->fields["cp_name"];
        $val = $result->fields["val"];
        $unit = $result->fields["unit"];
        $max_sale_price = $result->fields["max_sale_price"];
        $min_order_price = $result->fields["min_order_price"];
        $use_start_date = $result->fields["use_able_start_date"];

        $use_cnd = "";
        $use_val = "";
        $use_dvs = "";

        //요율일때
        if ($max_sale_price != "") {

            $use_cnd = "최대 ";
            $use_val = "&#8361;" . number_format($max_sale_price);
            $use_dvs = " 할인";
        }

        //금액일때
        if ($min_order_price != "") {

            $use_cnd = "주문금액 ";
            $use_val =  "&#8361;" . number_format($min_order_price);
            $use_dvs =  " 이상";
        } 

        $use_deadline = $result->fields["use_deadline"];
        $issue_date = $result->fields["issue_date"];
        $use_yn = $result->fields["use_yn"];
        $today = date("Y-m-d H:i:s", time());

        $state = "";
        //사용
        if ($use_yn == "N") {

            //사용기한이 현재 날짜보다 크고 현재날짜가 사용 가능 시작 일자보다 클때
            if ($today <= $use_deadline && $today >= $use_start_date) {
                $state = "사용가능";
                $class = "";

            } else if (!$use_deadline) {
                $state = "사용가능";
                $class = "";

            //현재 날짜가 사용기한보다 클때
            } else {
                $state = "기한만료";
                $class = "class=\"ended\"";
            }
        }

        $ret .= sprintf($list
                ,$class
                ,$i
                ,$cp_name
                ,number_format($val) . $unit
                ,$use_cnd
                ,$use_val
                ,$use_dvs
                ,substr($use_deadline, 0, 10)
                ,substr($issue_date, 0, 10)
                ,$state); 

        $i--;
        $result->moveNext();
    }

    return $ret;
}

/* 
 * 주문 list  생성 
 * $result : $result->fields["order_regi_date"] = "주문등록일자" 
 * $result : $result->fields["order_num"] = "주문번호" 
 * $result : $result->fields["title"] = "인쇄물제목" 
 * $result : $result->fields["order_detail"] = "주문 상세" 
 * $result : $result->fields["amt"] = "수량" 
 * $result : $result->fields["pay_price"] = "결제금액" 
 * $result : $result->fields["order_state"] = "주문상태" 
 * $result : $result->fields["order_common_seqno"] = "주문 공통 일련번호" 
 * 
 * return : list
 */
function makePointListHtml($conn, $result, $param, $type) {

    $ret = "";
    
    $list .= "\n  <tr>";
    $list .= "\n    <td>%d</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td %s>%s</td>";
    $list .= "\n    <td %s>%s</td>";
    $list .= "\n    <td %s>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n    <td>%s</td>";
    $list .= "\n  </tr>";

    $i = $param["count"] - $param["s_num"];

    if ($result) {
        $total_cnt = $result->recordCount();
    }

    while ($result && !$result->EOF) {

        $regi_date = substr($result->fields["regi_date"], 0,10);
        $dvs = $result->fields["dvs"];
        $point = $result->fields["point"];
        $dvs_class = "";
        $plus_class = "";
        $minus_class = "";

        if ($dvs == "적립") {

            $save_point = number_format($point);
            $use_point = "-";
            $dvs_class = " class=\"plus\"";
            $plus_class = " class=\"plus\"";

        } else if ($dvs == "사용") {

            $use_point = number_format($point);
            $save_point = "-";
            $dvs_class= " class=\"minus\"";
            $minus_class= " class=\"minus\"";

        }

        $order_num = $result->fields["order_num"];
        $order_price = number_format($result->fields["order_price"]);
        $rest_point = number_format($result->fields["rest_point"]);

        $ret .= sprintf($list
                ,$i
                ,$regi_date
                ,$dvs_class
                ,$dvs
                ,$plus_class
                ,$save_point
                ,$minus_class
                ,$use_point 
                ,$order_num
                ,"&#8361;" . $order_price
                ,$rest_point . "p"); 

        $i--;
        $result->moveNext();
    }

    return $ret;
}

/*
 * 거래 내역 리스트 생성
 *
 * return : list
 */
function makePrepaymentListHtml($rs, $param) {

    $list = "";
    $html  = "\n<tr>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>\%s</td>";
    $html .= "\n    <td class='plus'>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td><button type=\"button\" class=\"tableFunction\" onclick=\"goPrint('%s');\">출력</buttion></td>";
    $html .= "\n</tr>";
    $i = $param["count"];// = $param["snum"];

    while ($rs && !$rs->EOF) {
        $deal_date = substr($rs->fields["deal_date"], 0, 10);
        $cont = "선입금 충전(" . $rs->fields["depo_way"] . ")";
        $depo_price = number_format($rs->fields["depo_price"]);
        $state = $rs->fields["state"];
        $deal_num = $rs->fields["deal_num"];

        $list .= sprintf($html, $i, $deal_date
                , $cont, $depo_price
                , $state ,$deal_num);

        $i--;
        $rs->moveNext();
    }

    return $list;
}
?>
