var monoYn    = null;
var prdtDvs   = null;
var affil     = null;
var sortcode  = null;
var amtUnit   = null;
var cateName  = null;

$(document).ready(function() {
    // 건수 초기화
    initCount(99, "count");

    monoYn   = $("#bl_mono_yn").val();
    prdtDvs  = $("#prdt_dvs").val();
    affil    = $("#bl_size").find("option:selected").attr("affil");
    sortcode = $("#bl_cate_sortcode").val();
    cateName = $("#bl_cate_sortcode").find("option:selected").text();
    amtUnit  = $("#bl_amt").attr("amt_unit");

    if (amtUnit === 'R') {
        $("#sheet_count_div").show();
        calcSheetCount(prdtDvs);
    } else {
        var max = $("#bl_amt > option:last-child").val();
        aftRestrict.laminex.max = parseInt(max);
        $("#bl_laminex_max").html(max.format());
    }

    $("input[name='chk_opt']").each(function() {
        var opt = $(this).val();

        if (opt === "당일판") {
            var idx =  $(this).attr("id").split('_')[1];
            optRestrict[opt].common(prdtDvs, idx, false);
            return false;
        }
    });

	showUvDescriptor(prdtDvs);
    calcLaminexMaxCount();
    changeReleaseStr();
});

/**
 * @brief 종이 바뀔 때 평량에 따라 규격 변경
 *
 * @param dvs = 제품구분값
 * @param val = 종이맵핑코드
 */
var changePaper = function(dvs, val) {
    var prefix = getPrefix(dvs);
    var affil  = $(prefix + "size > option:selected").attr("affil");
    var posNum = $(prefix + "size > option:selected").attr("pos_num");

    changeSizeDvs.flag = false;

    $("#size_dvs").val("stan");
    $("#size_dvs").trigger("change");

    changeReleaseStr();

    var url = "/ajax/product/load_paper_size.php";
    var data = {
        "cate_sortcode" : sortcode,
        "mono_yn"       : $(prefix + "mono_yn").val(),
        "affil_yn"      : affil,
        "pos_yn"        : posNum,
        "size_typ_yn"   : 'N',
        "paper_mpcode"  : val
    };
    var callback = function(result) {
        $(prefix + "size").html(result);
        size();

        loadPrdtAmt(dvs);
    };

    loadPaperPreview(dvs);

    ajaxCall(url, "html", data, callback);
};

/**
 * @brief 바뀐 종이와 사이즈로 수량 변경
 *
 * @param dvs = 제품구분값
 * @param val = 종이맵핑코드
 */
var loadPrdtAmt = function(dvs, val) {
    var prefix = getPrefix(dvs);

    var url = "/ajax/product/load_amt.php";
    var data = {
        "cate_sortcode" : sortcode,
        "mono_yn"       : $(prefix + "mono_yn").val(),
        "amt_unit"      : amtUnit,
        "stan_mpcode"   : $(prefix + "size").val(),
        "paper_mpcode"  : $(prefix + "paper").val()
    };
    var callback = function(result) {
        $(prefix + "amt").html(result);

        if (amtUnit === 'R') {
            calcSheetCount(dvs);
        } else {
        }

        var aftInfoArr = getAftInfoArr(dvs);
        var size = $(prefix + "size > option:selected").text();
        loadAfterMpcode(dvs, aftInfoArr, size);

        rangeBarBySelect();
        changeData();
        calcLaminexMaxCount();
        changeReleaseStr();
    };

    ajaxCall(url, "html", data, callback);
};

/**
 * @brief 계산형일 때 사이즈 선택할 경우 사이즈 계열에 맞는 도수값 검색
 *
 * @param val = 구분값
 */
var changeSize = {
    "exec" : function(dvs) {
        loadPrdtAmt(dvs);
    }
};

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 */
var changeData = function() {
    changeSizeDvs.flag = true;
    monoYn = $("#bl_mono_yn").val();

    var data ={
        "dvs"           : prdtDvs,
        "mono_yn"       : monoYn,
        "cate_sortcode" : sortcode,
        "amt"           : $("#bl_amt").val(),
        "stan_mpcode"   : $("#bl_size").val(),
        "affil"         : affil
    };

    data.flag = $("#frm").find("input[name='flag']").val();
    data.paper_mpcode       = $("#bl_paper").val();
    data.bef_print_mpcode     = $("#bl_print_tmpt").val();
    data.bef_add_print_mpcode = '';
    data.aft_print_mpcode     = '';
    data.aft_add_print_mpcode = '';
    data.print_purp         = $("#bl_print_purp").val();
    data.page_info          = "2";

    loadPrdtPrice.data = data;
    loadPrdtPrice.exec();
};

/**
 * @brief 상품 가격정보 json으로 반환
 */
var loadPrdtPrice = {
    "data"  : {},
    "price" : {},
    "exec"  : function() {
        var url = null;
        if (monoYn === '0') {
            url = "/ajax/product/load_ply_price.php";
        } else {
            url = "/ajax/product/load_calc_price.php";
        }
        var callback = function(result) {
            if (checkBlank(result[prdtDvs].sell_price) === true) {
                return alertReturnFalse("해당하는 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
            }

            loadPrdtPrice.price = result[prdtDvs];

            calcPrice();
        };

        ajaxCall(url, "json", loadPrdtPrice.data, callback);
    }
};

/**
 * @brief 화면에 출력되는 가격 및 빠른견적서 내용 수정
 *
 * @param flag = 옵션가격 재검색 후 무한루프방지
 */
var calcPrice = function(flag) {
    // 건수
    var count = parseInt($("#count").val());
    // 정상판매가
    var sellPrice = loadPrdtPrice.price.sell_price;
    if (checkBlank(sellPrice)) {
        sellPrice = parseInt($("#sell_price").attr("val").replace(',', ''));
        loadPrdtPrice.price.sell_price = sellPrice;
    }
    sellPrice  = ceilVal(sellPrice);
    sellPrice *= count;
    // 등급 할인율
    var gradeSale = parseFloat($("#bl_grade_sale_rate").val());
    gradeSale /= 100.0;
    // 회원 할인율
    var memberSale = parseFloat($("#bl_member_sale_rate").val());
    memberSale /= 100.0;
    // 옵션비 총합
    var sumOptPrice = getSumOptPrice();
    sumOptPrice = ceilVal(sumOptPrice);
    // 후공정비 총합
    var sumAfterPrice = getSumAfterPrice(prdtDvs);
    sumAfterPrice = ceilVal(sumAfterPrice);

    // 견적서 종이비 계산
    var paper  = 0;
    if (monoYn === '1') {
        paper  = parseInt(loadPrdtPrice.price.paper);
        paper  = ceilVal(paper);
        paper *= count;
    }

    // 견적서 출력비 계산
    var output = 0;
    if (monoYn === '1') {
        output  = parseInt(loadPrdtPrice.price.output);
        output  = ceilVal(output);
        output *= count;
    }

    // 견적서 인쇄비 계산
    var print  = sellPrice;
    if (monoYn === '1') {
        print  = parseInt(loadPrdtPrice.price.print);
        print  = ceilVal(print);
        print *= count;
    }

    // 견적서 후공정비 계산
    var after = sumAfterPrice;
    // 견적서 옵션비 계산
    var opt = sumOptPrice;
    // 견적서 합계 계산
    var sum = paper + output + print + after + opt;

    // 회원등급 할인가 계산
    var calcGradeSale = sellPrice * gradeSale;
    calcGradeSale = ceilVal(calcGradeSale);
    // 회원 할인가 계산
    var calcMemberSale = (sellPrice + calcGradeSale) * memberSale;
    calcMemberSale = ceilVal(calcMemberSale);
    // 기본할인가 계산
    var calcSalePrice = sellPrice + calcGradeSale + calcMemberSale;
    // 결제금액 계산
    var calcPayPrice = calcSalePrice + after + opt;
    // 부가세
    var tax = Math.round(calcPayPrice / 11);
    // 공급가
    var supplyPrice = calcPayPrice - tax;

    // 정상판매가 변경
    $("#sell_price").attr("val", (sellPrice + after + opt));
    $("#sell_price").html((sellPrice + after + opt).format() + ' 원');
    // 회원등급 할인가 변경
    $("#grade_sale").html((calcGradeSale + calcMemberSale).format() + ' 원');
    // 결제금액 변경
    $("#sale_price").attr("val", calcPayPrice);
    $("#sale_price").html(calcPayPrice.format());
    // 공급가 변경
    $("#supply_price").html(supplyPrice.format());
    // 부가세 변경
    $("#tax").html(tax.format());

    var param = {
        "paper"         : paper,
        "print"         : print,
        "output"        : output,
        "after"         : after,
        "opt"           : opt,
        "count"         : count,
        "gradeSaleRate" : gradeSale,
        "sellPrice"     : sellPrice
    };

    changeQuickEsti(param);

    if (flag === false) {
        return false;
    }

    reCalcOptPrice(prdtDvs, null);
};

/**
 * @param 인쇄방식에 해당하는 인쇄도수 검색
 *
 * @param val = 인쇄방식
 */
var loadPrintTmpt = function(dvs) {
    var callback = function(result) {
        $("#bl_print_tmpt").html(result.bef_tmpt);

        if (monoYn === '1') {
            changeData();
        }
    };

    loadPrintTmptCommon.exec(dvs, callback);
};

/**
 * @brief 비규격 사이즈 선택할 경우 재단사이즈 값 초기화
 *
 * @param val  = 구분값
 */
var changeSizeDvs = {
    "flag" : true,
    "exec" : function(val) {
        // 비규격 사이즈 선택시 기본 사이즈로 데이터 변경
        var prefix = getPrefix(prdtDvs);
        $(prefix + "similar_size").attr("divide", '1');

        // 비규격 사이즈 선택시 기본 사이즈로 데이터 변경
        if (val === "manu") {
            var str = $(prefix + "size > option:selected").text() + " 1/1 등분";

            $(prefix + "similar_size").show();
            $(prefix + "similar_size").html(str);
        } else {
            $(prefix + "similar_size").hide();
            calcSheetCount(prdtDvs);
        }
    
        if (changeSizeDvs.flag) {
            changeData();
        }
    }
};

/**
 * @brief 수량변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeAmt = function() {
    if (amtUnit === 'R') {
        calcSheetCount(prdtDvs);
    } else {
    }
    changeReleaseStr();
    reCalcAfterPrice(prdtDvs, null);
    changeData();
    calcLaminexMaxCount();
};

/**
 * @brief 건수변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeCount = function() {
    reCalcAfterPrice(prdtDvs, null);
    calcPrice();
}

/**
 * @brief 관심상품 등록이나 장바구니 전에 데이터 세팅
 */
var setSubmitParam = function() {
    if ($("#il").val() === "0") {
        $("#cart_flag").val('Y');
        alert("로그인 후 확인 가능합니다.");
        return false;
    }

    if (checkBlank($("#title").val().trim()) === true) {
        $("#cart_flag").val('Y');
        alert("인쇄물제목을  입력해주세요.");
        $("#title").focus();
        return false;
    }

    if (!aftRestrict.all(prdtDvs)) {
        return false;
    }

    var amtUnit   = $("#bl_amt").attr("amt_unit");
    var paperName = $("#bl_paper").find("option:selected").text();
    var tmptName  = $("#bl_print_tmpt").find("option:selected").text();
    var sizeName  = $("#bl_size").find("option:selected").text();

    var sellPrice     = $("#sell_price").attr("val");
    var salePrice     = $("#sale_price").attr("val");
    var afterPrice    = getSumAfterPrice(prdtDvs);
    var optPrice      = getSumOptPrice();

    var ret = makeAfterInfo.all(prdtDvs);

    if (ret === false) {
        return false;
    }

    setAddOptInfo();

    $frm = $("#frm");

    $frm.find("input[name='bl_cate_name']").val(cateName);
    $frm.find("input[name='bl_amt_unit']").val(amtUnit);
    $frm.find("input[name='bl_paper_name']").val(paperName);
    $frm.find("input[name='bl_bef_tmpt_name']").val(tmptName);
    $frm.find("input[name='bl_size_name']").val(sizeName);

    $frm.find("input[name='bl_sell_price']").val(sellPrice);
    $frm.find("input[name='bl_sale_price']").val(salePrice);
    $frm.find("input[name='bl_after_price']").val(afterPrice);
    $frm.find("input[name='bl_opt_price']").val(optPrice);
};

/**
 * @brief 견적서 팝업 본문정보 생성
 */
var makeEstiPopInfo = {
    "data" : null,
    "exec" : function(dvs) {
        var prefix = getPrefix(prdtDvs);
    
        var paper = $.trim($("#bl_paper > option:selected").text());
        var size  = $.trim($(prefix + "size > option:selected").text());
        var tmpt  = $.trim($("#bl_print_tmpt > option:selected").text());
        var amt   = $.trim($("#bl_amt").val());
        var count = $.trim($("#esti_count").text());

        var after = '';
        $('.after .overview ul li').each(function() {
            after += $(this).text();
            after += ', ';
        });
        after = after.substr(0, after.length - 2);

        var data = {
            "cate_name" : [
                cateName
            ],
            "paper" : [
                paper
            ],
            "size" : [
                size
            ],
            "tmpt" : [
                tmpt
            ],
            "amt" : [
                amt
            ],
            "amt_unit" : [
                amtUnit
            ],
            "count" : [
                count
            ],
            "after" : [
                after
            ]
        };

		data = getEstiPopData(data);

        this.data = data;

        if (dvs === "pop") {
            getEstiPopHtml(data);
        } else {
            downEstiExcel(data);
        }
    }
};

/**
 * @brief 납기문구 수정
 */
var changeReleaseStr = function() {
    var prefix = getPrefix(prdtDvs);
    var paper = $(prefix + "paper > option:selected").text();
    paper = paper.split(' ');
    var basisweight = parseInt(paper[paper.length - 1]);
    var size = $(prefix + "size > option:selected").text();
    var amt = parseFloat($(prefix + "amt").val());

    var str = null;

    if (basisweight === 90) {
        str  = "* 평일 오후 7시 마감(토요일 접수 없음).";
        if (amt === 0.5) {
            str += "<br/>* 출고까지 2~3일 정도 소요됩니다.";
        } else if ((size === "8절" || size === "A3") &&
                amt === 1.0 || amt === 3.0) {
            str += "<br/>* 출고까지 2~3일 정도 소요됩니다.";
	} else {
            str += "<br/>* 익일 출고됩니다.";
	}
    /*
    } else if (basisweight === 120 || basisweight === 150 || basisweight === 180) {
        str  = "* 평일 오후 6시 마감(토요일 접수 없음).";
        str += "<br/>* 납기일은 3~4일 정도 소요됩니다.";
    */
    } else {
        str  = "* 평일 오후 6시 마감(토요일 접수 없음).";
        str += "<br/>* 익일 출고됩니다.";
    }

    $("#release_str").html(str);
};
