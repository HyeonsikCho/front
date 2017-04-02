<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");

/**
 * @brief 추가 후공정 코팅 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getCoatingInfoHtml($info, $dvs) {
    $util = new FrontCommonUtil();

    $info_count = count($info);

    $option = "";
    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];

        $mpcode = $temp["mpcode"];
        $aft = $util->getOptAfterFullName($temp);

        $class = "";

        if (strpos($aft, "부분") !== false) {
            $class = "class=\"_part\"";
        }

        $option .= option($mpcode, $aft, $attr);
    }

    $html = <<<html
        <div class="option _coating">
            <dl>
                <dt>코팅</dt>
                <dd class="price" id="{$dvs}_coating_price_dd"></dd>
                <dd>
                    <select id="{$dvs}_coating_val" name="{$dvs}_coating_val" onchange="getAfterPrice.common('coating', '{$dvs}');">
                        $option
                    </select>
                    <p class="note _part">File에 부분코팅 부분을 먹1도로 업로드 해주세요.</p>
                    <input type="hidden" id="{$dvs}_coating_price" name="{$dvs}_coating_price" value="" />
                </dd>
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 귀도리 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getRoundingInfoHtml($info, $dvs) {
    $info_count = count($info);

    $merge_arr = array();

    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];
        $depth1 = $temp["depth1"];
        $depth2 = $temp["depth2"];
        $mpcode = $temp["mpcode"];

        $merge_arr[$depth1][$depth2] = $mpcode;
    }

    $depth1_option = "";
    $depth2_option = "";

    $flag = true;
    foreach ($merge_arr as $depth1 => $depth2_arr) {
        $attr = "";

        if ($flag === true) {
            $flag = false;

            foreach ($depth2_arr as $depth2 => $mpcode) {
                $depth2_option .= option($mpcode, $depth2);
            }

            $attr = "selected=\"selected\"";
        }

        if ($depth1 === "네귀도리") {
            $attr = "class=\"_all\"";
        }

        $depth1_option .= option($depth1, $depth1, $attr);
    }

    $html = <<<html
        <div class="option _rounding">
            <dl>
                <dt>귀도리</dt>
                <dd class="price" id="{$dvs}_rounding_price_dd"></dd>
                <dd>
                    <select class="_num" id="{$dvs}_rounding_cnt" onchange="loadRoundingDepth2(this.value, '{$dvs}');">
                        $depth1_option
                    </select>
                    <select id="{$dvs}_rounding_val" name="{$dvs}_rounding_val" onchange="getAfterPrice.common('rounding', '{$dvs}');">
                        $depth2_option
                    </select>
                </dd>
                <dd class="br">
                    <label class="left top"><input name="{$dvs}_rounding_dvs" value="좌상" type="checkbox" onchange="chkRoundingLimit('{$dvs}', this);"> 좌상</label>
                    <label class="right top"><input name="{$dvs}_rounding_dvs" value="우상" type="checkbox" onchange="chkRoundingLimit('{$dvs}', this);"> 우상</label>
                    <label class="right bottom"><input name="{$dvs}_rounding_dvs" value="우하" type="checkbox" onchange="chkRoundingLimit('{$dvs}', this);"> 우하</label>
                    <label class="left bottom"><input name="{$dvs}_rounding_dvs" value="좌하" type="checkbox" onchange="chkRoundingLimit('{$dvs}', this);"> 좌하</label>
                    <input type="hidden" id="{$dvs}_rounding_info" name="{$dvs}_rounding_info" value="" />
                    <input type="hidden" id="{$dvs}_rounding_price" name="{$dvs}_rounding_price" value="" />
                </dd>
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 오시 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getImpressionInfoHtml($info, $dvs) {
    $info_count = count($info);

    // html 생성하기 쉽도록 배열 가공
    $html_info = array();

    $option = "";
    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];

        $mpcode = $temp["mpcode"];
        $depth1 = $temp["depth1"];
        $depth2 = $temp["depth2"];

        $html_info[$depth1][$depth2] = $mpcode;
    }

    $is_one   = false;
    $is_two   = false;
    $is_three = false;
    $is_four  = false;

    // 1줄
    $one_mpcode1 = $html_info["1줄"]["중앙"];
    $one_mpcode2 = $html_info["1줄"]["비중앙"];
    if (!empty($one_mpcode1) || !empty($one_mpcode_2)) {
        $is_one   = true;
    }
    // 2줄
    $two_mpcode1 = $html_info["2줄"]["비례3단"];
    $two_mpcode2 = $html_info["2줄"]["십자4단"];
    $two_mpcode3 = $html_info["2줄"]["비대칭다단"];
    if (!empty($two_mpcode1) ||
            !empty($two_mpcode_2) ||
            !empty($two_mpcode_3)) {
        $is_two   = true;
    }
    // 3줄
    $three_mpcode1 = $html_info["3줄"]["비례4단"];
    $three_mpcode2 = $html_info["3줄"]["비대칭다단"];
    if (!empty($three_mpcode1) || !empty($three_mpcode_2)) {
        $is_three = true;
    }
    // 4줄
    $four_mpcode1 = $html_info["4줄"]["비례5단"];
    $four_mpcode2 = $html_info["4줄"]["비대칭다단"];
    if (!empty($four_mpcode1) || !empty($four_mpcode_2)) {
        $is_four  = true;
    }

    // 조건때문에 doc에서 str로 변경함
    $html  = "    <div class=\"option _impression\">";
    $html .= "        <dl>";
    $html .= "            <dt>오시</dt>";
    $html .= "            <dd class=\"price\" id=\"{$dvs}_impression_price_dd\"></dd>";
    $html .= "            <dd>";
    $html .= "                <select id=\"{$dvs}_impression_cnt\" onchange=\"getAfterPrice.common('impression', '{$dvs}');\">";
    if ($is_one) {
        $html .= "                    <option value=\"one\" class=\"_one\">1줄</option>";
    }
    if ($is_two) {
        $html .= "                    <option value=\"two\" class=\"_two\">2줄</option>";
    }
    if ($is_three) {
        $html .= "                    <option value=\"three\" class=\"_three\">3줄</option>";
    }
    if ($is_four) {
        $html .= "                    <option value=\"four\" class=\"_four\">4줄</option>";
    }
    $html .= "                </select>";
    $html .= "            </dd>";
    // 1줄
    $html .= "            <dd class=\"br _one\">";
    if (isset($one_mpcode1)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_impression_one_val\" onclick=\"getAfterPrice.common('impression', '{$dvs}');\" value=\"{$one_mpcode1}\" dvs=\"M\"> 중앙2단</label>";
    }
    if (isset($one_mpcode2)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_impression_one_val\" onclick=\"getAfterPrice.common('impression', '{$dvs}');\" value=\"{$one_mpcode2}\" class=\"_custom\" dvs=\"C\"> 비중앙2단</label>";
    }
    $html .= "            </dd>";
    if (isset($one_mpcode2)) {
        $html .= "            <dd class=\"br _one _custom\">";
        $html .= "                <label>첫 번째 선 <input id=\"{$dvs}_impression_one_pos1\" type=\"text\" class=\"mm {$dvs}_impression_one_mm\">mm</label>";
        $html .= "            </dd>";
    }

    // 2줄
    $html .= "            <dd class=\"br _two\">";
    if (isset($two_mpcode1)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_impression_two_val\" onclick=\"getAfterPrice.common('impression', '{$dvs}');\" value=\"{$two_mpcode1}\" dvs=\"M\"> 비례3단</label>";
    }
    if (isset($two_mpcode2)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_impression_two_val\" onclick=\"getAfterPrice.common('impression', '{$dvs}');\" value=\"{$two_mpcode2}\" dvs=\"M\"> 십자4단</label>";
    }
    if (isset($two_mpcode3)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_impression_two_val\" onclick=\"getAfterPrice.common('impression', '{$dvs}');\" value=\"{$two_mpcode3}\" dvs=\"C\" class=\"_custom\"> 비대칭다단</label>";
    }
    $html .= "            </dd>";
    if (isset($two_mpcode3)) {
        $html .= "            <dd class=\"br _two _custom\">";
        $html .= "                <label>첫 번째 선 <input id=\"{$dvs}_impression_two_pos1\" type=\"text\" class=\"mm {$dvs}_impression_two_mm\" onblur=\"aftRestrict.impression.common('{$dvs}');\">mm</label><br />";
        $html .= "                <label>두 번째 선 <input id=\"{$dvs}_impression_two_pos2\" type=\"text\" class=\"mm {$dvs}_impression_two_mm\" onblur=\"aftRestrict.impression.common('{$dvs}');\"\">mm</label>";
        $html .= "            </dd>";
    }

    // 3줄
    $html .= "            <dd class=\"br _three\">";
    if (isset($three_mpcode1)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_impression_three_val\" onclick=\"getAfterPrice.common('impression', '{$dvs}');\" value=\"{$three_mpcode1}\" dvs=\"M\"> 비례4단</label>";
    }
    if (isset($three_mpcode2)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_impression_three_val\" onclick=\"getAfterPrice.common('impression', '{$dvs}');\" value=\"{$three_mpcode2}\" dvs=\"C\" class=\"_custom\"> 비대칭다단</label>";
    }
    $html .= "            </dd>";
    if (isset($three_mpcode2)) {
        $html .= "            <dd class=\"br _three _custom\">";
        $html .= "                <label>첫 번째 선 <input id=\"{$dvs}_impression_three_pos1\" type=\"text\" class=\"mm {$dvs}_impression_three_mm\" onblur=\"aftRestrict.impression.common('{$dvs}');\">mm</label><br />";
        $html .= "                <label>두 번째 선 <input id=\"{$dvs}_impression_three_pos2\" type=\"text\" class=\"mm {$dvs}_impression_three_mm\" onblur=\"aftRestrict.impression.common('{$dvs}');\">mm</label><br />";
        $html .= "                <label>세 번째 선 <input id=\"{$dvs}_impression_three_pos3\" type=\"text\" class=\"mm {$dvs}_impression_three_mm\" onblur=\"aftRestrict.impression.common('{$dvs}');\">mm</label>";
        $html .= "            </dd>";
    }

    // 4줄
    $html .= "            <dd class=\"br _four\">";
    if (isset($four_mpcode1)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_impression_four_val\" onclick=\"getAfterPrice.common('impression', '{$dvs}');\" value=\"{$four_mpcode1}\" dvs=\"M\"> 비례5단</label>";
    }
    if (isset($four_mpcode2)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_impression_four_val\" onclick=\"getAfterPrice.common('impression', '{$dvs}');\" value=\"{$four_mpcode2}\" dvs=\"C\" class=\"_custom\"> 비대칭다단</label>";
    }
    $html .= "            </dd>";
    if (isset($four_mpcode2)) {
        $html .= "            <dd class=\"br _four _custom\">";
        $html .= "                <label>첫 번째 선 <input id=\"{$dvs}_impression_four_pos1\" type=\"text\" class=\"mm {$dvs}_impression_four_mm\" onblur=\"aftRestrict.impression.common('{$dvs}');\">mm</label><br />";
        $html .= "                <label>두 번째 선 <input id=\"{$dvs}_impression_four_pos2\" type=\"text\" class=\"mm {$dvs}_impression_four_mm\" onblur=\"aftRestrict.impression.common('{$dvs}');\">mm</label><br />";
        $html .= "                <label>세 번째 선 <input id=\"{$dvs}_impression_four_pos3\" type=\"text\" class=\"mm {$dvs}_impression_four_mm\" onblur=\"aftRestrict.impression.common('{$dvs}');\">mm</label><br />";
        $html .= "                <label>네 번째 선 <input id=\"{$dvs}_impression_four_pos4\" type=\"text\" class=\"mm {$dvs}_impression_four_mm\" onblur=\"aftRestrict.impression.common('{$dvs}');\">mm</label>";
        $html .= "            </dd>";
    }
    $html .= "            <input type=\"hidden\" id=\"{$dvs}_impression_price\" name=\"{$dvs}_impression_price\" value=\"\" />";
    $html .= "            <input type=\"hidden\" id=\"{$dvs}_impression_info\" name=\"{$dvs}_impression_info\" value=\"\" />";
    $html .= "            <input type=\"hidden\" id=\"{$dvs}_impression_val\" name=\"{$dvs}_impression_val\" value=\"\" />";
    $html .= "        </dl>";
    $html .= "    </div>";

    return $html;
}

/**
 * @brief 추가 후공정 미싱 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getDotlineInfoHtml($info, $dvs) {
    $info_count = count($info);

    // html 생성하기 쉽도록 배열 가공
    $html_info = array();

    $option = "";
    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];

        $mpcode = $temp["mpcode"];
        $depth1 = $temp["depth1"];
        $depth2 = $temp["depth2"];

        $html_info[$depth1][$depth2] = $mpcode;
    }

    $is_one   = false;
    $is_two   = false;
    $is_three = false;
    $is_four  = false;

    // 1줄
    $one_mpcode1 = $html_info["1줄"]["중앙"];
    $one_mpcode2 = $html_info["1줄"]["비중앙"];
    if (!empty($one_mpcode1) || !empty($one_mpcode_2)) {
        $is_one   = true;
    }
    // 2줄
    $two_mpcode1 = $html_info["2줄"]["비례3단"];
    $two_mpcode2 = $html_info["2줄"]["비대칭다단"];
    if (!empty($two_mpcode1) || !empty($two_mpcode_2)) {
        $is_two   = true;
    }
    // 3줄
    $three_mpcode1 = $html_info["3줄"]["비례4단"];
    $three_mpcode2 = $html_info["3줄"]["비대칭다단"];
    if (!empty($three_mpcode1) || !empty($three_mpcode_2)) {
        $is_three = true;
    }
    // 4줄
    $four_mpcode1 = $html_info["4줄"]["비례5단"];
    $four_mpcode2 = $html_info["4줄"]["비대칭다단"];
    if (!empty($four_mpcode1) || !empty($four_mpcode_2)) {
        $is_four  = true;
    }

    // 조건때문에 doc에서 str로 변경함
    $html  = "    <div class=\"option _dotline\">";
    $html .= "        <dl>";
    $html .= "            <dt>미싱</dt>";
    $html .= "            <dd id=\"{$dvs}_dotline_price_dd\" class=\"price\"></dd>";
    $html .= "            <dd>";
    $html .= "                <select id=\"{$dvs}_dotline_cnt\" onchange=\"getAfterPrice.common('dotline', '{$dvs}');\">";
    if ($is_one) {
        $html .= "                    <option value=\"one\" class=\"_one\">1줄</option>";
    }
    if ($is_two) {
        $html .= "                    <option value=\"two\" class=\"_two\">2줄</option>";
    }
    if ($is_three) {
        $html .= "                    <option value=\"three\" class=\"_three\">3줄</option>";
    }
    if ($is_four) {
        $html .= "                    <option value=\"four\" class=\"_four\">4줄</option>";
    }
    $html .= "                </select>";
    $html .= "            </dd>";
    // 1줄
    $html .= "            <dd class=\"br _one\">";
    if (isset($one_mpcode1)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_dotline_one_val\" onclick=\"getAfterPrice.common('dotline', '{$dvs}');\" value=\"{$one_mpcode1}\" dvs=\"M\"> 중앙</label>";
    }
    if (isset($one_mpcode2)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_dotline_one_val\" onclick=\"getAfterPrice.common('dotline', '{$dvs}');\" value=\"{$one_mpcode2}\" class=\"_custom\" dvs=\"C\"> 비중앙</label>";
    }
    $html .= "            </dd>";
    if (isset($one_mpcode2)) {
        $html .= "            <dd class=\"br _one _custom\">";
        $html .= "                <label>첫 번째 선 <input id=\"{$dvs}_dotline_one_pos1\" type=\"text\" class=\"mm\">mm</label>";
        $html .= "            </dd>";
    }

    // 2줄
    $html .= "            <dd class=\"br _two\">";
    if (isset($two_mpcode1)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_dotline_two_val\" onclick=\"getAfterPrice.common('dotline', '{$dvs}');\" value=\"{$two_mpcode1}\" dvs=\"M\"> 비례3단</label>";
    }
    if (isset($two_mpcode2)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_dotline_two_val\" onclick=\"getAfterPrice.common('dotline', '{$dvs}');\" value=\"{$two_mpcode2}\" class=\"_custom\" dvs=\"C\"> 비대칭다단</label>";
    }
    $html .= "            </dd>";
    if (isset($two_mpcode2)) {
        $html .= "            <dd class=\"br _two _custom\">";
        $html .= "                <label>첫 번째 선 <input id=\"{$dvs}_dotline_two_pos1\" type=\"text\" class=\"mm\">mm</label><br />";
        $html .= "                <label>두 번째 선 <input id=\"{$dvs}_dotline_two_pos2\" type=\"text\" class=\"mm\">mm</label>";
        $html .= "            </dd>";
    }

    // 3줄
    $html .= "            <dd class=\"br _three\">";
    if (isset($three_mpcode1)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_dotline_three_val\" onclick=\"getAfterPrice.common('dotline', '{$dvs}');\" value=\"$three_mpcode1\" dvs=\"M\"> 비례4단</label>";
    }
    if (isset($three_mpcode2)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_dotline_three_val\" onclick=\"getAfterPrice.common('dotline', '{$dvs}');\" value=\"$three_mpcode2\" class=\"_custom\" dvs=\"C\"> 비대칭다단</label>";
    }
    $html .= "            </dd>";
    if (isset($three_mpcode2)) {
        $html .= "            <dd class=\"br _three _custom\">";
        $html .= "                <label>첫 번째 선 <input id=\"{$dvs}_dotline_three_pos1\" type=\"text\" class=\"mm\">mm</label><br />";
        $html .= "                <label>두 번째 선 <input id=\"{$dvs}_dotline_three_pos2\" type=\"text\" class=\"mm\">mm</label><br />";
        $html .= "                <label>세 번째 선 <input id=\"{$dvs}_dotline_three_pos3\" type=\"text\" class=\"mm\">mm</label>";
        $html .= "            </dd>";
    }

    // 4줄
    $html .= "            <dd class=\"br _four\">";
    if (isset($four_mpcode1)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_dotline_four_val\" onclick=\"getAfterPrice.common('dotline', '{$dvs}');\" value=\"$four_mpcode1\" dvs=\"M\"> 비례5단</label>";
    }
    if (isset($four_mpcode2)) {
        $html .= "                <label><input type=\"radio\" name=\"{$dvs}_dotline_four_val\" onclick=\"getAfterPrice.common('dotline', '{$dvs}');\" value=\"$four_mpcode2\" class=\"_custom\" dvs=\"C\"> 비대칭다단</label>";
    }
    $html .= "            </dd>";
    if (isset($four_mpcode2)) {
        $html .= "            <dd class=\"br _four _custom\">";
        $html .= "                <label>첫 번째 선 <input id=\"{$dvs}_dotline_four_pos1\" type=\"text\" class=\"mm\">mm</label><br />";
        $html .= "                <label>두 번째 선 <input id=\"{$dvs}_dotline_four_pos2\" type=\"text\" class=\"mm\">mm</label><br />";
        $html .= "                <label>세 번째 선 <input id=\"{$dvs}_dotline_four_pos3\" type=\"text\" class=\"mm\">mm</label><br />";
        $html .= "                <label>네 번째 선 <input id=\"{$dvs}_dotline_four_pos4\" type=\"text\" class=\"mm\">mm</label>";
        $html .= "            </dd>";
    }
    $html .= "            <input type=\"hidden\" id=\"{$dvs}_dotline_price\" name=\"{$dvs}_dotline_price\" value=\"\" />";
    $html .= "            <input type=\"hidden\" id=\"{$dvs}_dotline_info\" name=\"{$dvs}_dotline_info\" value=\"\" />";
    $html .= "            <input type=\"hidden\" id=\"{$dvs}_dotline_val\" name=\"{$dvs}_dotline_val\" value=\"\" />";
    $html .= "        </dl>";
    $html .= "    </div>";

    return $html;
}

/**
 * @brief 추가 후공정 타공 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getPunchingInfoHtml($info, $dvs) {
    $info_count = count($info);

    $merge_arr = array();

    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];
        $depth1 = $temp["depth1"];
        $depth2 = $temp["depth2"];
        $mpcode = $temp["mpcode"];

        $merge_arr[$depth1][$depth2] = $mpcode;
    }

    $depth1_option = "";
    $depth2_option = "";

    $flag = true;
    $i = 1;
    foreach ($merge_arr as $depth1 => $depth2_arr) {
        $attr = "";

        if ($flag === true) {
            $flag = false;

            foreach ($depth2_arr as $depth2 => $mpcode) {
                $depth2_option .= option($mpcode, $depth2);
            }

            $attr = "selected=\"selected\"";
        }

        $depth1_option .= option($i++, $depth1, $attr);
    }

    $html = <<<html
        <div class="option _punching">
            <dl>
                <dt>타공</dt>
                <dd id="{$dvs}_punching_price_dd" class="price"></dd>
                <dd>
                    <select class="_num" id="{$dvs}_punching" onchange="loadPunchingDepth2(this.value, '{$dvs}');">
                        $depth1_option
                    </select>
                    <select name="{$dvs}_punching_val" id="{$dvs}_punching_val" onchange="getAfterPrice.common('punching', '{$dvs}');">
                        $depth2_option
                    </select>
                </dd>
                <dd class="br _on">
                    첫 번째 타공 위치
                    <label>가로 <input type="text" id="{$dvs}_punching_pos_w1" class="mm">mm</label>
                    <label>세로 <input type="text" id="{$dvs}_punching_pos_h1" class="mm">mm</label>
                </dd>
                <dd class="br">
                    두 번째 타공 위치
                    <label>가로 <input type="text" id="{$dvs}_punching_pos_w2" class="mm">mm</label>
                    <label>세로 <input type="text" id="{$dvs}_punching_pos_h2" class="mm">mm</label>
                </dd>
                <dd class="br">
                    세 번째 타공 위치
                    <label>가로 <input type="text" id="{$dvs}_punching_pos_w3" class="mm">mm</label>
                    <label>세로 <input type="text" id="{$dvs}_punching_pos_h3" class="mm">mm</label>
                 </dd>
                 <dd class="br">
                    네 번째 타공 위치
                    <label>가로 <input type="text" id="{$dvs}_punching_pos_w4" class="mm">mm</label>
                    <label>세로 <input type="text" id="{$dvs}_punching_pos_h4" class="mm">mm</label>
                </dd>
                <dd class="br note">
                    File에 타공 부분을 먹1도로 업로드 해주세요.
                </dd>
                <input type="hidden" id="{$dvs}_punching_price" name="{$dvs}_punching_price" value="" />
                <input type="hidden" id="{$dvs}_punching_info" name="{$dvs}_punching_info" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 접지 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getFoldlineInfoHtml($info, $dvs) {
    $info_count = count($info);

    $merge_arr = array();

    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];
        $depth1 = $temp["depth1"];
        $depth2 = $temp["depth2"];
        $mpcode = $temp["mpcode"];

        $merge_arr[$depth1][$depth2] = $mpcode;
    }

    $depth1_option = "";
    $depth2_option = "";

    $flag = true;
    foreach ($merge_arr as $depth1 => $depth2_arr) {
        $attr = "";

        if ($flag === true) {
            $flag = false;

            foreach ($depth2_arr as $depth2 => $mpcode) {
                $depth2_option .= option($mpcode, $depth2);
            }

            $attr = "selected=\"selected\"";
        }

        $depth1_option .= option($depth1, $depth1, $attr);
    }

    $html = <<<html
        <div class="option _foldline">
            <dl>
                <dt>접지</dt>
                <dd id="{$dvs}_foldline_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_foldline" onchange="loadFoldlineDepth2(this.value, '{$dvs}');">
                        $depth1_option
                    </select>
                    <select id="{$dvs}_foldline_val" onchange="getAfterPrice.common('foldline', '{$dvs}');">
                        $depth2_option
                    </select>
                </dd>
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 엠보싱 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getEmbossingInfoHtml($info, $dvs) {
    $info_count = count($info);

    $merge_arr = array();

    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];
        $depth1 = $temp["depth1"];
        $depth2 = $temp["depth2"];
        $mpcode = $temp["mpcode"];

        $merge_arr[$depth1][$depth2] = $mpcode;
    }

    $depth1_option = "";
    $depth2_option = "";

    $flag = true;
    foreach ($merge_arr as $depth1 => $depth2_arr) {
        $attr = "";

        if ($flag === true) {
            $flag = false;

            foreach ($depth2_arr as $depth2 => $mpcode) {
                $depth2_option .= option($mpcode, $depth2);
            }

            $attr = "selected=\"selected\"";
        }

        $depth1_option .= option($depth1, $depth1, $attr);
    }

    $html = <<<html
        <div class="option _embossing">
            <dl>
                <dt>엠보싱</dt>
                <dd id="{$dvs}_embossing_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_embossing" onchange="loadEmbossingDepth2(this.value, '{$dvs}');">
                        $depth1_option
                    </select>
                    <select id="{$dvs}_embossing_val" name="{$dvs}_embossing_val" onchange="getAfterPrice.common('embossing', '{$dvs}');">
                        $depth2_option
                    </select>
                </dd>
                <input type="hidden" id="{$dvs}_embossing_price" name="{$dvs}_embossing_price" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 박 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getFoilInfoHtml($info, $dvs) {
    $info_count = count($info);

    $dup_chk = array();

    $opt1 = '';
    $opt2 = '';

    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];
        $depth1 = $temp["depth1"];
        $attr = '';

        if ($i === 0) {
            $attr = "selected";
        }

        if ($dup_chk[$depth1] === null) {
            $dup_chk[$depth1] = true;
            $opt1 .= option($depth1, $depth1, $attr);
            $opt2 .= option($depth1, $depth1);
        }
    }

    $html = <<<html
        <div class="option _foil">
            <dl>
                <dt>박</dt>
                <dd id="{$dvs}_foil_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_foil_1" style="width:85px;" onchange="foilAreaInit('{$dvs}', this.value, '1');">
                        <option value="">-</option>
                        $opt1
                    </select>
                    <select id="{$dvs}_foil_dvs_1" style="min-width:60px;" onchange="changeFoilDvs('{$dvs}', this.value);">
                        <option value="">-</option>
                        <option value="전면" selected>전면</option>
                        <option value="양면">양면</option>
                    </select>
                    &nbsp;/&nbsp;
                    <select id="{$dvs}_foil_2" style="width:85px;" onchange="foilAreaInit('{$dvs}', this.value, '2');">
                        <option value="">-</option>
                        $opt2
                    </select>
                    <select id="{$dvs}_foil_dvs_2" style="min-width:60px;" onchange="getAfterPrice.common('foil', '{$dvs}');">
                        <option value="">-</option>
                        <option value="후면">후면</option>
                    </select>
                    <input type="hidden" id="{$dvs}_foil_val_1" name="{$dvs}_foil_val" value="" />
                    <input type="hidden" id="{$dvs}_foil_val_2" name="{$dvs}_foil_val_2" value="" />
                    <input type="hidden" id="{$dvs}_foil_info" name="{$dvs}_foil_info" value="" />
                    <input type="hidden" id="{$dvs}_foil_price" name="{$dvs}_foil_price" value="" />
                </dd>
                <dd class="br">
                    <label>가로 <input id="{$dvs}_foil_wid_1" type="text" class="mm" onblur="getAfterPrice.common('foil', '{$dvs}');">mm</label>
                    <label>세로 <input id="{$dvs}_foil_vert_1" type="text" class="mm" onblur="getAfterPrice.common('foil', '{$dvs}');">mm</label>
                    &nbsp;/&nbsp;
                    <label>가로 <input id="{$dvs}_foil_wid_2" type="text" class="mm" onblur="getAfterPrice.common('foil', '{$dvs}');">mm</label>
                    <label>세로 <input id="{$dvs}_foil_vert_2" type="text" class="mm" onblur="getAfterPrice.common('foil', '{$dvs}');">mm</label>
                </dd>
                <dd class="br note">
                    File에 박 부분을 먹1도로 업로드 해주세요.
                </dd>
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 형압 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getPressInfoHtml($info, $dvs) {
    $info_count = count($info);

    $merge_arr = array();

    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];
        $depth1 = $temp["depth1"];
        $depth2 = $temp["depth2"];
        $mpcode = $temp["mpcode"];

        $merge_arr[$depth1][$depth2] = $mpcode;
    }

    $depth1_option = "";
    $depth2_option = "";

    $flag = true;
    foreach ($merge_arr as $depth1 => $depth2_arr) {
        $attr = "";

        if ($flag === true) {
            $flag = false;

            foreach ($depth2_arr as $depth2 => $mpcode) {
                $depth2_option .= option($mpcode, $depth2);
            }

            $attr = "selected=\"selected\"";
        }

        $depth1_option .= option($depth1, $depth1, $attr);
    }

    $html = <<<html
        <div class="option _press">
            <dl>
                <dt>형압</dt>
                <dd id="{$dvs}_press_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_press_1" onchange="loadPressDepth2(this.value, '{$dvs}');">
                        $depth1_option
                    </select>
                    <select id="{$dvs}_press_val" name="{$dvs}_press_val" onchange="getAfterPrice.common('press', '{$dvs}');">
                        $depth2_option
                    </select>
                    <input type="hidden" id="{$dvs}_press_info" name="{$dvs}_press_info" value="" />
                    <input type="hidden" id="{$dvs}_press_price" name="{$dvs}_press_price" value="" />
                </dd>
                <dd class="br">
                    <label>가로 <input id="{$dvs}_press_wid_1" type="text" class="mm"  onblur="getAfterPrice.common('press', '{$dvs}');">mm</label>
                    <label>세로 <input id="{$dvs}_press_vert_1" type="text" class="mm" onblur="getAfterPrice.common('press', '{$dvs}');">mm</label>
                </dd>
                <dd class="br note">
                    File에 형압 부분을 먹1도로 업로드 해주세요.
                </dd>
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 도무송 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getTomsonInfoHtml($info, $dvs) {
    $info_count = count($info);

    $merge_arr = array();

    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];
        $depth1 = $temp["depth1"];
        $depth2 = $temp["depth2"];
        $mpcode = $temp["mpcode"];

        $merge_arr[$depth1][$depth2] = $mpcode;
    }

    $depth1_option = "";
    $depth2_option = "";

    $flag = true;
    foreach ($merge_arr as $depth1 => $depth2_arr) {
        $attr = "";

        if ($flag === true) {
            $flag = false;

            foreach ($depth2_arr as $depth2 => $mpcode) {
                $depth2_option .= option($mpcode, $depth2);
            }

            $attr = "selected=\"selected\"";
        }

        $depth1_option .= option($depth1, $depth1, $attr);
    }

    $html = <<<html
        <div class="option _thomson">
            <dl>
                <dt>도무송</dt>
                <dd id="{$dvs}_thomson_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_thomson" onchange="loadThomsonDepth2(this.value, '{$dvs}');">
                        $depth1_option
                    </select>
                    <select id="{$dvs}_thomson_val" name="{$dvs}_thomson_val" onchange="getAfterPrice.common('thomson', '{$dvs}');">
                        $depth2_option
                    </select>
                </dd>
                <input type="hidden" id="{$dvs}_thomson_price" name="{$dvs}_thomson_price" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 넘버링 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getNumberingInfoHtml($info, $dvs) {
    $info_count = count($info);

    $merge_arr = array();

    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];
        $depth1 = $temp["depth1"];
        $depth2 = $temp["depth2"];
        $mpcode = $temp["mpcode"];

        $merge_arr[$depth1][$depth2] = $mpcode;
    }

    $depth1_option = "";
    $depth2_option = "";

    $flag = true;
    foreach ($merge_arr as $depth1 => $depth2_arr) {
        $attr = "";

        if ($flag === true) {
            $flag = false;

            foreach ($depth2_arr as $depth2 => $mpcode) {
                $depth2_option .= option($mpcode, $depth2);
            }

            $attr = "selected=\"selected\"";
        }

        $depth1_option .= option($depth1, $depth1, $attr);
    }

    $html = <<<html
        <div class="option _numbering">
            <dl>
                <dt>넘버링</dt>
                <dd id="{$dvs}_numbering_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_numbering" onchange="loadDepth2(this.value, '{$dvs}');">
                        $depth1_option
                    </select>
                    <select id="{$dvs}_numbering_val" name="{$dvs}_numbering_val" onchange="getAfterPrice.common('numbering', '{$dvs}');">
                        $depth2_option
                    </select>
                </dd>
                <input type="hidden" id="{$dvs}_numbering_price" name="{$dvs}_numbering_price" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 재단 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getCuttingInfoHtml($info, $dvs) {
    $info_count = count($info);

    $option = '';
    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];

        $option .= option($temp["mpcode"], $temp["depth1"]);
    }

    $html = <<<html
        <div class="option _cutting">
            <dl>
                <dt>재단</dt>
                <dd id="{$dvs}_cutting_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_cutting_val" name="{$dvs}_cutting_val" onchange="getAfterPrice.common('cutting', '{$dvs}');">
                        {$option}
                    </select>
                </dd>
                <input type="hidden" id="{$dvs}_cutting_price" name="{$dvs}_cutting_price" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 제본 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getBindingInfoHtml($info, $dvs) {
    $info_count = count($info);

    $merge_arr = array();

    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];
        $depth1 = $temp["depth1"];
        $depth2 = $temp["depth2"];
        $mpcode = $temp["mpcode"];

        $merge_arr[$depth1][$depth2] = $mpcode;
    }

    $depth1_option = "";
    $depth2_option = "";

    $flag = true;
    foreach ($merge_arr as $depth1 => $depth2_arr) {
        $attr = "";

        if ($flag === true) {
            $flag = false;

            foreach ($depth2_arr as $depth2 => $mpcode) {
                $depth2_option .= option($mpcode, $depth2);
            }

            $attr = "selected=\"selected\"";
        }

        $depth1_option .= option($depth1, $depth1, $attr);
    }

    $html = <<<html
        <div class="option _binding">
            <dl>
                <dt>제본</dt>
                <dd id="{$dvs}_binding_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_binding" onchange="loadBindingDepth2(this.value, '{$dvs}');">
                        $depth1_option
                    </select>
                    <select id="{$dvs}_binding_val" onchange="getAfterPrice.common('binding', '{$dvs}');">
                        $depth2_option
                    </select>
                </dd>
                <input type="hidden" id="{$dvs}_binding_price" name="{$dvs}_binding_price" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 접착 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getBondingInfoHtml() {
    $info_count = count($info);

    $merge_arr = array();

    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];
        $depth1 = $temp["depth1"];
        $depth2 = $temp["depth2"];
        $mpcode = $temp["mpcode"];

        $merge_arr[$depth1][$depth2] = $mpcode;
    }

    $depth1_option = "";
    $depth2_option = "";

    $flag = true;
    foreach ($merge_arr as $depth1 => $depth2_arr) {
        $attr = "";

        if ($flag === true) {
            $flag = false;

            foreach ($depth2_arr as $depth2 => $mpcode) {
                $depth2_option .= option($mpcode, $depth2);
            }

            $attr = "selected=\"selected\"";
        }

        $depth1_option .= option($depth1, $depth1, $attr);
    }

    $html = <<<html
        <div class="option _bonding">
            <dl>
                <dt>접착</dt>
                <dd id="{$dvs}_bonding_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_bonding" onchange="loadBondingDepth2(this.value, '{$dvs}');">
                        $depth1_option
                    </select>
                    <select id="{$dvs}_bonding_val" onchange="getAfterPrice.common('bonding', '{$dvs}');">
                        $depth2_option
                    </select>
                </dd>
                <input type="hidden" id="{$dvs}_bonding_price" name="{$dvs}_bonding_price" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 라미넥스 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getLaminexInfoHtml($info, $dvs) {
    $option = option($info["mpcode"], "라미넥스");

    $html = <<<html
        <div class="option _laminex">
            <dl>
                <dt>라미넥스</dt>
                <dd id="{$dvs}_laminex_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_laminex_val" onchange="getAfterPrice.common('laminex', '{$dvs}');">
                        $option
                    </select>
                </dd>
                <dd>
                    <label><input type="text" id="{$dvs}_laminex_info" name="{$dvs}_laminex_info" class="page" value=""> 매</label>
                </dd>
                <input type="hidden" id="{$dvs}_laminex_price" name="{$dvs}_laminex_price" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 가공 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getManufactureInfoHtml($info, $dvs) {
    $info_count = count($info);

    $option = "";
    for ($i = 0; $i < $info_count; $i++) {
        $temp = $info[$i];

        $mpcode = $temp["mpcode"];
        $aft    = $temp["depth1"];


        $option .= option($mpcode, $aft);
    }

    $html = <<<html
        <div class="option _manufacture">
            <dl>
                <dt>가공</dt>
                <dd id="{$dvs}_manufacture_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_manufacture" onchange="getAfterPrice.common('manufacture', '{$dvs}');">
                        $option
                    </select>
                </dd>
                <input type="hidden" id="{$dvs}_manufacture_price" name="{$dvs}_manufacture_price" value="" />
            </dl>
        </div>
html;

    return $html;
}

/**
 * @brief 추가 후공정 복권 실크 정보 html 반환
 *
 * @param $info = 후공정 정보
 * @param $dvs  = 제품구분값
 *
 * @return div html
 */
function getLotterySilkInfoHtml($info, $dvs) {
    $option = option($info["mpcode"], "복권실크");

    $html = <<<html
        <div class="option _lotterysilk">
            <dl>
                <dt>복권실크</dt>
                <dd id="{$dvs}_lotterysilk_price_dd" class="price"></dd>
                <dd>
                    <select id="{$dvs}_lotterysilk_val" onchange="getAfterPrice.common('lotterysilk', '{$dvs}');">
                        $option
                    </select>
                </dd>
                <dd>
                    <label><input type="text" id="{$dvs}_lotterysilk_info" name="{$dvs}_lotterysilk_info" class="page" value=""> 매</label>
                </dd>
                <input type="hidden" id="{$dvs}_lotterysilk_price" name="{$dvs}_lotterysilk_price" value="" />
            </dl>
        </div>
html;

    return $html;
}
?>
