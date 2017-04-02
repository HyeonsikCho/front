// 후공정명 한글-영문간 매칭배열
var aftArr = {
    "코팅"     : "coating",
    "귀도리"   : "rounding",
    "박"       : "foil",
    "형압"     : "press",
    "엠보싱"   : "embossing",
    "오시"     : "impression",
    "미싱"     : "dotline",
    "타공"     : "punching",
    "접지"     : "foldline",
    "도무송"   : "thomson",
    "넘버링"   : "numbering",
    "재단"     : "cutting",
    "제본"     : "binding",
    "접착"     : "bonding",
    "가공"     : "manufacture",
    "복권실크" : "lotterysilk",
    "라미넥스" : "laminex"
};

/**
 * @brief 후공정 가격 세팅
 *
 * @param dvs = 제품 구분값
 * @param aft = 후공정 구분값
 * @param price = 후공정 가격
 */
var setAfterPrice = function(dvs, aft, price) {
    var prefix = getPrefix(dvs);
    var sortcode = $(prefix + "cate_sortcode").val().substr(0, 3);
    // 재단이면 수량*단가, 명함은 제외
    if (sortcode === "003" && aft === "cutting") {
        var amt = parseFloat($(prefix + "amt").val());
        price *= amt;
    } else if (aft === "binding" && price === false) {
        calcBindingPrice.exec(dvs, price);
	return false;
    }

    var count = $("#count").val();
    if (checkBlank(count)) {
        count = 1;
    } else {
        count = parseInt(count);
    }
    price = parseInt(price) * count;

    aftPrefix = getPrefix(dvs) + aft + '_';
    $(aftPrefix + "price").val(price);
    $(aftPrefix + "price_dd").html(price.format() + "원");

    if ($(prefix + aft).prop("checked")) {
        $("#esti_" + aft + "_dt").show();
        $("#esti_" + aft + "_dd").show();
        $("#esti_" + aft).html(ceilVal(price / 1.1).format());
    }
};

/**
 * @brief 후공정 가격검색을 위해 후공정명을
 * 공통으로 사용하는 영어명으로 변경하고 실검색 함수로 전달
 *
 * @param obj = 체크확인용
 * @param val = 후공정명
 */
var loadAfterPrice = {
    "aft"     : null,
    "exec"    : function(checked, val, dvs) {
        var aft = afterKo2En(val);
        this.aft = aft;

        if (aft === "foldline") {
            if (!checked) {
                closeMovPreview();
            }
            swapSize.exec(dvs, checked);
        }
        if (!checked) {
            calcPrice();
            quickEstiAftHide(aft);
            return false;
        }

        preview.dvs = dvs;

        getAfterPrice.common(this.aft, dvs);
    }
};

/**
 * @brief 후공정명 한글 -> 영어 변환
 *
 * @param val = 후공정명 한글
 *
 * @return 후공정명 영어
 */
var afterKo2En = function(val) {
    return aftArr[val];
};

/**
 * @brief 불러온 후공정 가격에서 해당하는 가격 검색
 * 실제로 수행하는 함수는 각 페이지별 자바스크립트 파일에
 * 존재하는 공통된 이름을 가진 함수를 호출한다
 *
 * 각 상품별로 처리 로직이 다를 수 있기 때문에 별도로 처리한다
 *
 * @param aft = 후공정 구분값
 * @param mpcode = 맵핑코드
 */
var getAfterPrice = {
    "flattypYn" : null,
    "price"     : {
        "coating"     : null,
        "rounding"    : null,
        "foil"        : null,
        "press"       : null,
        "embossing"   : null,
        "impression"  : null,
        "dotline"     : null,
        "punching"    : null,
        "foldline"    : null,
        "thomson"     : null,
        "numbering"   : null,
        "cutting"     : null,
        "binding"     : null,
        "bonding"     : null,
        "manufacture" : null,
        "lotterysilk" : null,
        "laminex"     : null
    },
    "load" : function(aft, data, dvs) {
        // 가격이 없을경우 검색하는 함수
        var url = "/ajax/product/load_after_price.php";
        var callback = function(result) {
            getAfterPrice.price[aft] = result;

            getAfterPrice.common(aft, dvs);
        };

        ajaxCall(url, "json", data, callback);
    },
    "common" : function(aft, dvs) {
        var prefix = getPrefix(dvs);
        var monoYn = $(prefix + "mono_yn").val();

        ($(prefix + "flattyp_yn").val() === 'Y') ?
                getAfterPrice.flattypYn = true :
                getAfterPrice.flattypYn = false;

        // 오시같이 바로 체크해줘야 되는 부분 때문에 추가
        if (!chkAftRestrict(dvs, aft)) {
            return false;
        }

        if (!checkBlank(getAfterPrice[aft].common)) {
            getAfterPrice[aft].common(aft, dvs);
        }

        getAfterPrice[aft][monoYn](aft, dvs);
    },
    "coating"    : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getCoatingPlySheetPrice(aft, dvs);
            } else {
                getCoatingPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getCoatingCalcSheetPrice(aft, dvs);
            } else {
                getCoatingCalcBookletPrice(aft, dvs);
            }
        }
    },
    "rounding"   : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getRoundingPlySheetPrice(aft, dvs);
            } else {
                getRoundingPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getRoundingCalcSheetPrice(aft, dvs);
            } else {
                getRoundingCalcBookletPrice(aft, dvs);
            }
        }
    },
    "foil"       : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getFoilPlySheetPrice(aft, dvs);
            } else {
                getFoilPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getFoilCalcSheetPrice(aft, dvs);
            } else {
                getFoilCalcBookletPrice(aft, dvs);
            }
        }
    },
    "press"      : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getPressPlySheetPrice(aft, dvs);
            } else {
                getPressPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getPressCalcSheetPrice(aft, dvs);
            } else {
                getPressCalcBookletPrice(aft, dvs);
            }
        }
    },
    "embossing"  : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getEmbossingPlySheetPrice(aft, dvs);
            } else {
                getEmbossingPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getEmbossingCalcSheetPrice(aft, dvs);
            } else {
                getEmbossingCalcBookletPrice(aft, dvs);
            }
        }
    },
    "impression" : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getImpressionPlySheetPrice(aft, dvs);
            } else {
                getImpressionPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getImpressionCalcSheetPrice(aft, dvs);
            } else {
                getImpressionCalcBookletPrice(aft, dvs);
            }
        }
    },
    "dotline"    : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getDotlinePlySheetPrice(aft, dvs);
            } else {
                getDotlinePlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getDotlineCalcSheetPrice(aft, dvs);
            } else {
                getDotlineCalcBookletPrice(aft, dvs);
            }
        }
    },
    "punching"   : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getPunchingPlySheetPrice(aft, dvs);
            } else {
                getPunchingPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getPunchingCalcSheetPrice(aft, dvs);
            } else {
                getPunchingCalcBookletPrice(aft, dvs);
            }
        }
    },
    "foldline"   : {
        "common" : function(aft, dvs) {
            var $obj = $(getPrefix(dvs) + aft);

            if ($obj.prop("checked")) {
                setAfterMovSrc(dvs);
            }
        },
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getFoldlinePlySheetPrice(aft, dvs);
            } else {
                getFoldlinePlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getFoldlineCalcSheetPrice(aft, dvs);
            } else {
                getFoldlineCalcBookletPrice(aft, dvs);
            }
        }
    },
    "thomson"    : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getThomsonPlySheetPrice(aft, dvs);
            } else {
                getThomsonPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getThomsonCalcSheetPrice(aft, dvs);
            } else {
                getThomsonCalcBookletPrice(aft, dvs);
            }
        }
    },
    "numbering"  : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getNumberingPlySheetPrice(aft, dvs);
            } else {
                getNumberingPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getNumberingCalcSheetPrice(aft, dvs);
            } else {
                getNumberingCalcBookletPrice(aft, dvs);
            }
        }
    },
    "cutting"    : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getCuttingPlySheetPrice(aft, dvs);
            } else {
                getCuttingPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getCuttingCalcSheetPrice(aft, dvs);
            } else {
                getCuttingCalcBookletPrice(aft, dvs);
            }
        }
    },
    "binding"    : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getBindingPlySheetPrice(aft, dvs);
            } else {
                getBindingPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getBindingCalcSheetPrice(aft, dvs);
            } else {
                getBindingCalcBookletPrice(aft, dvs);
            }
        }
    },
    "bonding"    : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getBondingPlySheetPrice(aft, dvs);
            } else {
                getBondingPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getBondingCalcSheetPrice(aft, dvs);
            } else {
                getBondingCalcBookletPrice(aft, dvs);
            }
        }
    },
    "manufacture" : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getManufacturePlySheetPrice(aft, dvs);
            } else {
                getManufacturePlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getManufactureCalcSheetPrice(aft, dvs);
            } else {
                getManufactureCalcBookletPrice(aft, dvs);
            }
        }
    },
    "lotterysilk" : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getLotterysilkPlySheetPrice(aft, dvs);
            } else {
                getLotterysilkPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getLotterysilkCalcSheetPrice(aft, dvs);
            } else {
                getLotterysilkCalcBookletPrice(aft, dvs);
            }
        }
    },
    "laminex"     : {
        "0" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getLaminexPlySheetPrice(aft, dvs);
            } else {
                getLaminexPlyBookletPrice(aft, dvs);
            }
        },
        "1" : function(aft, dvs) {
            if (getAfterPrice.flattypYn === true) {
                getLaminexCalcSheetPrice(aft, dvs);
            } else {
                getLaminexCalcBookletPrice(aft, dvs);
            }
        }
    }
};

/**
 * @brief 수량 변경시 후공정 가격 재계산 함수
 *
 * @param dvs     = 제품 구분값
 * @param addFunc = 재계산 전에 추가로 실행할 함수, 없으면 null
 */
var reCalcAfterPrice = function(dvs, addFunc) {
    if (!checkBlank(addFunc)) {
        addFunc();
    }

    $("input[name='" + dvs + "_chk_after[]']:checked").each(function() {
        var $obj = $(this);

        loadAfterPrice.exec($obj.prop("checked"), $obj.val(), dvs)
    });
};

/**
 * @brief 박 구분 양면으로 변경시 후면부분 disabled
 *
 * @param dvs = 제품 구분값
 * @param val = 박 구분값
 */
var changeFoilDvs = function(dvs, val) {
    if (checkBlank(val)) {
        return false;
    }

    var prefix = getPrefix(dvs);

    if (val === "양면") {
        $(prefix + "foil_2").val('');
        $(prefix + "foil_dvs_2").val('');
        $(prefix + "foil_wid_2").val('');
        $(prefix + "foil_vert_2").val('');

        $(prefix + "foil_2").prop("disabled", true);
        $(prefix + "foil_dvs_2").prop("disabled", true);
        $(prefix + "foil_wid_2").prop("disabled", true);
        $(prefix + "foil_vert_2").prop("disabled", true);
    } else {
        $(prefix + "foil_2").prop("disabled", false);
        $(prefix + "foil_dvs_2").prop("disabled", false);
        $(prefix + "foil_wid_2").prop("disabled", false);
        $(prefix + "foil_vert_2").prop("disabled", false);
    }

    getAfterPrice.common("foil", dvs);
};

/**
 * @brief 박에서 선택안함(-)으로 변경시 인풋박스 값 초기화
 *
 * @param dvs = 제품구분값
 * @param val = 선택값
 * @param idx = 위치값
 */
var foilAreaInit = function(dvs, val, idx) {
    if (!checkBlank(val)) {
        return false;
    }

    var prefix = getPrefix(dvs);

    $(prefix + "foil_dvs_" + idx).val('');
    $(prefix + "foil_wid_" + idx).val('');
    $(prefix + "foil_vert_" + idx).val('');

    $(prefix + "foil_val_" + idx).val('');
    $(prefix + "foil_info_" + idx).val('');
    $(prefix + "foil_price_" + idx).val('');

    setAfterPrice(dvs, 'foil', '0');

    calcPrice();
};

/**
 * @brief 귀도리 체크박스 처리
 *
 * @detail 한귀도리면 체크박스 옮기고 나머지는 alert
 *
 * @param dvs = 제품구분값
 * @param obj = 체크박스 객체
 */
var chkRoundingLimit = function(dvs, obj) {
    var prefix = getPrefix(dvs);
    var cnt    = $(prefix + "rounding_cnt").val();
    var limit  = 0;

    if(cnt === "한귀도리") {
        $("input[name='" + dvs + "_rounding_dvs']:checked").prop("checked", false);
        $(obj).prop('checked', true);
        preview.rounding();

        return false;
    } else if(cnt === "두귀도리") {
        limit = 2;
    } else if(cnt === "세귀도리") {
        limit = 3;
    } else if(cnt === "네귀도리") {
        limit = 4;
    }

    var i = $("input[name='" + dvs + "_rounding_dvs']:checked").length;

    if(limit < i) {
        $(obj).prop('checked', false);
        alert(cnt +"에는 " + limit + "개의 귀도리까지만 들어 갈 수 있습니다.");
    }

    preview.rounding();
};

/******************************************************************************
 * 후공정 정보 생성 함수
 *****************************************************************************/

/**
 * @brief 후공정 정보 생성 객체
 *
 * @param dvs = 제품 구분값
 *
 * @return validation 체크 통과여부
 */
var makeAfterInfo = {
    "msg" : '',
    "all" : function(dvs) {
        var ret = null;
        var aft = null;
        var func = null;

        $("input[name='" + dvs + "_chk_after[]']").each(function() {
            if ($(this).prop("checked") === false) {
                return true;
            }

            aft = $(this).attr("aft");
            func = makeAfterInfo[aft];

            if (checkBlank(func) === true) {
                return true;
            }

            ret = func(dvs, aft);

            if (ret === false) {
                alert(makeAfterInfo.msg);
                return false;
            }

            if (ret === true) {
                ret = '';
            }

            $("#" + dvs + '_' + aft + "_info").val(ret);
        });

        return ret;
    },
    "rounding"   : function(dvs, aft) {
        var prefix = dvs + '_' + aft + '_';
        var str = "";
        var val = $("#" + prefix + "cnt").val();

        var cnt = 0;
        $("input[name='" + prefix + "dvs']:checked").each(function() {
            str += $(this).val();
            str += ", ";

            cnt++;
        });

        if (checkBlank(str) === true) {
            makeAfterInfo.msg = "귀도리 방향을 선택해주세요.";
            return false;
        }

        if (val === "한귀도리") {
            if (cnt !== 1) {
                makeAfterInfo.msg = "한 개의 귀도리 방향을 선택해주세요.";
                return false;
            }
        } else if (val === "두귀도리") {
            if (cnt !== 2) {
                makeAfterInfo.msg = "두 개의 귀도리 방향을 선택해주세요.";
                return false;
            }
        } else if (val === "세귀도리") {
            if (cnt !== 3) {
                makeAfterInfo.msg = "세 개의 귀도리 방향을 선택해주세요.";
                return false;
            }
        } else if (val === "네귀도리") {
            if (cnt !== 4) {
                makeAfterInfo.msg = "네 개의 귀도리 방향을 선택해주세요.";
                return false;
            }
        }

        str = str.substr(0, (str.length - 2));

        return str;
    },
    "impression" : function(dvs, aft) {
        var prefix = dvs + '_' + aft + '_';
        var val = $('#' + prefix + "cnt").val();
        var selector = "input[name='" + prefix + val + "_val']:checked";

        if ($(selector).length === 0) {
            makeAfterInfo.msg = "오시 위치를 선택해주세요.";
            return false;
        }

        var mpcode = $(selector).val();
        $("#" + prefix + "val").val(mpcode);

        if ($(selector).attr("dvs") === "M") {
            return '';
        }

        selector = prefix + val + "_pos";
        var str = "";
        var posVal = $("#" + selector + '1').val();

        if (checkBlank(posVal) === true) {
            makeAfterInfo.msg = "오시 선 위치를 입력해주세요.";
            return false;
        }
        str += "첫 번째 선 : ";
        str += posVal;
        str += "mm";

        var $obj = $("#" + selector + '2');
        if ($obj.length > 0) {
            posVal = $obj.val();
            if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "오시 선 위치를 입력해주세요.";
                return false;
            }

            str += " / 두 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        $obj = $("#" + selector + '3');
        if ($("#" + selector + '3').length > 0) {
            posVal = $obj.val();
            if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "오시 선 위치를 입력해주세요.";
                return false;
            }

            str += " / 세 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        $obj = $("#" + selector + '4');
        if ($("#" + selector + '4').length > 0) {
            posVal = $obj.val();
	    if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "오시 선 위치를 입력해주세요.";
                return false;
	    }

            str += " / 네 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        return str;
    },
    "dotline"    : function(dvs, aft) {
        var prefix = dvs + '_' + aft + '_';
        var val = $('#' + prefix + "cnt").val();
        var selector = "input[name='" + prefix + val + "_val']:checked";

        if ($(selector).length === 0) {
            makeAfterInfo.msg = "미싱 위치를 선택해주세요.";
            return false;
        }

        var mpcode = $(selector).val();
        $("#" + prefix + "val").val(mpcode);

        if ($(selector).attr("dvs") === "M") {
            return true;
        }

        selector = prefix + val + "_pos";
        var str = "";
        var posVal = $("#" + selector + '1').val();

        if (checkBlank(posVal) === true) {
            makeAfterInfo.msg = "미싱 선 위치를 입력해주세요.";
            return false;
        }
        str += "첫 번째 선 : ";
        str += posVal;
        str += "mm";

        var $obj = $("#" + selector + '2');
        if ($obj.length > 0) {
            posVal = $obj.val();
            if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "미싱 선 위치를 입력해주세요.";
                return false;
            }

            str += " / 두 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        $obj = $("#" + selector + '3');
        if ($("#" + selector + '3').length > 0) {
            posVal = $obj.val();
            if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "미싱 선 위치를 입력해주세요.";
                return false;
            }

            str += " / 세 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        $obj = $("#" + selector + '4');
        if ($("#" + selector + '4').length > 0) {
            posVal = $obj.val();
            if (checkBlank(posVal) === true) {
                makeAfterInfo.msg = "미싱 선 위치를 입력해주세요.";
                return false;
            }

            str += " / 네 번째 선 : ";
            str += posVal;
            str += "mm";
        }

        return str;
    },
    "punching"   : function(dvs, aft) {
        var prefix = getPrefix(dvs) + aft + '_';
        var cnt = $(prefix + "cnt").val();
        var str = "";

        for (var i = 1; i <= cnt; i++) {
           var selectorW = prefix + "pos_w" + i;
           var selectorH = prefix + "pos_h" + i;

           var valW = $(selectorW).val();
           var valH = $(selectorH).val();

           if (checkBlank(valW) === true ||
               checkBlank(valH) === true) {
               makeAfterInfo.msg = "타공 위치를 입력해주세요.";
               return false;
           }

           if (i === 1) {
               str += "첫 번째 타공 위치 가로 ";
               str += $(selectorW).val();
               str += "mm, ";
               str += "첫 번째 타공 위치 세로 ";
               str += $(selectorH).val();
               str += "mm";
           } else if (i === 2) {
               str += " / 두 번째 타공 위치 가로 ";
               str += $(selectorW).val();
               str += "mm, ";
               str += "두 번째 타공 위치 세로 ";
               str += $(selectorH).val();
               str += "mm";
           } else if (i === 3) {
               str += " / 세 번째 타공 위치 가로 ";
               str += $(selectorW).val();
               str += "mm, ";
               str += "세 번째 타공 위치 세로 ";
               str += $(selectorH).val();
               str += "mm";
           } else if (i === 4) {
               str += " / 네 번째 타공 위치 가로 ";
               str += $(selectorW).val();
               str += "mm, ";
               str += "네 번째 타공 위치 세로 ";
               str += $(selectorH).val();
               str += "mm";
           }
        }

        return str;
    },
    "foldline"    : function(dvs, aft) {
        return true;
    },
    "foil"        : function(dvs, aft) {
        var prefix = getPrefix(dvs);
        var aftPrefix = prefix + aft + '_';

        var wid_1  = parseInt($(aftPrefix + "wid_1").val());
        var vert_1 = parseInt($(aftPrefix + "vert_1").val());
        var wid_2  = parseInt($(aftPrefix + "wid_2").val());
        var vert_2 = parseInt($(aftPrefix + "vert_2").val());
    
        var aft_1 = $(aftPrefix + "1").val();
        var dvs_1 = $(aftPrefix + "dvs_1").val();
        var aft_2 = $(aftPrefix + "2").val();
        var dvs_2 = $(aftPrefix + "dvs_2").val();
    
        if (checkBlank(aft_1) && checkBlank(dvs_1) &&
                checkBlank(aft_2) && checkBlank(dvs_2)) {
            return false;
        }

        var str = ''
	
        if (dvs_1 === "양면") {
            if (isNaN(wid_1)) {
                makeAfterInfo.msg = "박 가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_1)) {
                makeAfterInfo.msg = "박 세로값을 입력해주세요.";
                return false;
            }

            str += "전면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm / ";
            str += "후면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm";
        } else if (!checkBlank(aft_1) && !checkBlank(dvs_1) &&
                checkBlank(aft_2) && checkBlank(dvs_2)) {
            // 전면만
            if (isNaN(wid_1)) {
                makeAfterInfo.msg = "박 가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_1)) {
                makeAfterInfo.msg = "박 세로값을 입력해주세요.";
                return false;
            }

            str += "전면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm";
        } else if (checkBlank(aft_1) && checkBlank(dvs_1) &&
                !checkBlank(aft_2) && !checkBlank(dvs_2)) {
            // 후면만
            if (isNaN(wid_2)) {
                makeAfterInfo.msg = "박 가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_2)) {
                makeAfterInfo.msg = "박 세로값을 입력해주세요.";
                return false;
            }

            str += "후면(" + aft_2 + ") : 가로 " + wid_2 + "mm, 세로 " + vert_2 + "mm";
        } else if (!checkBlank(aft_1) && !checkBlank(dvs_1) &&
                !checkBlank(aft_2) && !checkBlank(dvs_2)) {
            if (isNaN(wid_1)) {
                makeAfterInfo.msg = "박 전면 가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_1)) {
                makeAfterInfo.msg = "박 전면 세로값을 입력해주세요.";
                return false;
            }

            if (isNaN(wid_2)) {
                makeAfterInfo.msg = "박 후면 가로값을 입력해주세요.";
                return false;
            }

            if (isNaN(vert_2)) {
                makeAfterInfo.msg = "박 후면 세로값을 입력해주세요.";
                return false;
            }

            str += "전면(" + aft_1 + ") : 가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm / ";
            str += "후면(" + aft_2 + ") : 가로 " + wid_2 + "mm, 세로 " + vert_2 + "mm";
        } else {
            return false;
        }

        return str;
    },
    "embossing" : function(dvs, aft) {
        var prefix = getPrefix(dvs);
        var aftPrefix = prefix + aft + '_';

        var wid_1  = parseInt($(aftPrefix + "wid_1").val());
        var vert_1 = parseInt($(aftPrefix + "vert_1").val());
    
        if (isNaN(wid_1)) {
            makeAfterInfo.msg = "엠보싱 가로값을 입력해주세요.";
            return false;
        }

        if (isNaN(vert_1)) {
            makeAfterInfo.msg = "엠보싱 세로값을 입력해주세요.";
            return false;
        }

        var str = "가로 " + wid_1 + "mm, 세로 " + vert_1 + "mm";

        return str;
    },
    "press"       : function(dvs, aft) {
        var prefix = getPrefix(dvs) + aft + '_';

        var wid  = $(prefix + "wid_1").val();
        var vert = $(prefix + "vert_1").val();

        if (checkBlank(wid)) {
            makeAfterInfo.msg = "가로값을 입력해주세요.";
            return false;
        }

        if (checkBlank(vert)) {
            makeAfterInfo.msg = "세로값을 입력해주세요.";
            return false;
        }

        var str = "가로 : " + wid + ", 세로 : " + vert;

        return str;
    },
    "thomson"     : function(dvs, aft) {
        return true;
    },
    "numbering"   : function(dvs, aft) {
        return true;
    },
    "cutting"     : function(dvs, aft) {
        return true;
    },
    "binding"     : function(dvs, aft) {
        var prefix = getPrefix(dvs) + aft + '_';
        if ($("#binding_typ").length > 0) {
            return $("#binding_typ").val();
        }
        return true;
    },
    "bonding"     : function(dvs, aft) {
        return true;
    },
    "manufacture" : function(dvs, aft) {
        return true;
    },
    "lotterysilk" : function(dvs, aft) {
        return true;
    },
    "laminex"     : function(dvs, aft) {
        return $("#" + dvs + '_' + aft + "_info").val() + '매';
    }
};

/**
 * @brief 후공정 가격 초기화
 */
var initAfterPrice = function() {
    getAfterPrice.price = {
        "coating"     : null,
        "rounding"    : null,
        "impression"  : null,
        "dotline"     : null,
        "punching"    : null,
        "foldline"    : null,
        "embossing"   : null,
        "foil"        : null,
        "press"       : null,
        "thomson"     : null,
        "numbering"   : null,
        "cutting"     : null,
        "binding"     : null,
        "bonding"     : null,
        "manufacture" : null,
        "lotterysilk" : null,
        "laminex"     : null
    };
};

/**
 * @brief 사이즈 변경시 후공정 맵핑코드 재검색
 *
 * @param dvs        = 제품구분
 * @param aftInfoArr = 후공정 정보 배열
 * @param size       = 사이즈명
 */
var loadAfterMpcode = function(dvs, aftInfoArr, size) {
    var prefix = getPrefix(dvs);

    var url = "/ajax/product/load_after_mpcode.php";
    var data = {
        "cate_sortcode"  : $(prefix + "cate_sortcode").val(),
        "after_info_arr" : aftInfoArr,
        "size"           : size
    };
    var callback = function(result) {
        var len = result.length;

        for (var i = 0; i < len; i++) {
            var obj = result[i];
            var aft  = obj.name;
            var html = obj.html;

            if (aft === "오시" || aft === "미싱") {
                var cnt = $(prefix + afterKo2En(aft) + "_cnt").val();
                var selector = "input[name='" +
                               dvs + '_' +
                               afterKo2En(aft) + '_' +
                               cnt + "_val']";
                var m = obj.m;
                var c = obj.c;
                $(selector + "[dvs='M']").val(m);
                $(selector + "[dvs='C']").val(c);
            } else {
                $(prefix + afterKo2En(aft) + "_val").html(html);

                if (aft === "접지") {
                    $(prefix + afterKo2En(aft) + "_val").trigger("change");
                }

            }
        }

        reCalcAfterPrice(dvs, null);
    };

    ajaxCall(url, "json", data, callback);
};

/**
 * @brief 후공정 가격 합산해서 반환
 *
 * @param dvs = 제품 구분값
 *
 * @return 합산된 후공정 가격
 */
var getSumAfterPrice = function(dvs) {
    var ret = 0;
    var prefix = getPrefix(dvs);

    $("input[type='checkbox'][name='" + dvs + "_chk_after[]']").each(function() {
        if ($(this).prop("checked") === false) {
            return true;
        }

        var aft = $(this).attr("aft");
        var temp = $(prefix + aft + "_price").val();
        if (checkBlank(temp) === false) {
            temp = parseInt(temp);
            ret += temp;
        }
    });

    return ret;
};

/******************************************************************************
 * getAterPrice 이후 공통처리 함수
 *****************************************************************************/

/**
 * @brief 후공정 가격 공통처리 함수
 *
 * @param aft      = 후공정 구분값
 * @param dvs      = 제품 구분값
 * @param selector = mpcode 객체 selector
 */
var getAfterPriceCommon = function(aft, dvs, selector) {
    var priceArr  = getAfterPrice.price[aft];
    var prefix = getPrefix(dvs) + aft;
    var name   = $(prefix).val();

    if (checkBlank(priceArr) === true) {
        var data = {
            "cate_sortcode" : sortcode,
            "after_name"    : name
        };

        getAfterPrice.load(aft, data, dvs);
        return false;
    }

    var mpcode = null;
    if (selector === null) {
        mpcode = $("#" + dvs + '_' + aft + "_val").val();
    } else {
        mpcode = $(selector).val();
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var afterPrice = getAfterCalcPrice(dvs, priceArr);

    if (aft === "binding") {
        // 제본은 가격을 따로 계산하므로 가격파라미터에 false 전달
	calcBindingPrice.price = afterPrice;
        setAfterPrice(dvs, aft, false);
	return false;
    }

    setAfterPrice(dvs, aft, afterPrice);

    calcPrice();

    return false;
};

/**
 * @brief 가격 배열에서 후공정 가격 계산해서 반환
 *
 * @param priceArr = 가격 배열
 *
 * @return 계산된 가격
 */
var getAfterCalcPrice = function(dvs, priceArr) {
    var crtrUnit = priceArr.crtr_unit;
    var prefix   = getPrefix(dvs);
    var amt      = parseFloat($(prefix + "amt").val());

    // 책자 때문에 추가
    if (typeof commonDvs !== "undefined") {
        prefix   = getPrefix(commonDvs);
        amt      = parseFloat($(prefix + "amt").val());
    }

    // 표지 종이수량
    amt = amtCalc(dvs, amt, amtUnit, crtrUnit);

    return calcAfterPrice(priceArr, amt);
};

/**
 * @brief 후공정 가격 배열에서 해당하는 수량의 가격 검색해서
 * 가격 계산 후 반환
 *
 * @param priceArr = 가격 배열
 * @param amt      = 수량
 *
 * @return 가격
 */
var calcAfterPrice = function(priceArr, amt) {
    // 서버에서 받은 json 수량이 틀어져서 추가
    var amtArr = [];

    // 수량만 별도 배열에 추가
    for (afterAmt in priceArr) {
        if (afterAmt === "crtr_unit") {
            continue;
        }

        amtArr.push(parseFloat(afterAmt));
    }

    amtArr.sort(function(a, b) {return a - b;});

    var amtLen = amtArr.length;
    for (var i = 0; i < amtLen; i++) {
        if (amt <= amtArr[i]) {
            amt = amtArr[i];
            break;
        }
    }

    var price = priceArr[amt];

    if (checkBlank(price)) {
        price = priceArr[amtArr[amtArr.length - 1]];
    }

    return ceilVal(price);
};

/**
 * @brief 라미넥스 가격 공통계산 함수
 *
 * @param aft = 후공정 구분값
 * @param dvs = 제품 구분값
 */
var getLaminexPrice = function(aft, dvs) {
    var prefix    = getPrefix(dvs);
    var aftPrefix = getPrefix(dvs) + aft;
    var priceArr  = getAfterPrice.price[aft];

    if (checkBlank(priceArr) === true) {
        var data = {
            "cate_sortcode" : sortcode,
            "after_name"    : $(aftPrefix).val()
        };

        getAfterPrice.load(aft, data, dvs);
        return false;
    }

    var mpcode = $(aftPrefix + "_val").val();

    priceArr = priceArr[mpcode];

    var amt = $(aftPrefix + "_info").val();

    var afterPrice = calcAfterPrice(priceArr, amt);

    setAfterPrice(dvs, aft, afterPrice);

    calcPrice();
};

/**
 * @brief 미싱 가격 공통계산 함수
 *
 * @param aft = 후공정 구분값
 * @param dvs = 제품 구분값
 */
var getDotlinePrice = function(aft, dvs) {
    var selector = "input[name='" +
                   dvs + '_' + aft + '_' +
                   $("#" + dvs + '_' + aft + "_cnt").val() +
                   "_val']:checked";

    var mpcode = $(selector).val();

    if (checkBlank(mpcode) === true) {
        return false;
    }

    return getAfterPriceCommon(aft, dvs, selector);
};

/**
 * @brief 박, 형압 가격 계산
 *
 * @param aft = 후공정 구분값
 * @param dvs = 제품 구분값
 * @param amt = 수량
 */
var getAfterFoilPressPrice = function(aft, dvs, amt) {
    var prefix = getPrefix(dvs);
    var aftPrefix = prefix + aft + '_';

    var name = (aft === "foil") ? "박" : "형압";
    var wid_1  = parseInt($(aftPrefix + "wid_1").val());
    var vert_1 = parseInt($(aftPrefix + "vert_1").val());
    var wid_2  = parseInt($(aftPrefix + "wid_2").val());
    var vert_2 = parseInt($(aftPrefix + "vert_2").val());

    var aft_1 = $(aftPrefix + "1").val();
    var dvs_1 = $(aftPrefix + "dvs_1").val();
    var aft_2 = $(aftPrefix + "2").val();
    var dvs_2 = $(aftPrefix + "dvs_2").val();

    if (aft === "foil") {
        // 박
        if (checkBlank(aft_1) && checkBlank(dvs_1) &&
                checkBlank(aft_2) && checkBlank(dvs_2)) {
            return false;
        }
	
        if (dvs_1 === "양면") {
            if (isNaN(wid_1) || isNaN(vert_1)) {
                 return false;
            }

            if (wid_1 < 10 || vert_1 < 10) {
                wid_1  = $(aftPrefix + "wid_1").val("10");
                vert_1 = $(aftPrefix + "vert_1").val("10");

                alert("최조 사이즈는 10*10 입니다.");

                wid_1  = 10;
                vert_1 = 10;
            }
        } else if (!checkBlank(aft_1) && !checkBlank(dvs_1) &&
                checkBlank(aft_2) && checkBlank(dvs_2)) {
            // 전면만
            if (isNaN(wid_1) || isNaN(vert_1)) {
                 return false;
            }

            if (wid_1 < 10 || vert_1 < 10) {
                wid_1  = $(aftPrefix + "wid_1").val("10");
                vert_1 = $(aftPrefix + "vert_1").val("10");

                alert("최조 사이즈는 10*10 입니다.");

                wid_1  = 10;
                vert_1 = 10;
            }
        } else if (checkBlank(aft_1) && checkBlank(dvs_1) &&
                !checkBlank(aft_2) && !checkBlank(dvs_2)) {
            // 후면만
            if (isNaN(wid_2) || isNaN(vert_2)) {
                 return false;
            }

            if (wid_2 < 10 || vert_2 < 10) {
                wid_2  = $(aftPrefix + "wid_2").val("10");
                vert_2 = $(aftPrefix + "vert_2").val("10");

                alert("최조 사이즈는 10*10 입니다.");

                wid_2  = 10;
                vert_2 = 10;
            }
        } else if (!checkBlank(aft_1) && !checkBlank(dvs_1) &&
                !checkBlank(aft_2) && !checkBlank(dvs_2)) {
            if (isNaN(wid_1) || isNaN(vert_1) ||
                    isNaN(wid_2) || isNaN(vert_2)) {
                 return false;
            }

            if (wid_2 < 10 || vert_2 < 10 ||
                    wid_2 < 10 || vert_2 < 10) {
                wid_1  = $(aftPrefix + "wid_1").val("10");
                vert_1 = $(aftPrefix + "vert_1").val("10");
                wid_2  = $(aftPrefix + "wid_2").val("10");
                vert_2 = $(aftPrefix + "vert_2").val("10");

                alert("최조 사이즈는 10*10 입니다.");

                wid_1  = 10;
                vert_1 = 10;
                wid_2  = 10;
                vert_2 = 10;
            }
        } else {
            return false;
        }
    } else {
        // 형압
        if (isNaN(wid_1) || isNaN(vert_1)) {
             return false;
        }

        if (wid_1 < 10 || vert_1 < 10) {
            wid_1  = $(aftPrefix + "wid_1").val("10");
            vert_1 = $(aftPrefix + "vert_1").val("10");

            alert("최조 사이즈는 10*10 입니다.");

            wid_1  = 10;
            vert_1 = 10;
        }
    }

    var url = "/ajax/product/load_after_foil_press_price.php";
    var data = {
        "cate_sortcode" : $(prefix + "cate_sortcode").val(),
        "aft"           : aft,
        "amt"           : amt,
        "aft_1"         : aft_1,
        "dvs_1"         : dvs_1,
        "aft_2"         : aft_2,
        "dvs_2"         : dvs_2,
        "wid_1"         : wid_1,
        "vert_1"        : vert_1,
        "wid_2"         : wid_2,
        "vert_2"        : vert_2
    };
    var callback = function(result) {
        if (result === "-1") {
            return alertReturnFalse("가격 계산에 실패했습니다.");
        }

        if (!checkBlank(result.val_1)) {
            $(aftPrefix + "val_1").val(result.val_1);
        }

        if (!checkBlank(result.val_2)) {
            $(aftPrefix + "val_2").val(result.val_2);
        }

        setAfterPrice(dvs, aft, result.price);

        calcPrice();
    };

    ajaxCall(url, "json", data, callback);
};

/**
 * @brief 자유형 도무송 가격 계산
 *
 * @param aft      = 후공정 구분값
 * @param dvs      = 제품 구분값
 * @param stanName = 제품 구분값
 * @param amt      = 제품 구분값
 */
var getFreeTomsonPrice = function(aft, dvs, param) {
    var prefix = getPrefix(dvs);

    var wid  = param.wid;
    var vert = param.vert;

    if (checkBlank(wid) || checkBlank(vert)) {
        return false;
    }

    wid  = parseInt(wid);
    vert = parseInt(vert);

    if (wid < 10 || vert < 10) {
        alert("최소사이즈는 10*10 입니다.");

        if (wid < 10) {
            wid  = 10;
            $(prefix + aft + "_wid").val(wid);
        }
        if (vert < 10) {
            vert = 10;
            $(prefix + aft + "_vert").val(vert);
        }
    }

    var url = "/ajax/product/load_free_tomson_price.php";
    var data = {
        "paper_name" : $(prefix + "paper > option:selected").text(),
        "stan_name"  : param.stanName,
        "amt"        : param.amt,
        "size_width" : wid,
        "size_vert"  : vert
    };

    ajaxCall(url, "text", data, param.callback);
};

/**
 * @brief 제본가격 계산
 *
 * @param dvs   = 제품구분
 * @param price = 기준가격
 */
var calcBindingPrice = {
    "price"   : 0, // 배열에 저장된 값
    "getPage" : function() {
        var page = 0;

        var arrLength = dvsArr.length;
        for (var i = 0; i < arrLength; i++) {
            var dvs = dvsArr[i];
            var pfx = getPrefix(dvs);
    
            if (!dvsOnOff[dvs]) {
                continue;
            }
    
            var val = parseInt($(pfx + "page").val());
            page += val;
        }

        return page;
    },
    "exec" : function(dvs, price) {
        var flattypYn = getAfterPrice.flattypYn;
    
        // 낱장형이면 해당 가격 사용
        if (flattypYn) {
            return price;
        }
    
	// 이하 책자형
        var prefix = getPrefix(dvs);

        var amt      = $(prefix + "amt").val();
        var page     = calcBindingPrice.getPage();
        var depth1   = $(prefix + "binding_depth1 > option:selected").text();
        var stanName = $(prefix + "size > option:selected").text();
        var coatingYn = $("#cover_coating").prop("checked");

        var url = "/ajax/product/load_calc_binding_price.php";
        var data = {
            "cate_sortcode" : $(prefix + "cate_sortcode").val(),
            "amt"           : amt,
            "page"          : page,
            "price"         : calcBindingPrice.price,
            "depth1"        : depth1,
            "coating_yn"    : coatingYn,
            "stan_name"     : stanName
        };
	var callback = function(result) {
            calcBindingPrice.price = parseInt(result);
	    setAfterPrice(dvs, "binding", result);
	};

	ajaxCall(url, "text", data, callback);
    }
};

/**
 * @brief 페이지 수에 따라서 가능한 제본만 표시
 *
 * @param page = 페이지수
 */
var chkBookletBinding = function(page) {
    var prefix = getPrefix(commonDvs);
    var $depth1Obj     = $(prefix + "binding_depth1");
    var selectedDepth1 = $depth1Obj.find("option:selected").text();
    var depth1Str      = $depth1Obj.text().trim();

    if (page < 28) {
        // 4 ~ 28p 까지는 중철제본만
	// 무선제본 삭제
        if (depth1Str.indexOf("무선") > -1) {
            $depth1Obj.find("option[value='무선제본']").remove();
	}

	// 선택되어있던 값이 무선제본일 경우 중철제본 depth2 재검색
	if (selectedDepth1 === "무선제본") {
            var html = "<option value=\"중철제본\">중철제본</option>";
            $depth1Obj.html(html);
            return loadBindingDepth2("중철제본", commonDvs);
        }
    } else if (28 <= page && page <= 36) {
        // 28 ~ 36p 까지는 중철/무선제본 둘 다 -> 둘 다 만들기
        var html = "<option value=\"중철제본\">중철제본</option>";
        html += "<option value=\"무선제본\">무선제본</option>";

        $depth1Obj.html(html);

        return loadBindingDepth2("중철제본", commonDvs);
    } else if (36 < page) {
        // 36p 초과는 무선제본만 -> 중철제본 삭제
	// 중철제본 삭제
        if (depth1Str.indexOf("중철") > -1) {
            $depth1Obj.find("option[value='중철제본']").remove();
	}

	// 선택되어있던 값이 무선제본일 경우 중철제본 depth2 재검색
	if (selectedDepth1 === "중철제본") {
            var html = "<option value=\"무선제본\">무선제본</option>";
            $depth1Obj.html(html);
            return loadBindingDepth2("무선제본", commonDvs);
        }
    }

    return true;
};

/**
 * @brief 제약사항 개개별로 체크하는 함수
 *
 * @param dvs = 제품구분값
 * @param aft = 후공정 영문명
 */
var chkAftRestrict = function(dvs, aft) {
    var ret = true;

    if (!checkBlank(aftRestrict[aft])) {
        aftRestrict.msg = '';
        ret = aftRestrict[aft].common(dvs);
    }
    
    if (!checkBlank(aftRestrict.msg)) {
        alert(aftRestrict.msg);
    }

    aftRestrict.msg = '';
    
    if (!ret) {
        setAfterPrice(dvs, aft, 0);
        calcPrice(false);
        ret = false;
    }

    return ret;
};

/******************************************************************************
 * 후공정 하위 depth 관련 함수
 *****************************************************************************/

/**
 * @brief 후공정 하위항목 검색
 *
 * @param data     = ajaxCall에서 사용할 파라미터
 * @param callback = ajaxCall에서 사용할 callback함수
 */
var loadAfterDepth = function(data, callback) {
    var url = "/ajax/product/load_after_depth.php";
    ajaxCall(url, "html", data, callback);
};

/**
 * @brief 제본 하위항목 검색
 *
 * @param val = 제본 depth1 이름
 * @param dvs = 제품 구분값
 */
var loadBindingDepth2 = function(val, dvs) {
    var prefix = getPrefix(dvs);

    var data = {
        "cate_sortcode" : $(prefix + "cate_sortcode").val(),
        "after_name"    : "제본",
        "depth1"        : val,
        "size"          : $(prefix + "size > option:selected").text(),
        "flag"          : 'Y'
    };
    var callback = function(result) {
        $(prefix + "binding_val").html(result);
        getAfterPrice.common("binding", dvs);
    };

    loadAfterDepth(data, callback);

    return true;
};

/**
 * @brief 제본 하위항목 검색
 *
 * @param val = 제본 depth1 이름
 */
var loadRoundingDepth2 = function(val, dvs) {
    var prefix = getPrefix(dvs);
    var callback = function(result) {
        $(prefix + "rounding_val").html(result);
        getAfterPrice.common("rounding", dvs);
    };

    var data = {
        "cate_sortcode" : $(prefix + "cate_sortcode").val(),
        "after_name"    : "귀도리",
        "depth1"        : val,
        "flag"          : 'Y'
    };

    loadAfterDepth(data, callback);
};

/**
 * @brief 타공 하위항목 검색
 *
 * @param val = 타공 depth1 이름
 * @param dvs = 제품구분값
 */
var loadPunchingDepth2 = function(val, dvs) {
    var prefix = getPrefix(dvs);
    var callback = function(result) {
        $(prefix + "punching_val").html(result);
        getAfterPrice.common("punching", dvs);
    };

    var data = {
        "cate_sortcode" : $(prefix + "cate_sortcode").val(),
        "after_name"    : "타공",
        "depth1"        : val + '개',
        "flag"          : 'Y'
    };

    loadAfterDepth(data, callback);
};

/**
 * @brief 박 하위항목 검색
 *
 * @param val = 후공정 depth1, depth2
 * @param dvs = 제품구분값
 */
var loadFoilDepth2 = function(val, dvs) {
    var prefix = getPrefix(dvs);
    var callback = function(result) {
        $(prefix + "foil_dvs").html(result);
        getAfterPrice.common("foil", dvs);
    };

    var data = {
        "cate_sortcode" : $(prefix + "cate_sortcode").val(),
        "after_name"    : "박",
        "depth1"        : val,
        "flag"          : 'Y'
    };

    loadAfterDepth(data, callback);
};

/**
 * @brief 형압 하위항목 검색
 *
 * @param val = 후공정 depth1, depth2
 * @param dvs = 제품구분값
 */
var loadPressDepth2 = function(val, dvs) {
    var prefix = getPrefix(dvs);
    var callback = function(result) {
        $(prefix + "press_dvs").html(result);
        getAfterPrice.common("press", dvs);
    };

    var data = {
        "cate_sortcode" : $(prefix + "cate_sortcode").val(),
        "after_name"    : "형압",
        "depth1"        : val,
        "flag"          : 'Y'
    };

    loadAfterDepth(data, callback);
};

/**
 * @brief 도무송 하위항목 검색
 *
 * @param val = 후공정 depth1, depth2
 * @param dvs = 제품구분값
 */
var loadThomsonDepth2 = function(val, dvs) {
    var prefix = getPrefix(dvs);
    var callback = function(result) {
        $(prefix + "thomson_val").html(result);
        getAfterPrice.common("thomson", dvs);
    };

    var data = {
        "cate_sortcode" : $(prefix + "cate_sortcode").val(),
        "after_name"    : "도무송",
        "depth1"        : val,
        "flag"          : 'Y'
    };

    loadAfterDepth(data, callback);
};

/**
 * @brief 접착 하위항목 검색
 *
 * @param val = 후공정 depth1, depth2
 * @param dvs = 제품구분값
 */
var loadBondingDepth2 = function(val, dvs) {
    var prefix = getPrefix(dvs);
    var callback = function(result) {
        $(prefix + "bonding_val").html(result);
        getAfterPrice.common("bonding", dvs);
    };

    var data = {
        "cate_sortcode" : $(prefix + "cate_sortcode").val(),
        "after_name"    : "접착",
        "depth1"        : val,
        "flag"          : 'Y'
    };

    loadAfterDepth(data, callback);
};

/**
 * @brief 접지 하위항목 검색
 *
 * @param val = 후공정 depth1, depth2
 * @param dvs = 제품구분값
 */
var loadFoldlineDepth2 = function(val, dvs) {
    var prefix = getPrefix(dvs);
    var callback = function(result) {
        $(prefix + "foldline_val").html(result);
        getAfterPrice.common("foldline", dvs);
        preview.foldline();
    };

    var data = {
        "cate_sortcode" : $(prefix + "cate_sortcode").val(),
        "size"          : $(prefix + "size > option:selected").text(),
        "after_name"    : "접지",
        "depth1"        : val,
        "flag"          : 'Y'
    };

    loadAfterDepth(data, callback);
};

/**
 * @brief 가공 하위항목 검색
 *
 * @param val = 후공정 depth1, depth2
 * @param dvs = 제품구분값
 */
var loadManufactureDepth2 = function(val, dvs) {
    var prefix = getPrefix(dvs);
    var callback = function(result) {
        $(prefix + "manufacture_val").html(result);
        getAfterPrice.common("manufacture", dvs);
    };

    var data = {
        "cate_sortcode" : $(prefix + "cate_sortcode").val(),
        "after_name"    : "가공",
        "depth1"        : val,
        "flag"          : 'Y'
    };

    loadAfterDepth(data, callback);
};

/**
 * @brief 각 후공정별 가격 배열 반환
 *
 * @param dvs = 제품구분값
 *
 * @return [{ko:귀도리, en:rounding, price:1000}, ...]
 */
var getAfterPriceArr = function(dvs) {
    var prefix = getPrefix(dvs);
    var ret = [];

    $("input[type='checkbox'][name='" + dvs + "_chk_after[]']").each(function() {
        if ($(this).prop("checked") === false) {
            return true;
        }

        var obj = {};

        var aftKo = $(this).attr("aft");
        var aftEn = afterKo2En(aftKo);
        var price = $(prefix + aftEn + "_price").val();

	obj.ko = aftKo;
	obj.en = aftEn;
	obj.price = price;

	ret.push(obj);
    });

    return ret;
};

/**
 * @brief 종이 변경 시 라미넥스 최대수량 조정
 *
 * @detail 90아트는 라미넥스 가능수량이 1천장까지 제한되지만 120g이상일때는 모든 수량 가능함
 */
var calcLaminexMaxCount = function() {
    var prefix = getPrefix(prdtDvs);
    var paper = $(prefix + "paper > option:selected").text();
    paper = paper.split(' ');
    var basisweight = parseInt(paper[paper.length - 1]);

    if (parseInt(basisweight) >= 120) {
        aftRestrict.laminex.max = parseInt($(prefix + "sheet_count").val());
        $("#bl_laminex_max").text($("#sheet_count_span").text());
    } else {
        aftRestrict.laminex.max = 1000;
        $("#bl_laminex_max").text("1,000");
    }
};

/**
 * @brief 빠른 견적서 후공정 부분 감춤
 *
 * @param aft = 후공정 영문
 */
var quickEstiAftHide = function(aft) {
    $("#esti_" + aft + "_dt").hide();
    $("#esti_" + aft + "_dd").hide();
}
