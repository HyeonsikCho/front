<?
/**
 * @breif 빠른 견적서 html 반환
 *
 * @detail 판매금액에서 역으로 부가세 등 추출
 *
 * @param $info    = 정보 배열
 * @param $util    = 유틸 클래스 객체
 * @param $aft_arr = ProductInfoClass::AFTER_ARR
 *
 * @return 견적서 html
 */
function getQuickEstimateHtml($info, $util, $aft_arr) {
    $sell_price = $info["esti_sell_price"];
    $sale_price = $info["esti_sale_price"];

    $paper  = $util->ceilVal($info["esti_paper"] / 1.1);
    $output = $util->ceilVal($info["esti_output"] / 1.1);
    $print  = $util->ceilVal($info["esti_print"] / 1.1);
    $opt    = $util->ceilVal($info["esti_opt"] / 1.1);

    $after_html = '';
    $after_sum = 0;
    foreach ($aft_arr as $aft_ko => $aft_en) {
        $after = $info["esti_" . $aft_en];
        $attr = '';

        if (empty($after)) {
            $attr = "style=\"display:none;\"";
        }

        $after = $util->ceilVal($after / 1.1);
        $after_sum += $after;

        $after_html .= "\n     <dt id=\"esti_" . $aft_en . "_dt\" " . $attr . ">" . $aft_ko . "비</dt>";
        $after_html .= "\n     <dd id=\"esti_" . $aft_en . "_dd\" " . $attr . ">";
        $after_html .= "\n         <span id=\"esti_" . $aft_en . "\">";
        $after_html .=                 number_format($after);
        $after_html .= "\n         </span> 원";
        $after_html .= "\n     </dd>";
    }

    $supply_price = $paper + $output + $print + $after_sum + $opt;
    $tax = $util->ceilVal($supply_price / 10);

    $temp = $supply_price + $tax;

    if ($sell_price !== $temp) {
        $tax -=
            ($sell_price < $temp) ? $temp - $sell_price : $sell_price - $temp;
    }

    $attr = '';
    
    $html  = "\n <dl>";
    if ($paper === 0.0) {
        $attr = "style=\"display:none;\"";
    }
    $html .= "\n     <dt class=\"esti_paper_info\" " . $attr . ">종이비</dt>";
    $html .= "\n     <dd class=\"esti_paper_info\" " . $attr . ">";
    $html .= "\n         <span id=\"esti_paper\">" . number_format($paper) . "</span> 원";
    $html .= "\n     </dd>";

    $attr = '';
    if ($output === 0.0) {
        $attr = "style=\"display:none;\"";
    }
    $html .= "\n     <dt class=\"esti_output_info\" " . $attr . ">출력비</dt>";
    $html .= "\n     <dd class=\"esti_output_info\" " . $attr . ">";
    $html .= "\n         <span id=\"esti_output\">" . number_format($output) .  "</span> 원";
    $html .= "\n     </dd>";

    $attr = '';
    if ($print === 0.0) {
        $attr = "style=\"display:none;\"";
    }
    $html .= "\n     <dt class=\"esti_print_info\" " . $attr . ">인쇄비</dt>";
    $html .= "\n     <dd class=\"esti_print_info\" " . $attr . ">";
    $html .= "\n         <span id=\"esti_print\">" . number_format($print) . "</span> 원";
    $html .= "\n     </dd>";

    $html .= $after_html;

    $html .= "\n     <dt>옵션비</dt>";
    $html .= "\n     <dd><span id=\"esti_opt\">" . number_format($opt) . "</span> 원</dd>";
    $html .= "\n     <dt class=\"esti_count_info\">주문건</dt>";
    $html .= "\n     <dd class=\"esti_count_info\"><span id=\"esti_count\">1</span> 건</dd>";
    $html .= "\n     <dt>공급가</dt>";
    $html .= "\n     <dd><span id=\"esti_supply\">" . number_format($supply_price) . "</span> 원</dd>";
    $html .= "\n </dl>";
    $html .= "\n <dl class=\"price\">";
    $html .= "\n     <dt>부가세</dt>";
    $html .= "\n     <dd><span id=\"esti_tax\">" . number_format($tax) . "</span> 원</dd>";
    $html .= "\n     <dt>정상판매가</dt>";
    $html .= "\n     <dd><span id=\"esti_sell_price\">" . number_format($sell_price) . "</span> 원</dd>";
    $html .= "\n     <dt>할인금액</dt>";
    $html .= "\n     <dd><span id=\"esti_sale_price\">-" . number_format($sell_price - $sale_price) . "</span> 원</dd>";
    $html .= "\n     <dt>결제금액</dt>";
    $html .= "\n     <dd><strong id=\"esti_pay_price\">" . number_format($sale_price) . "</strong> 원</dd>";
    $html .= "\n </dl>";

    return $html;
}
?>
