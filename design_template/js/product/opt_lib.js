/**
 * @brief 후공정 가격 세팅
 *
 * @param dvs = 제품 구분값
 * @param aft = 후공정 구분값
 * @param price = 후공정 가격
 */
var setOptPrice = function(idx, price) {
    $("#opt_" + idx).attr("price", price);
    $("#opt_" + idx + "_price").html(price.format() + '원');
};

/**
 * @brief 옵션 가격 검색
 *
 * @param obj = 체크확인용 객체
 * @param idx = 옵션 위치
 * @param dvs = 제품구분값
 */
var loadOptPrice = {
    "data" : {},
    "idx"  : null,
    "exec" : function(obj, idx, dvs) {

        if ($(obj).prop("checked") === false) {
            optSlideUp(idx);

            calcPrice(false);
            return false;
        }

        optSlideDown(idx);

        if (!chkOptRestrict(dvs, idx, $(obj).val())) {
            return false;
        }

        this.calc(idx, dvs);
    },
    "calc" : function(idx, dvs) {
        var prefix = getPrefix(dvs);
        var sellPrice = loadPrdtPrice.price.sell_price;

        if (checkBlank(sellPrice)) {
            sellPrice = $("#sell_price").attr("val");
        }

        this.idx = idx;

        var $paper = $(prefix + "paper > option:selected");
        var optName = $("#opt_" + idx).val();

        var url = "/ajax/product/load_opt_price.php";
        var data = {
            "cate_sortcode" : $(prefix + "cate_sortcode").val(),
            "name"          : optName,
            "amt"           : $(prefix + "amt").val(),
            "mpcode"        : $("#opt_" + idx + "_sel").val(),
            "sell_price"    : sellPrice,
            "paper_mpcode"  : $paper.val(),
            "paper_info"    : $paper.text(),
            "affil"         : $(prefix + "size > option:selected").attr("affil")
        };

        if ($(prefix + "sheet_count").length > 0) {
            data.sheet_count = $(prefix + "sheet_count").val();
        }

        var callback = function(result) {
            loadOptPrice.data[loadOptPrice.idx] = result;
            var count  = parseInt($("#count").val());
            if (isNaN(count)) {
                count = 1;
            }
            var optPrice = parseInt(result.price) * count;

            setOptPrice(idx, optPrice);

            var opt = $("#opt_" + idx).val();
            
            procOpt.exec(opt, idx, dvs);

            calcPrice(false);
        };
    
        ajaxCall(url, "json", data, callback);
    }
};

/**
 * @brief 특정 옵션들에 대해 별도의 연산이 필요한 경우 처리
 */
var procOpt = {
    "exec" : function(opt, idx, dvs) {
        if (checkBlank(this[opt])) {
            return false;
        }

        this[opt](idx, dvs);
    },
    "빠른생산요청" : function(idx, dvs) {
        // 맵핑코드 변경
        var prefix = "#opt_" + idx + '_';
        $(prefix + "sel > option:selected").attr("value",
                                                 loadOptPrice.data[idx].mpcode);
    }
};

/**
 * @brief 수량 변경시 옵션 가격 재계산 함수
 */
var reCalcOptPrice = function(dvs) {
    $("input[name='chk_opt']:checked").each(function() {
        var $obj = $(this);
        var opt = $obj.val();
        var idx  = parseInt($obj.attr("id").split('_')[1]);

	if (!chkOptRestrict(dvs, idx, opt)) {
            return false;
	}

        if ($obj.val() === "포장방법") {
            loadOptPrice.calc(idx,  dvs);
        } else if ($obj.val() === "빠른생산요청") {
            loadOptPrice.calc(idx, dvs);
        } else if ($obj.val() === "정매생산요청") {
            loadOptPrice.calc(idx, dvs);
        }
    });
};

/**
 * @brief 제약사항 개개별로 체크하는 함수
 *
 * @param dvs = 제품구분값
 * @param aft = 후공정 영문명
 */
var chkOptRestrict = function(dvs, idx, opt) {
    var ret = true;

    if (!checkBlank(optRestrict[opt])) {
        optRestrict.msg = '';
        ret = optRestrict[opt].common(dvs, idx);
    }

    if (optRestrict.alertFlag === false) {
        optRestrict.alertFlag = true;
        optRestrict.msg = '';
    }
    
    if (!checkBlank(optRestrict.msg)) {
        alert(optRestrict.msg);
    }

    optRestrict.msg = '';
    
    if (!ret) {
        setOptPrice(idx, 0);
        calcPrice(false);
        ret = false;
    }

    return ret;
};

/**
 * @brief 옵션 가격 합산해서 반환
 *
 * @return 합산된 옵션 가격
 */
var getSumOptPrice = function() {
    var ret = 0;
    var temp = null;

    $("input[name='chk_opt']").each(function() {
        if ($(this).prop("checked") === false) {
            return true;
        }

        temp = $(this).attr("price");
        if (checkBlank(temp) === false) {
            temp = parseInt(temp);
            ret += temp;
        }
    });

    return ret;
};

/**
 * @brief 추가 옵션 정보 생성
 */
var setAddOptInfo = function() {
    var id = null;
    var mpcode = "";
    var price = "";

    $("input[name='chk_opt']").each(function() {
        if ($(this).prop("checked") === false ||
                $(this).prop("disabled") === true) {
            return true;
        }

        id = $(this).attr("id");

        mpcode += $("#" + id + "_sel").val();
        mpcode += '|';

        price += $(this).attr("price");
        price += '|';
    });

    mpcode = mpcode.substr(0, (mpcode.length - 1));
    price = price.substr(0, (price.length - 1));

    $("#frm").find("input[name='opt_add']").val(mpcode);
    $("#frm").find("input[name='opt_add_price']").val(price);
};

/**
 * @brief 옵션 div객체 감춤
 *
 * @param idx = 객체 위치
 */
var optSlideUp = function(idx) {
    var $divObj = $("#opt_" + idx + "_div");
    $divObj.slideUp(300);
    $divObj.removeClass("_on");
};

/**
 * @brief 옵션 div객체 노출
 *
 * @param idx = 객체 위치
 */
var optSlideDown = function(idx) {
    var $divObj = $("#opt_" + idx + "_div");
    $divObj.slideDown(300);
    $divObj.addClass("_on");
};
