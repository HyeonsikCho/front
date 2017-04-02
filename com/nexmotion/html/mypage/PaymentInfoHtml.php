<?
/*
 * 입출금 내역 리스트 생성
 *
 * return : list
 */
function makePaymentListHtml($rs) {

    $list = "";
    $html  = "\n<tr>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    %s";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    %s";
    $html .= "\n    %s";
    $html .= "\n</tr>";

    while ($rs && !$rs->EOF) {
        $deal_date = substr($rs->fields["deal_date"], 0, 10);
        $dvs = $rs->fields["dvs"];
        $cont = "";
        $depo_price = $rs->fields["depo_price"];
        $pay_price = $rs->fields["pay_price"];

        if ($depo_price == 0) {
            $depo_price = "<td>-</td>";
        } else {
            $depo_price = "<td class=\"plus\">\\" 
                . number_format($depo_price) . "</td>";
        }

        if ($pay_price == 0) {
            $pay_price = "<td>-</td>";
        } else {
            $pay_price = "<td class=\"minus\">\\" 
                . number_format($pay_price) . "</td>";
        }

        if ($dvs == "입금") {
            $cont = $rs->fields["depo_way"];
            $dvs = "<td class=\"plus\">입금</td>";
        } else {
            $cont = "제품구입사용[" . $rs->fields["title"] . "]";
            $dvs = "<td class=\"minus\">주문</td>";
        }

        $list .= sprintf($html, $deal_date, $dvs
                , $cont, $depo_price
                , $pay_price);

        $rs->moveNext();
    }

    return $list;
}

/*
 * 거래 내역 리스트 생성
 *
 * return : list
 */
function makeTransactionalInfoHtml($rs) {

    $list = "";
    $html  = "\n<tr>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class=''>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class='minus'>\%s</td>";
    $html .= "\n    <td class='plus'>\%s</td>";
    $html .= "\n    <td>\%s</td>";
    $html .= "\n</tr>";

    while ($rs && !$rs->EOF) {
        $deal_date = substr($rs->fields["deal_date"], 0, 10);
        $order_num = $rs->fields["order_num"];
        $order_detail = $rs->fields["order_detail"];

        if (!$rs->fields["amt"]) {
            $amt = "-";
        } else if ($rs->fields["amt"] == "-1" ) {
            $amt = $rs->fields["amt_unit_dvs"];
        } else {
            $amt = number_format($rs->fields["amt"]) . $rs->fields["amt_unit_dvs"];
        }

        if ( $rs->fields["count"] > 0 ) {
            $count = number_format($rs->fields["count"]) . "건";
        } else {
            $count = "";
        }

        $sell_price = number_format($rs->fields["sell_price"]);
        $sale_price = number_format($rs->fields["sale_price"]);
        $pay_price = number_format($rs->fields["pay_price"]);

        $list .= sprintf($html, $deal_date
                , $order_num, $order_detail
                , $amt, $count
                , $sell_price, $sale_price
                , $pay_price);

        $rs->moveNext();
    }

    return $list;
}

/*
 * 거래 내역 리스트 생성
 *
 * return : list
 */
function makeTransactionPrintHtml($rs) {

    $list = "";
    $html .= "\n        <tr>";
    $html .= "\n            <td>%d</td>";
    $html .= "\n            <td>%s</td>";
    $html .= "\n            <td class=\"table_left\">%s</td>";
    $html .= "\n            <td class=\"table_left\">%s</td>";
    $html .= "\n            <td>%s</td>";
    $html .= "\n            <td>%s</td>";
    $html .= "\n            <td>&#8361;%s</td>";
    $html .= "\n            <td>&#8361;%s</td>";
    $html .= "\n            <td>&#8361;%s</td>";
    $html .= "\n        </tr>";

    $i = 1;
    while ($rs && !$rs->EOF) {
        $deal_date = substr($rs->fields["deal_date"], 5, 5);
        $title = $rs->fields["title"];
        $order_detail = $rs->fields["order_detail"];

        if (!$rs->fields["amt"]) {
            $amt = "-";
        } else if ($rs->fields["amt"] == "-1" ) {
            $amt = $rs->fields["amt_unit_dvs"]; 
        } else {
            $amt = number_format($rs->fields["amt"]) . $rs->fields["amt_unit_dvs"];
        }

        if ( $rs->fields["count"] > 0 ) {
            $count = number_format($rs->fields["count"]);
        } else {
            $count = "";
        }

        $sell_price = number_format($rs->fields["sell_price"]);
        $sale_price = number_format($rs->fields["sale_price"]);
        $pay_price = number_format($rs->fields["pay_price"]);

        $list .= sprintf($html, $i, $deal_date
                , $title
                , $order_detail
                , $amt, $count
                , $sell_price, $sale_price
                , $pay_price);

        $rs->moveNext();
        $i++;
    }

    return $list;
}

/*
 * 거래 내역 리스트 생성
 *
 * return : list
 */
function makeTransactionPriceHtml($p_rs, $d_rs, $b_rs) {

    $sell_price = $p_rs->fields["sell_price"];
    $sale_price = $p_rs->fields["sale_price"];
    $pay_price = $p_rs->fields["pay_price"];
    $depo_price = $d_rs->fields["depo_price"];
    $prepay_bal = $b_rs->fields["prepay_bal"];

    $html  = "";
    $html .= "\n    <caption class=\"table_right\">";
    $html .= "\n        총매출액 : &#8361;" . number_format($sell_price) . "원";
    $html .= "/ 에누리 : &#8361;" . number_format($sale_price) . "원";
    $html .= "/ 순매출액 : &#8361;" . number_format($pay_price) . "원";
    $html .= "/ 입금액 : &#8361;" . number_format($depo_price) . "원";
    $html .= "/ 일일잔액 : &#8361;" . number_format($prepay_bal) . "원";
    $html .= "\n    </caption>";

    return $html;
}

?>
