<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/define/product_info_class.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/doc/product/AddOptInfo.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/doc/product/AddAfterInfo.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/doc/product/PriceInfo.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/util/FrontCommonUtil.php');

/**
 * @brief 카테고리 종이분류 option html 생성
 *
 * @param $rs      = 검색결과
 * @param $default = default selected 값
 * @param $price_info_arr = 가격검색용 정보저장 배열
 *
 * @return option html
 */
function makeCatePaperSortOption($rs, $default, &$price_info_arr) {
    $ret = "";
    $ret_sort = "";

    $util = new FrontCommonUtil();

    while($rs && !$rs->EOF) {
        $fields = $rs->fields;
        $sort   = $fields["sort"];

        $selected = "";
        if ($default === true || $sort === $default) {
            $selected = "selected=\"selected\"";

            $price_info_arr["paper_sort"] = $sort;

            $default = false;
        }

        $ret .= option($sort, $sort, $selected);

        $rs->MoveNext();
    }

    return $ret;
}

/**
 * @brief 카테고리 종이명 option html 생성
 *
 * @param $rs      = 검색결과
 * @param $default = default selected 값
 * @param $price_info_arr = 가격검색용 정보저장 배열
 *
 * @return option html
 */
function makeCatePaperNameOption($rs, $default, &$price_info_arr) {
    $ret = "";
    $ret_sort = "";

    $util = new FrontCommonUtil();

    while($rs && !$rs->EOF) {
        $fields = $rs->fields;
        $name   = $fields["name"];

        $selected = "";
        if ($default === true || $name === $default) {
            $selected = "selected=\"selected\"";

            $price_info_arr["paper_name"] = $name;

            $default = false;
        }

        $ret .= option($name, $name, $selected);

        $rs->MoveNext();
    }

    return $ret;
}

/**
 * @brief 카테고리 종이정보 option html 생성
 *
 * @param $rs      = 검색결과
 * @param $default = default selected 값
 * @param $price_info_arr = 가격검색용 정보저장 배열
 *
 * @return option html
 */
function makeCatePaperOption($rs, $default, &$price_info_arr) {
    $ret = "";
    $ret_sort = "";

    $util = new FrontCommonUtil();

    $sort_dup_chk = array();

    while($rs && !$rs->EOF) {
        $fields = $rs->fields;
        $mpcode = $fields["mpcode"];
        $sort   = $fields["sort"];

        $info = $util->makePaperInfoStr($fields);

        $selected = "";
        if ($default === true || $info === $default) {
            $selected = "selected=\"selected\"";

            if (isset($price_info_arr["paper_name"])) {
                $price_info_arr["paper_name"]  = $fields["name"];
                $price_info_arr["paper_dvs"]   = $fields["dvs"];
                $price_info_arr["paper_color"] = $fields["color"];
            }

            $price_info_arr["paper_mpcode"] = $mpcode;

            $sort_dup_chk[$sort] = option($sort, $sort, $selected);

            $default = false;
        }

        if ($sort_dup_chk[$sort] === null) {
            $sort_dup_chk[$sort] = option($sort, $sort, $selected);
        }

        $ret .= option($mpcode, $info, $selected);

        $rs->MoveNext();
    }

    foreach ($sort_dup_chk as $val) {
        $ret_sort .= $val;
    }

    return array("info" => $ret,
                 "sort" => $ret_sort);
}

/**
 * @brief 카테고리 인쇄도수 option html 생성
 *
 * @param $rs             = 검색결과
 * @param $default_print  = default selected 값
 * @param $default_purp   = $price_info_arr에 저장할 맵핑코드 검색용
 * @param $price_info_arr = 가격검색용 정보저장 배열
 * @param $tmpt_dvs       = 도수구분(단/양면 => true, 전/후면 => false)
 *
 * @return 면 구분별 html 배열
 */
function makeCatePrintOption($rs,
                             $default_print,
                             $default_purp,
                             &$price_info_arr) {
    $ret = array();
    $dup_chk = array();

    // ProductDefaultSel 클래스에 기본 설정값 없는경우 바이패스값 설정
    if (empty($default_print)) {
        $default_print = true;
        $default_val = true;
    }
    if (empty($default_purp)) {
        $default_purp = true;
    }

    $purp_arr = array();
    $purp_html = '';

    while($rs && !$rs->EOF) {
        $mpcode   = $rs->fields["mpcode"];
        $name     = $rs->fields["name"];
        $side_dvs = $rs->fields["side_dvs"];
        $purp_dvs = $rs->fields["purp_dvs"];

        // ProductDefaultSel 클래스에 기본 설정값 있는경우
        if ($default_print !== true &&
                $side_dvs === "단면" || $side_dvs === "양면") {
            $default_val = $default_print;
        } else if ($default_print !== true &&
                $side_dvs === "전면") {
            $default_val = $default_print["bef_print"];
        } else if ($default_print !== true &&
                $side_dvs === "전면추가") {
            $default_val = $default_print["bef_add_print"];
        } else if ($default_print !== true &&
                $side_dvs === "후면") {
            $default_val = $default_print["aft_print"];
        } else if ($default_print !== true &&
                $side_dvs === "후면추가") {
            $default_val = $default_print["aft_add_print"];
        }

        $selected = '';
        if ($side_dvs === "단면" || $side_dvs === "양면") {
            // 기본 설정값 없거나
            if ((is_bool($default_val) && $default_val) ||
                    (is_bool($default_purp) && $default_purp)) {
                $selected = "selected=\"selected\"";

                $price_info_arr["print_mpcode"] = $mpcode;
                $price_info_arr["print_name"]   = $name;

                // 기본값 검색 후 조건문 다시 못들어오게 변수값 수정
                $default_print = false;
                $default_purp = false;
                $default_val = false;
            }
            // 기본 설정값이랑 검색값이랑 같은경우
            if ($name === $default_val && $purp_dvs === $default_purp) {
                $selected = "selected=\"selected\"";

                $price_info_arr["print_mpcode"] = $mpcode;
                $price_info_arr["print_name"]   = $name;

                // 기본값 검색 후 조건문 다시 못들어오게 변수값 수정
                $default_print = false;
                $default_purp = false;
                $default_val = false;
            }
            // 주문페이지에서 인쇄방식 변경한경우
            if ($name === $default_val && is_int($default_purp)) {
                $selected = "selected=\"selected\"";

                $price_info_arr["print_mpcode"] = $mpcode;
                $price_info_arr["print_name"]   = $name;

                // 기본값 검색 후 조건문 다시 못들어오게 변수값 수정
                $default_print = false;
                $default_purp = false;
                $default_val = false;
            }
        } else {
            if ((is_bool($default_val) && $default_val) ||
                    (is_bool($default_purp) && $default_purp)) {
                $temp = $price_info_arr["print_mpcode"][$side_dvs]["mpcode"];
                if (empty($temp)) {
                    $selected = "selected=\"selected\"";

                    $price_info_arr["print_mpcode"][$side_dvs] = array(
                        "mpcode" => $mpcode,
                        "name"   => $name
                    );
                }
            }
            if ($name === $default_val && $purp_dvs === $default_purp) {
                $temp = $price_info_arr["print_mpcode"][$side_dvs]["mpcode"];
                if (empty($temp)) {
                    $selected = "selected=\"selected\"";

                    $price_info_arr["print_mpcode"][$side_dvs] = array(
                        "mpcode" => $mpcode,
                        "name"   => $name
                    );
                }
            }
            if ($name === $default_val && is_int($default_purp)) {
                $temp = $price_info_arr["print_mpcode"][$side_dvs]["mpcode"];
                if (empty($temp)) {
                    $selected = "selected=\"selected\"";

                    $price_info_arr["print_mpcode"][$side_dvs] = array(
                        "mpcode" => $mpcode,
                        "name"   => $name
                    );
                }
            }
        }

        if (empty($purp_arr[$purp_dvs])) {
            $purp_arr[$purp_dvs] = true;

            $purp_sel = '';
            if ($purp_dvs === $default_purp) {
                $purp_sel = "selected=\"selected\"";
            }

            $purp_html .= option($purp_dvs, $purp_dvs, $purp_sel);
        }

        // 기본선택된 인쇄방식 있을경우 그것만 html 생성
        if (!is_int($default_purp) && 
                !is_bool($default_purp) &&
                $purp_dvs !== $default_purp) {
            $rs->MoveNext();
            continue;
        }

        // 중복 option 제거
        $key = $name . '!' . $side_dvs;
        if ($dup_chk[$key] === null) {
            $dup_chk[$key] = true;
        } else {
            $rs->MoveNext();
            continue;
        }

        if ($ret[$side_dvs] === null) {
            $ret[$side_dvs] = option($mpcode, $name, $selected);
        } else {
            $ret[$side_dvs] .= option($mpcode, $name, $selected);
        }

        $rs->MoveNext();
    }

    $ret["purp_dvs"] = $purp_html;

    return $ret;
}

/**
 * @brief 카테고리 사이즈 option html 생성
 *
 * @param $rs             = 검색결과
 * @param $default        = default selected 값
 * @param $pos_num_arr    = 사이즈별 자리수 배열
 * @param $price_info_arr = 가격검색용 정보저장 배열
 * @param $affil_yn       = 계열 노출여부
 * @param $size_typ_yn    = 사이즈 타입명 노출여부
 *
 * @return option html
 */
function makeCateSizeOption($rs,
                            $default,
                            $pos_num_arr,
                            &$price_info_arr,
                            $affil_yn,
                            $size_typ_yn) {
    $ret = "";

    $attr_form  = "class=\"";
    $attr_form .= "_workingWH%s-%s ";
    $attr_form .= "_cuttingWH%s-%s ";
    $attr_form .= "_thomsonWH%s-%s ";
    $attr_form .= "_designWH%s-%s";
    $attr_form .= "\" ";
    $attr_form .= "pos_num=\"%s\" ";
    $attr_form .= "affil=\"%s\"";

    $pos_num = 0;

    while($rs && !$rs->EOF) {
        $mpcode = $rs->fields["mpcode"];
        $name   = $rs->fields["name"];
        $typ    = $rs->fields["typ"];
        $affil  = $rs->fields["affil"];

        if (!$affil_yn) {
            $affil = '';
        }

        $work_wid_size  = $rs->fields["work_wid_size"];
        $work_vert_size = $rs->fields["work_vert_size"];
        $cut_wid_size  = $rs->fields["cut_wid_size"];
        $cut_vert_size = $rs->fields["cut_vert_size"];
        $tomson_wid_size  = $rs->fields["tomson_wid_size"];
        $tomson_vert_size = $rs->fields["tomson_vert_size"];
        $design_wid_size  = $rs->fields["design_wid_size"];
        $design_vert_size = $rs->fields["design_vert_size"];

        if (is_array($pos_num_arr) === true) {
            $pos_num = $pos_num_arr[$name];
        }

        if ($size_typ_yn) {
            $name .= sprintf("(%s)", $typ);
        }

        $attr = sprintf($attr_form, $work_wid_size
                                  , $work_vert_size
                                  , $cut_wid_size
                                  , $cut_vert_size
                                  , $tomson_wid_size
                                  , $tomson_vert_size
                                  , $design_wid_size
                                  , $design_vert_size
                                  , $pos_num
                                  , $affil);

        $selected = "";
        if ($default === true || $name === $default) {
            $selected = " selected=\"selected\"";

            $price_info_arr["size_name"]    = $name;
            $price_info_arr["affil"]        = $affil;
            $price_info_arr["pos_num"]      = $pos_num;
            $price_info_arr["stan_mpcode"]  = $mpcode;
            $price_info_arr["def_cut_wid"]  = $cut_wid_size;
            $price_info_arr["def_cut_vert"] = $cut_vert_size;
            $price_info_arr["size_gap"]     = intval($work_wid_size) -
                                              intval($cut_wid_size);

            $default = false;
        }

        $attr .= $selected;

        $ret .= option($mpcode, $name, $attr);

        $rs->MoveNext();
    }

    return $ret;
}

/**
 * @brief 카테고리 수량 option html 생성
 *
 * @param $rs       = 검색결과
 * @param $amt_unit = 수량단위
 * @param $default  = default selected 값
 * @param $price_info_arr = 가격검색용 정보저장 배열
 *
 * @return option html
 */
function makeCateAmtOption($rs, $amt_unit, $default, &$price_info_arr) {
    $ret = '';

    while($rs && !$rs->EOF) {
        $amt = $rs->fields["amt"];
        $amt = doubleval($amt);

        $selected = "";
        if ($default === 0.0 || $amt === $default) {
            $selected = " selected=\"selected\"";

            $price_info_arr["amt"] = $amt;

            $default = -1;
        }

        $ciphers = 0;
        if ($amt < 1) {
            $ciphers = 1;
        }

        $amt_format = number_format($amt, $ciphers);
        $amt_format .= ' ' . $amt_unit;

        $ret .= option($amt, $amt_format, $selected);

        $rs->MoveNext();
    }

    return $ret;
}

/**
 * @brief 카테고리 수량 option html 생성
 *
 * @param $rs  = 검색결과
 *
 * @return option html
 */
function makeCatePackWayOption($rs) {
    $ret = '';

    while($rs && !$rs->EOF) {
        $name = $rs->fields["pack_name"];

        $ret .= option($name, $name);

        $rs->MoveNext();
    }

    return $ret;
}

/**
 * @brief 카테고리 옵션 ul html 생성
 *
 * @param $rs = 검색결과
 * @param &$info_arr = 추가 옵션 팝업 생성용 정보배열
 *
 * @return ul html
 */
function makeCateOptUl($rs, &$info_arr) {
    if ($rs->EOF) {
        return "<ul>없음</ul>";
    }

    $dvs = $info_arr["dvs"];

    $ret = "<ul>";

    $li_form = "<li><label><input type=\"checkbox\" name=\"chk_opt\" id=\"opt_%s\" value=\"%s\" price=\"\" onclick=\"loadOptPrice.exec(this, '%s', '%s');\" %s /> %s</label></li>";

    // 추가 옵션
    $add = "";

    $i = 0;
    $dup_chk = array();
    while($rs && !$rs->EOF) {
        $mpcode   = $rs->fields["mpcode"];
        $name     = $rs->fields["name"];
        $basic_yn = $rs->fields["basic_yn"];

        $key = $name . '!' . $basic_yn;

        if ($dup_chk[$key] === null) {
            $dup_chk[$key] = true;
        } else {
            $rs->MoveNext();
            continue;
        }

        if ($basic_yn === "Y") {
            $rs->MoveNext();
            continue;
        }

        $checked = '';
        // 당일판 옵션 시간에 따라 처리
        if ($name === "당일판") {
            $time = intval(date("Gis"));

            // 15:00 ~ 20:00 까지 비노출
            if (150000 < $time && $time <= 200000) {
                $rs->MoveNext();
                continue;
            }
            /*
            */

            $checked = "checked=\"checked\"";
        }

        $add .= sprintf($li_form, $i
                                , $name
                                , $i
                                , $dvs
                                , $checked
                                , $name);

        $info_arr["idx"][$name] = $i;
        $info_arr["name"][$i++] = $name;

        $rs->MoveNext();
    }

    $ret .= $add;
    $ret .= "</ul>";

    return $ret;
}

/**
 * @brief 카테고리 후공정 ul html 생성
 *
 * @param $rs  = 검색결과
 * @param $dvs = 제품 구분값
 * @param &$info_arr  = 추가 후공정 팝업 생성용 정보배열
 * @param $except_arr = html을 생성하지 않을 후공정명 배열
 *
 * @return ul html
 */
function makeCateAftUl($rs, $dvs, &$info_arr, $except_arr = array()) {
    if ($rs->EOF) {
        return "<ul>없음</ul>";
    }

    $li_class_arr = ProductInfoClass::AFTER_ARR;

    $ret = "<ul>";

    $li_form = "<li %s><label><input type=\"checkbox\" name=\"%s_chk_after[]\" id=\"%s_%s\" onclick=\"loadAfterPrice.exec(this.checked, this.value, '%s');\" value=\"%s\" aft=\"%s\" class=\"aft_chkbox\" /> %s</label></li>";

    // 추가 후공정
    $add = "";

    $i = 0;
    $j = 0;
    $dup_chk = array();
    while($rs && !$rs->EOF) {
        $name     = $rs->fields["name"];
        $basic_yn = $rs->fields["basic_yn"];

        if ($except_arr[$name] !== null) {
            $rs->MoveNext();
            continue;
        }

        $key = $name . '!' . $basic_yn;

        if ($dup_chk[$key] === null) {
            $dup_chk[$key] = true;
        } else {
            $rs->MoveNext();
            continue;
        }

        $class = $li_class_arr[$name];
        $li_class = ($class === null) ? '' : "class=\"_" . $class . "\"";

        if ($basic_yn === "Y") {
            $info_arr["basic"][$j++] = $name;
            $rs->MoveNext();
            continue;
        }

        $add .= sprintf($li_form, $li_class
                                , $dvs
                                , $dvs
                                , $class
                                , $dvs
                                , $name
                                , $class
                                , $name);

        $info_arr["add"][$i++] = $name;

        $rs->MoveNext();
    }

    $ret .= $add;
    $ret .= "</ul>";

    return $ret;
}

/**
 * @brief 카테고리 옵션 ul html 생성
 *
 * @param $rs    = 검색결과
 * @param $parma = 검색결과
 *
 * @return ul html
 */
function makeCateAddOpt($rs, $idx_arr) {
    $ret = "";

    $info_arr = array();

    $i = 0;
    while($rs && !$rs->EOF) {
        $mpcode = $rs->fields["mpcode"];

        $name   = $rs->fields["opt_name"];
        $depth1 = $rs->fields["depth1"];
        $depth2 = $rs->fields["depth2"];
        $depth3 = $rs->fields["depth3"];

        if ($info_arr[$name] === null) {
            $i = 0;
        }

        // 당일판 옵션 시간에 따라 처리
        if ($name === "당일판") {
            /*
            $rs->MoveNext();
            continue;
            */
            $time = intval(date("Gis"));

            if ($time < 120000) {
                if ($depth1 === "오후3시 마감") {
                    $rs->MoveNext();
                    continue;
                }
            } else if (120000 <= $time && $time < 150000) {
                if ($depth1 === "오전12시 마감") {
                    $rs->MoveNext();
                    continue;
                }
            }
            /*
            */
        }

        $info_arr[$name][$i]["mpcode"]   = $mpcode;
        $info_arr[$name][$i]["depth1"]   = $depth1;
        $info_arr[$name][$i]["depth2"]   = $depth2;
        $info_arr[$name][$i++]["depth3"] = $depth3;

        $rs->MoveNext();
    }

    unset($rs);

    $ret = "";

    $info_arr_count = count($info_arr);

    $i = 0;
    foreach ($info_arr as $opt_name => $info) {
        $ret .= getAddOptInfoHtml($idx_arr[$opt_name],
                                  $opt_name,
                                  $info);
    }

    return $ret;
}

/**
 * @brief 카테고리 후공정 ul html 생성
 *
 * @param $rs         = 검색결과
 * @param $dvs        = 제품 구분값
 * @param $except_arr = html 생성 제외 후공정
 *
 * @return ul html
 */
function makeCateAddAfter($rs, $dvs, $except_arr = array()) {
    $ret = "";

    $info_arr = array();
    $key_form = "%s!%s!%s!%s";

    $i = 0;
    while($rs && !$rs->EOF) {
        $mpcode = $rs->fields["mpcode"];

        $name   = $rs->fields["after_name"];
        $depth1 = $rs->fields["depth1"];
        $depth2 = $rs->fields["depth2"];
        $depth3 = $rs->fields["depth3"];

        $key = sprintf($key_form, $name, $depth1, $depth2, $depth3);

        if ($except_arr[$name] !== null) {
            $rs->MoveNext();
            continue;
        }

        if ($info_arr[$name] === null) {
            $i = 0;
        }

        $info_arr[$name][$i]["mpcode"]   = $mpcode;
        $info_arr[$name][$i]["depth1"]   = $depth1;
        $info_arr[$name][$i]["depth2"]   = $depth2;
        $info_arr[$name][$i++]["depth3"] = $depth3;

        $rs->MoveNext();
    }

    unset($rs);

    $ret = "";

    foreach ($info_arr as $after_name => $info) {
        switch ($after_name) {
            case "코팅" :
                $ret .= getCoatingInfoHtml($info, $dvs);
                break;
            case "귀도리" :
                $ret .= getRoundingInfoHtml($info, $dvs);
                break;
            case "박" :
                $ret .= getFoilInfoHtml($info, $dvs);
                break;
            case "형압" :
                $ret .= getPressInfoHtml($info, $dvs);
                break;
            case "엠보싱" :
                $ret .= getEmbossingInfoHtml($info, $dvs);
                break;
            case "오시" :
                $ret .= getImpressionInfoHtml($info, $dvs);
                break;
            case "미싱" :
                $ret .= getDotlineInfoHtml($info, $dvs);
                break;
            case "타공" :
                $ret .= getPunchingInfoHtml($info, $dvs);
                break;
            case "접지" :
                $ret .= getFoldlineInfoHtml($info, $dvs);
                break;
            case "도무송" :
                $ret .= getTomsonInfoHtml($info, $dvs);
                break;
            case "넘버링" :
                $ret .= getNumberingInfoHtml($info, $dvs);
                break;
            case "재단" :
                $ret .= getCuttingInfoHtml($info, $dvs);
                break;
            case "제본" :
                $ret .= getBindingInfoHtml($info, $dvs);
                break;
            case "접착" :
                $ret .= getBondingInfoHtml($info, $dvs);
                break;
            case "가공" :
                $ret .= getManufactureInfoHtml($info, $dvs);
                break;
            case "복권 실크" :
                $ret .= getLottrySilkInfoHtml($info, $dvs);
                break;
            case "라미넥스" :
                $ret .= getLaminexInfoHtml($info, $dvs);
                break;
        }
    }

    return $ret;
}

/**
 * @brief 인쇄유형 option html 반환
 *
 * @param $mono_dvs = 카테고리 계산방식
 *
 * @return option html
 */
function makeMonoDvsOption($mono_dvs) {
    $ret = "";

    if ($mono_dvs === '1') {
        $ret .= option('0', "일반형(합판)", "selected=\"selected\"");
        $ret .= option('1', "계산형(독판)");
    } else if ($mono_dvs === '2') {
        $ret .= option('0', "일반형(합판)");
    } else if ($mono_dvs === '3') {
        $ret .= option('1', "계산형(독판)");
    }

    return $ret;
}

/**
 * @brief 회원등급 할인 dl html 반환
 *
 * @param $param = 할인율, 회원등급 파라미터
 *
 * @return option html
 */
function makeGradeSaleDl($param) {
    return getGradeSaleDl($param);
}

/**
 * @brief 회원 수량별 할인 dl html 반환
 *
 * @param $param = 할인요율, 할인금액
 *
 * @return option html
 */
function makeAmtMemberSale($param) {
    return getAmtMemberSale($param);
}

/**
 * @brief 이벤트 할인 dl html 반환
 *
 * @param $param = 이벤트명, 할인 요율/가격 정보 파라미터
 *
 * @return option html
 */
function makeEventSaleDl($param) {
    return getEventSaleDl($param);
}

/**
 * @brief 책자형 제본 셀렉트박스 option html 생성
 *
 * @param $rs    = 제본 검색결과
 * @param $param = 기본값 등 파라미터
 * @param $price_info_arr = 가격검색용 정보저장 배열
 *
 * @return option html
 */
function makeBindingOptionHtml($rs, $param, &$price_info_arr) {
    $dvs     = $param["dvs"];
    $default = $param["default"];
    $html = '';

    while ($rs && !$rs->EOF) {
        $fields = $rs->fields;

        $aft    = $fields[$dvs];
        $mpcode = $fields["mpcode"];

        if (empty($mpcode)) {
            $mpcode = $aft;
        }

        $selected = '';
        if ($default === true || $aft === $default) {
            $price_info_arr["binding_" . $dvs] = $aft;
            $price_info_arr["binding_mpcode"]  = $mpcode;

            $selected = "selected=\"selected\"";

            $default = false;
        }

        $html .= option($mpcode, $aft, $selected);

        $rs->MoveNext();
    }

    return $html;
}
?>
