var monoYn    = null;
var prdtDvs   = null;
var affil     = null;
var sortcode  = null;
var amtUnit   = null;
var cateName  = null;

$(document).ready(function() {
    // 건수 초기화
    initCount(99, "count");

    monoYn   = $("#ad_mono_yn").val();
    prdtDvs  = $("#prdt_dvs").val();
    affil    = $("#ad_size").find("option:selected").attr("affil");
    sortcode = $("#ad_cate_sortcode").val();
    cateName = $("#ad_cate_sortcode").find("option:selected").text();
    amtUnit  = $("#ad_amt").attr("amt_unit");
	
	showUvDescriptor(prdtDvs);

    if (amtUnit === 'R') {
        $("#sheet_count_div").show();
        calcSheetCount(prdtDvs);
    } else {
        $("#r_count_div").show();
        calcRCount(prdtDvs);
    }

    if (sortcode === "004003007"/* || sortcode === "004003011"*/) {
        changeSizeTyp();

        var stanName = $("#ad_size_typ > option:selected").text();
        stanName = stanName.split(' ')[0];
        changePreviewImg(stanName);
    } else if (sortcode === "004003006") {
        var stanName = $("#ad_size > option:selected").text();
        stanName = stanName.split('[')[0];
        changePreviewImg(stanName);
    } else if (sortcode === "004003008") {
        changeSizeTyp();

        var stanName = $("#ad_size > option:selected").text();
        stanName = stanName.split(' ')[0].replace('(', '').replace(')', '');
        changePreviewImg(stanName);
    }
});

/**
 * @brief 종이 바뀔 때 평량에 따라 규격 변경
 *
 * @param dvs       = 제품구분값
 * @param val       = 종이맵핑코드
 * @param sizeTypYn = 사이즈 타입명 노출 여부
 */
var changePaper = function(dvs, val, sizeTypYn) {
    var prefix = getPrefix(dvs);
    var affil  = $("#ad_size > option:selected").attr("affil");
    var posNum = $("#ad_size > option:selected").attr("pos_num");

    var url = "/ajax/product/load_paper_size.php";
    var data = {
        "cate_sortcode" : sortcode,
        "mono_yn"       : $(prefix + "mono_yn").val(),
        "affil_yn"      : affil,
        "pos_yn"        : posNum,
        "size_typ_yn"   : sizeTypYn,
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
 */
var loadPrdtAmt = function(dvs) {
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
            calcRCount(dvs);
        }

        var aftInfoArr = getAftInfoArr(dvs);
        var size = $(prefix + "size > option:selected").text();
        loadAfterMpcode(dvs, aftInfoArr, size);

        rangeBarBySelect();
        changeData();
    };

    ajaxCall(url, "html", data, callback);
};

/**
 * @brief 계산형일 때 사이즈 선택할 경우 사이즈 계열에 맞는 도수값 검색
 *
 * @param val = 구분값
 */
var changeSize = {
    "holder" : function(dvs) {
        var prefix   = getPrefix(dvs);
        var stanName = $(prefix + "size > option:selected").text();
        stanName = stanName.split('[')[0];
        changePreviewImg(stanName);

        changeSize.exec(dvs);
    },
    "door" : function(dvs) {
        var prefix   = getPrefix(dvs);
        var stanName = $(preifx + "size > option:selected").text();
        stanName = stanName.split(' ')[0].replace('(', '').replace(')', '');
        changePreviewImg(stanName);

        changeSize.exec(dvs);
    },
    "exec" : function(dvs) {
        reCalcAfterPrice(dvs, null);
        loadPrdtAmt(dvs);
    }
};

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 */
var changeData = function() {
    monoYn = $("#ad_mono_yn").val();

    var data ={
        "dvs"           : prdtDvs,
        "mono_yn"       : monoYn,
        "cate_sortcode" : sortcode,
        "amt"           : $("#ad_amt").val(),
        "stan_mpcode"   : $("#ad_size").val()
    };

    data.paper_mpcode       = $("#ad_paper").val();
    data.bef_print_mpcode     = $("#ad_print_tmpt").val();
    data.bef_add_print_mpcode = '';
    data.aft_print_mpcode     = '';
    data.aft_add_print_mpcode = '';
    data.print_purp         = $("#ad_print_purp").val();
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
        changeData();
        return false;
    }
    sellPrice  = ceilVal(sellPrice);
    sellPrice *= count;
    // 등급 할인율
    var gradeSale = parseFloat($("#ad_grade_sale_rate").val());
    gradeSale /= 100.0;
    // 회원 할인율
    var memberSale = parseFloat($("#ad_member_sale_rate").val());
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
        $(".esti_paper_info").css("display", "");
    } else {
        $(".esti_paper_info").css("display", "none");
    }

    // 견적서 출력비 계산
    var output = 0;
    if (monoYn === '1') {
        output  = parseInt(loadPrdtPrice.price.output);
        output  = ceilVal(output);
        output *= count;
        $(".esti_output_info").css("display", "");
    } else {
        $(".esti_output_info").css("display", "none");
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
        $("#ad_print_tmpt").html(result.bef_tmpt);

        if (monoYn === '1') {
            changeData();
        }
    };

    loadPrintTmptCommon.exec(dvs, callback);
};

/**
 * @brief 비규격 사이즈 선택할 경우 재단사이즈 값 초기화
 *
 * @param val = 구분값
 */
var changeSizeDvs = function(val) {
    var prefix = getPrefix(prdtDvs);

    // 비규격 사이즈 선택시 기본 사이즈로 데이터 변경
    if (val === "manu") {
        var str = $(prefix + "size > option:selected").text() + " 1/1";

        $(prefix + "similar_size").show();
        $(prefix + "similar_size").html(str);
    } else {
        $(prefix + "similar_size").hide();
    }

    changeData();
};

/**
 * @brief 수량변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeAmt = function() {
    if (amtUnit === 'R') {
        calcSheetCount(prdtDvs);
    } else {
        calcRCount(prdtDvs);
    }
    reCalcAfterPrice(prdtDvs, null);
    changeData();
};

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

    var prefix = getPrefix(prdtDvs);

    var amtUnit   = $(prefix + "amt").attr("amt_unit");
    var paperName = $(prefix + "paper").find("option:selected").text();
    var tmptName  = $(prefix + "print_tmpt").find("option:selected").text();
    var sizeName  = $(prefix + "size").find("option:selected").text();

    var sellPrice     = $("#sell_price").attr("val");
    var salePrice     = $("#sale_price").attr("val");
    var afterPrice    = getSumAfterPrice(prdtDvs);
    var optPrice      = getSumOptPrice();

    var ret = makeAfterInfo.all(prdtDvs);

    if (ret === false) {
        return false;
    }

    setAddOptInfo();

    if ($(prefix + "paper_name").length > 0) {
        paperName = $(prefix + "paper_name").val() + ' ' + paperName;
    }

    if (sortcode === "004003007") {
        // 문어발
        var orderDetail = cateName + " / " +
                          paperName + " / " +
                          sizeName + " (" +
                          $("#ad_size_typ > option:selected").text() + ") / " +
                          tmptName;

        $("#ad_order_detail").val(orderDetail);
    } else if (sortcode === "004003009") {
        // 메모지
        var orderDetail = cateName + " / " +
                          paperName + " / " +
                          sizeName + " / " +
                          tmptName  + " / 제본매수 :  " +
                          $("#ad_binding_count").val() + " / " +
			  $("#ad_binding_val").val();

        $("#ad_order_detail").val(orderDetail);
    }

    $frm = $("#frm");

    $frm.find("input[name='ad_cate_name']").val(cateName);
    $frm.find("input[name='ad_amt_unit']").val(amtUnit);
    $frm.find("input[name='ad_paper_name']").val(paperName);
    $frm.find("input[name='ad_bef_tmpt_name']").val(tmptName);
    $frm.find("input[name='ad_size_name']").val(sizeName);

    $frm.find("input[name='ad_sell_price']").val(sellPrice);
    $frm.find("input[name='ad_sale_price']").val(salePrice);
    $frm.find("input[name='ad_after_price']").val(afterPrice);
    $frm.find("input[name='ad_opt_price']").val(optPrice);
};

/**
 * @brief 기획인쇄물 문어발에서 사이즈 변경시 제품유형 검색
 */
var loadSizeTyp = function() {
    var prefix = getPrefix(prdtDvs);

    var url = "/ajax/product/load_typ.php";
    var data = {
        "pos_yn"        : 'Y',
        "cate_sortcode" : sortcode,
        "stan_name"     : $(prefix + "size > option:selected").text()
    };
    var callback = function(result) {
        $(prefix + "size_typ").html(result);
        changeSizeTyp();
    };

    ajaxCall(url, "html", data, callback);
};

/**
 * @brief 문어발 제품유형 변경시 맵핑코드 사이즈로 입력
 */
var changeSizeTyp = function() {
    var prefix = getPrefix(prdtDvs);
    var val = $(prefix + "size_typ").val();

    $(prefix + "size > option:selected").attr("value", val);

    changeSize.exec(prdtDvs);

    var stanName = $(prefix + "size_typ > option:selected").text();
    stanName = stanName.split(' ')[0];
    changePreviewImg(stanName);
};

/**
 * @brief 건수변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeCount = function() {
    reCalcAfterPrice(prdtDvs, null);
    calcPrice();
};

/**
 * @brief 견적서 팝업 본문정보 생성
 */
var makeEstiPopInfo = {
    "data" : null,
    "exec" : function(dvs) {
        var prefix = getPrefix(prdtDvs);
    
        var paper = $.trim($(prefix + "paper > option:selected").text());
        var size  = $.trim($(prefix + "size > option:selected").text());
        var tmpt  = $.trim($(prefix + "print_tmpt > option:selected").text());
        var amt   = $.trim($(prefix + "amt").val());
        var count = $.trim($("#esti_count").text());
		
        if ($(prefix + "paper_name").length > 0) {
            paper = $(prefix + "paper_name").val() + ' ' + paper;
        }
    
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
