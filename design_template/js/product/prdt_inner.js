// calcPrice() 건너뛸 때 사용, 후공정 가격 재계산시 로직 반복타는거 방지
var passFlag  = false;
var tmptDvs   = null;
var monoYn    = '1';
var flattypYn = false;

$(document).ready(function() {
    tmptDvs = $("#inner_tmpt_dvs").val();

    getRealPaperAmt.exec("inner");
});

/**
 * @param 인쇄방식에 해당하는 인쇄도수 검색
 *
 * @param dvs = 인쇄방식을 선택한 위치구분값
 * @param val = 인쇄방식
 */
var loadPrintTmpt = function(dvs, val) {
    var callback = function(result) {
        var dvs = loadPrintTmptCommon.dvs;
        var prefix = getPrefix(dvs);

        $(prefix + "bef_tmpt").html(result.bef_tmpt);

        changeData(dvs);
    };

    loadPrintTmptCommon(dvs, val, callback);
};

/**
 * @brief 수량과 페이지수에 따른 실제 종이수량 계산
 *
 * @param info = 계산용 정보데이터
 */
var calcRealPaperAmt = function(info) {
    amt      = info["amt"];
    posNum   = info["posNum"];
    pageNum  = info["pageNum"].split('!')[0];
    amtUnit  = info["amtUnit"];

    // 0page일 경우 인쇄 수량 0 반환
    if (pageNum == 0) {
        return 0;
    }

    var ret = Math.round((amt / posNum) / (2 / pageNum));

    return ret;
};

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 *
 * @param dvs = 데이터 변경 영역 구분값
 */
var changeData = function(dvs) {
    var prefix = getPrefix(dvs);
    monoYn = $(prefix + "mono_yn").val();

    var data = {
        "cate_sortcode" : $("#cate_bot").val(),
        "stan_mpcode"   : $(prefix + "size").val(),
        "amt"           : $(prefix + "amt").val(),
        "dvs"           : dvs,
        "typ"           : $(prefix + "typ").val(),
        "mono_yn"       : monoYn,
        "tmpt_dvs"      : tmptDvs,
        "affil"         : $(prefix + "size").find("option:selected").attr("affil")
    };

    data.paper_mpcode       = $(prefix + "paper").val();
    data.bef_print_name     = $(prefix + "bef_tmpt").val();
    data.print_purp         = $(prefix + "print_purp").val();
    data.page_info          = $(prefix + "page").val();

    loadPrdtPrice.data = data;
    loadPrdtPrice.dvs  = dvs;
    loadPrdtPrice.exec();
};

/**
 * @brief 상품 가격정보 json으로 반환
 */
var loadPrdtPrice = {
    "data"  : null,
    "dvs"   : null,
    "price" : {
        // 가격이 있는지 확인
        "inner" : false
    },
    "exec"  : function() {
        var url = "/ajax/product/load_calc_price.php";
        var callback = function(result) {
            var dvs = loadPrdtPrice.dvs;
            var prefix = getPrefix(dvs);

            loadPrdtPrice.price[dvs] = true;

            var paperPrice  = result[dvs].paper;
            var printPrice  = result[dvs].print;
            var outputPrice = result[dvs].output;
            var price = result[dvs].price;

            $(prefix + "paper_price").val(paperPrice);
            $(prefix + "print_price").val(printPrice);
            $(prefix + "output_price").val(outputPrice);
            $(prefix + "prdt_price").val(price);

            calcPrice(dvs);
        };

        ajaxCall(url, "json", loadPrdtPrice.data, callback);
    }
};

/**
 * @brief 화면에 출력되는 가격 및 빠른견적서 내용 수정
 */
var calcPrice = function(dvs) {
    // 부가세율
    //var taxRate = 0.1;

    // 가격 정보
    var paperPrice  = parseInt($("#inner_paper_price").val());
    var printPrice  = parseInt($("#inner_print_price").val());
    var outputPrice = parseInt($("#inner_output_price").val());

    // 가격
    var sellPrice = paperPrice +
                    printPrice +
                    outputPrice;

    // 회원등급 할인
    var gradeSale = parseFloat($("#grade_sale").attr("rate"));
    gradeSale /= 100.0;

    // 제본 가격
    var bindingPrice = $("#inner_binding_price").val();

    if (checkBlank(bindingPrice) === true) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : "제본",
        };

        getAfterPrice.load("binding", data, dvs);
        return false;
    }

    bindingPrice = parseInt(bindingPrice);

    // 옵션비 총합
    var optDefaultPrice = parseInt($("#opt_default_price").attr("price"));
    var sumOptPrice = getSumOptPrice();
    sumOptPrice += optDefaultPrice;
    sumOptPrice  = ceilVal(sumOptPrice);

    // 후공정비 총합
    var sumAfterPrice = getSumAfterPrice("inner");
    sumAfterPrice += bindingPrice;
    sumAfterPrice  = ceilVal(sumAfterPrice);

    // 견적서 종이비 계산
    var paper = parseInt(paperPrice);

    // 견적서 인쇄비 계산
    var print = ceilVal(parseInt(printPrice));

    // 견적서 출력비 계산
    var output = ceilVal(parseInt(outputPrice));

    // 정상 판매가 계산
    sellPrice += sumAfterPrice + sumOptPrice;

    // 부가세 포함가격 계산
    //var tax = sellPrice * taxRate;
    var calcSellPrice = sellPrice;

    // 회원등급 할인가 계산
    var calcGradeSale = calcSellPrice * gradeSale;
    calcGradeSale = ceilVal(calcGradeSale);
    var salePrice = calcSellPrice - calcGradeSale;

    // 정상 판매가 변경
    $("#sell_price").attr("val", sellPrice);
    $("#sell_price").html(calcSellPrice.format() + "원");
    // 회원등급 할인가 변경
    $("#grade_sale").html(calcGradeSale.format() + "원");
    // 기본할인가 변경
    $("#sale_price").attr("val", salePrice);
    $("#sale_price").html(salePrice.format() + "원");

    // 견적서 종이비 변경
    $("#esti_paper").html(paper.format());
    // 견적서 출력비 변경
    $("#esti_output").html(output.format());
    // 견적서 인쇄비 변경
    $("#esti_print").html(print.format());
    // 견적서 후공정비 변경
    $("#esti_after").html(sumAfterPrice.format());
    // 견적서 옵션비 변경
    $("#esti_opt").html(sumOptPrice.format());
    // 견적서 합계 변경
    $("#esti_sum").html(sellPrice.format());
    // 견적서 부가세 변경
    //$("#esti_tax").html(tax.format());
    // 견적서 판매가 변경
    $("#esti_sell_price").html(calcSellPrice.format());
    // 견적서 기본할인가 변경
    $("#esti_sale_price").html(salePrice.format());
};

/**
 * @brief 페이지 변경시 종이수량 재계산을 위한 중간함수
 *
 * @param dvs = 바꾼 페이지 위치값
 */
var changePage = function(dvs) {
    getRealPaperAmt.exec(dvs);
    reCalcAfterPrice(dvs, null);
    getBindingCalcBookletPrice("binding", dvs);
    changeData(dvs);
};

/**
 * @brief 표지, 내지1/2/3 실제 종이인쇄수량 계산
 *
 * @param dvs = 바꾼 페이지 위치값
 */
var getRealPaperAmt = {
    "amt"  : {
        "inner" : 0,
    },
    "exec" : function(dvs) {
        var prefix = getPrefix(dvs);
        var $amtObj = $(prefix + "amt");
        var amt     = parseFloat($amtObj.val());
        var amtUnit = $amtObj.attr("amt_unit");
        var posNum  = $(prefix + "size").attr("pos_num");

        var info = {
           "amt"      : amt,
           "posNum"   : posNum,
           "amtUnit"  : amtUnit
        };

        var pageNum = $(prefix + "page").val();
        info.pageNum = pageNum;

        this.amt[dvs] = calcRealPaperAmt(info);
    }
};

/**
 * @brief 제본 미리보기 클릭시 팝업 출력
 */
var showBindingPop = function() {
    var url = "/ajax/product/load_preview_binding_pop.php";
    popupMask = layerPopup("l_preview_binding", url);
};

/**
 * @brief 장바구니로 이동
 */
var goCart = function() {
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

    var cateName  = $("#cate_bot").find("option:selected").text();
    var amtUnit   = $("#inner_amt").attr("amt_unit");
    var paperName = $("#inner_paper").find("option:selected").text();
    var tmptName  = $("#inner_bef_tmpt").find("option:selected").text();
    var sizeName  = $("#inner_size").find("option:selected").text();

    var paperPrice  = parseInt($("#inner_paper_price").val());
    var printPrice  = parseInt($("#inner_print_price").val());
    var outputPrice = parseInt($("#inner_output_price").val());

    var afterPrice    = getSumAfterPrice("inner");
    var bindingPrice  = parseInt($("#inner_binding_price").val());
    var optPrice      = getSumOptPrice();

    var gradeSaleRate = parseFloat($("#grade_sale").attr("rate"));

    var prdtPrice = paperPrice +
                    printPrice +
                    outputPrice +
                    afterPrice +
                    bindingPrice +
                    optPrice;
    var basicPrice = prdtPrice - ((gradeSaleRate / 100) * prdtPrice);

    var ret = makeAfterInfo.all();

    if (ret === false) {
        return false;
    }

    setAddOptInfo();

    $frm = $("#frm");

    $frm.find("input[name='inner_cate_name']").val(cateName);
    $frm.find("input[name='inner_amt_unit']").val(amtUnit);
    $frm.find("input[name='inner_paper_name']").val(paperName);
    $frm.find("input[name='inner_paper_amt']").val(getRealPaperAmt.amt.inner);
    $frm.find("input[name='inner_bef_tmpt_name']").val(tmptName);
    $frm.find("input[name='inner_size_name']").val(sizeName);

    $frm.find("input[name='inner_prdt_price']").val(prdtPrice);
    $frm.find("input[name='inner_basic_price']").val(basicPrice);
    $frm.find("input[name='inner_grade_sale_rate']").val(gradeSaleRate);

    $frm.find("input[name='inner_after_price']").val(afterPrice + bindingPrice);
    $frm.find("input[name='inner_opt_price']").val(optPrice);

    $frm.submit();
};

/******************************************************************************
 * 후공정 관련 함수
 ******************************************************************************/

var getAfterCalcBookletPrice = function(aftEn, dvs, aftKo) {
    var prefix = getPrefix(dvs);
    var mpcode = $(prefix + aftEn + "_val").val();
    var priceArr  = getAfterPrice.price[aftEn];

    // 가격정보가 없을경우
    if (checkBlank(priceArr)) {
        var data = {
            "cate_sortcode" : $("#cate_bot").val(),
            "after_name"    : aftKo,
        };

        getAfterPrice.load(aftEn, data, dvs);

        return false;
    }

    if (checkBlank(priceArr[mpcode])) {
        alert("해당 가격이 존재하지 않습니다.\n관리자에게 문의하세요.");
        return false;
    }

    priceArr = priceArr[mpcode];

    var sumPrice = getAfterSumPrice(priceArr, dvs);

    $(prefix + aftEn +"_price").val(sumPrice);

    setAfterPrice(dvs, aftEn, sumPrice);

    execCalcPrice(dvs);
};

/**
 * @brief 엠보싱의 가격을 검색한다
 * 가격 배열이 null일 경우 새로 검색한다
 *
 * @param dvs = 후공정 구분값
 */
var getEmbossingCalcBookletPrice = function(aft, dvs) {
    getAfterCalcBookletPrice(aft, dvs, "엠보싱");
};

/**
 * @brief 코팅의 가격을 검색한다
 * 가격 배열이 null일 경우 새로 검색한다
 *
 * @param dvs = 후공정 구분값
 */
var getCoatingCalcBookletPrice = function(aft, dvs) {
    getAfterCalcBookletPrice(aft, dvs, "코팅");
};

/**
 * @brief 제본의 가격을 검색한다
 * 가격 배열이 null일 경우 새로 검색한다
 *
 * @param dvs = 후공정 구분값
 */
var getBindingCalcBookletPrice = function(aft, dvs) {
    getAfterCalcBookletPrice(aft, dvs, "제본");
};

/**
 * @brief 종이 구성 변경 등으로 후공정 가격 재계산시
 * calcPrice() 함수 중복호출 되지 않도록 처리
 */
var execCalcPrice = function(dvs) {
    if (passFlag === true) {
        passFlag = false;
        return;
    }

    passFlag = false;
    calcPrice(dvs);

    return false;
}

/**
 * @brief 가격 배열에서 각 종이구분별로
 * 후공정 가격 계산해서 합산 후 반환
 *
 * @param priceArr = 가격 배열
 *
 * @return 합산된 가격
 */
var getAfterSumPrice = function(priceArr, dvs) {
    var prefix = getPrefix(dvs);
    var crtrUnit = priceArr.crtr_unit;
    var amtUnit  = $(prefix + "amt").attr("amt_unit");

    // 종이수량
    var paperAmtInner = getRealPaperAmt.amt.inner;
    paperAmtInner = amtCalc(paperAmtInner, amtUnit, crtrUnit);

    // 내지 제본 가격 계산
    var priceInner = calcAfterPrice(priceArr, paperAmtInner);

    var sumPrice = priceInner;

    return sumPrice;
};
