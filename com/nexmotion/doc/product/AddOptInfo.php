<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");

/**
 * @brief 추가 옵션 정보 html 반환
 *
 * @param $idx      = 구분용 인덱스
 * @param $opt_name = 옵션명
 * @param $info_arr = 추가옵션 정보
 *
 * @return div html OR input hidden html
 */
function getAddOptInfoHtml($idx, $opt_name, $info_arr) {
    $util = new FrontCommonUtil();

    $flag = false;
    $attr = '';

    $option = null;
    $note = '';

    if ($opt_name === "빠른생산요청") {
        $option = option('', "빠른생산요청", "selected=\"selected\"");
    } else {
        $info_arr_count = count($info_arr);
        for ($i = 0; $i < $info_arr_count; $i++) {
            $info = $info_arr[$i];

            $mpcode = $info["mpcode"];
            $dvs = $util->getOptAfterFullName($info);

            if ($dvs === '') {
                $flag = true;
                break;
            }

            $option .= option($mpcode, $dvs);
            $flag = false;
        }

        if ($opt_name === "시안요청") {
            $note = <<<note
                    <dd class="br note" style="float:left; width:83%;">
                        시안확인을 하지 않으시면 인쇄가 진행되지 않습니다.
                    </dd>
note;
        } else if ($opt_name === "베다인쇄") {
            $note = <<<note
                    <dd class="br note" style="float:left; width:83%;">
                        제품 납기가 연장될 수 있습니다.
                    </dd>
note;
        } else if ($opt_name === "색견본참고") {
            $note = <<<note
                    <dd class="br note" style="float:left; width:83%;">
                        색견본 도착 후 인쇄가 진행됩니다.
                    </dd>
note;
        }
    }

    $html_sel = <<<html_sel
        <div id="opt_{$idx}_div" class="option">
        <dl>
            <dt>$opt_name</dt>
            <dd class="price" id="opt_{$idx}_price"></dd>
            <dd>
                <select id="opt_{$idx}_sel" onchange="loadOptPrice.calc('{$idx}', this.value, '{$dvs}');" {$attr}>
                    $option
                </select>
            </dd>
            {$note}
        </dl>
        </div>
html_sel;

    // 하위 depth 가 없는 경우
    $html_hidden = <<<html_hidden
        <div id="opt_{$idx}_div" class="option">
        <dl>
            <dt>$opt_name</dt>
            <dd class="price" id="opt_{$idx}_price"></dd>
            {$note}
            <input type="hidden" id="opt_{$idx}_sel" value="$mpcode" style="display:none;" />
        </dl>
        </div>
html_hidden;

    if ($flag === true) {
        return $html_hidden;
    }

    return $html_sel;
}
?>

