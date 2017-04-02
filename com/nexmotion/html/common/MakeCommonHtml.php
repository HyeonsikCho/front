<?
/**
 * @brief option html 공통사용함수
 *
 * @param $val  = option 실제 값
 * @param $dvs  = option 화면 출력값
 * @param $attr = option에 추가로 입력할 attribute
 *
 * @return option html
 */
function option($val, $dvs, $attr = '') {
    $option_form = "<option %s value=\"%s\">%s</option>";

    $ret = sprintf($option_form, $attr, $val, $dvs);

    return $ret;
}

/**
 * @brief 옵션 html 생성
 *
 * @param $rs = 검색결과
 * @param $arr["flag"]    = "기본 값 존재여부"
 * @param $arr["def"]     = "기본 값(ex:전체)"
 * @param $arr["def_val"] = "기본 값의 option value"
 * @param $arr["val"]      = "option value에 들어갈 필드 값"
 * @param $arr["dvs"]      = "option에 표시할 필드 값"
 * @param $arr["dvs_tail"] = "option 값 뒤에 붙일 단어"
 * @param $arr["dvs_tail"] = "option 값 뒤에 붙일 단어"
 * @param $arr["sel"]        = "selected할 val값" -> value랑 비교
 * @param $arr["sel_dvs"]    = "selected할 val값" -> dvs랑 비교
 * @param $arr["except_arr"] = 예외처리사항, 해당 사항 외적으로 처리할 때 사용
 *
 * @return option html
 */
function makeOptionHtml($rs, $arr) {
    $html = "";

    if ($arr["flag"] === true) {
        $html = option($arr["def_val"], $arr["def"], "selected=\"selected\"");
    }

    $except_arr = $arr["except_arr"];

    $dvs_tail = $arr["dvs_tail"];
    $sel_val  = $arr["sel"];
    $sel_dvs  = $arr["sel_dvs"];
    $val = $arr["val"];
    $dvs = $arr["dvs"];

    while ($rs && !$rs->EOF) {
        $opt_dvs = $rs->fields[$dvs]; 
        $opt_val = null;

        //필드 값 뒤에 붙일 단어
        if ($dvs_tail !== null) {
            $opt_dvs = $opt_dvs . $dvs_tail;
        }

        //만약 $val 빈값이 아니면
        if ($val !== null) {
			$opt_val = $rs->fields[$val];

            if (empty($opt_val) === true) {
                $opt_val = $opt_dvs;
            }
        } else {
            $opt_val = $opt_dvs;
        }

        $selected = "";
        if ($sel_val === true ||
                $opt_val === $sel_val ||
                $opt_dvs === $sel_dvs) {
            $selected = "selected=\"selected\"";
            $sel_val = false;
            $sel_dvs = false;
        }

        // 예외사항 1 -> 후공정 처리예외
        if (!empty($except_arr["after_name"])) {
            if ($except_arr["after_name"] === "접지" &&
                    $opt_dvs === "비중앙") {
                $selected .= " class=\"_custom\"";
            }
        }

        $html .= option($opt_val, $opt_dvs, $selected);

        $rs->MoveNext();
    }

    return $html;
}

function noLoginPop() {
    $html = <<<HTML
<header>
    <h2>선입금 결제하기</h2>
    <button class="close" title="닫기"><img src="/design_template/images/common/btn_circle_x_white.png" alt="X"></button>
</header>
<article>
    <h3>로그인 상태가 아닙니다.</h3>
    <div class="function center">
        <strong><button type="button" onclick="location.replace('/member/login.html');">로그인</button></strong>
    </div>
</article>
HTML;

    return $html;
}
?>
