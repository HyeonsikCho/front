var tmptDvs   = null;
var monoYn    = null;
var prdtDvs   = null;
var affil     = null;
var sortcode  = null;
var amtUnit   = null;
var cateName  = null;

$(document).ready(function() {
    monoYn   = $("#mt_ncr_mono_yn").val();
    tmptDvs  = $("#mt_ncr_tmpt_dvs").val();
    prdtDvs  = $("#prdt_dvs").val();
    affil    = $("#mt_ncr_size").find("option:selected").attr("affil");
    sortcode = $("#mt_ncr_cate_sortcode").val();
    cateName = $("#mt_ncr_cate_sortcode").find("option:selected").text();
    amtUnit  = $("#mt_ncr_amt").attr("amt_unit");
	
	showUvDescriptor(prdtDvs);

    if (sortcode === "006001001") {
        $("#paperMid1_dt").remove();
        $("#paperMid1_dd").remove();
        $("#paperMid2_dt").remove();
        $("#paperMid2_dd").remove();
    } else if (sortcode === "006001002") {
        $("#paperMid2_dt").remove();
        $("#paperMid2_dd").remove();
    }
});

/**
 *
 */
var changePaper = function(paperDvs) {
    var paperMpcodeArr = [];

    if (paperDvs === 'T') {
        paperMpcodeArr.push($("#paperTop").val());
        loadPaperPreview(null, "#paperTop");
    } else if (paperDvs === "M1") {
        loadPaperPreview(null, "#paperMid1");
        paperMpcodeArr.push($("#paperMid1").val());
    } else if (paperDvs === "M2") {
        loadPaperPreview(null, "#paperMid2");
        paperMpcodeArr.push($("#paperMid2").val());
    } else if (paperDvs === 'B') {
        loadPaperPreview(null, "#paperBot");
        paperMpcodeArr.push($("#paperBot").val());
    }

    changeData(prdtDvs, paperMpcodeArr);
};

/**
 * @brief 계산형일 때 사이즈 선택할 경우 사이즈 계열에 맞는 도수값 검색
 *
 * @param val = 구분값
 */
var changeSize = {
    "exec" : function(dvs) {
        var prefix = getPrefix(dvs);
        changeAmtTxt();
    
        if (monoYn === '1') {
            var selectAffil =
                $(prefix + "size").find("option:selected").attr("affil");
            var aftInfoArr = [
                {"name" : "제본", "depth1" : "떡제본"},
                {"name" : "넘버링", "depth1" : "일반"}
            ];
            var size = $(prefix + "size > option:selected").text();

            loadAfterMpcode(dvs, aftInfoArr, size);
    
            if (affil === selectAffil) {
                changeData();
                return false;
            } else {
                affil = selectAffil;
            }
    
            var callback = function(result) {
                $(prefix + "print_tmpt").html(result.sheet_tmpt);
                changeMtTmpt($(prefix + "print_tmpt").val());
            };
        
            loadPrintTmptCommon.exec(dvs, callback);
        } else {
            changeData();
        }
    }
};

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 */
var changeData = function(dvs, paperMpcodeArr) {
    monoYn = $("#mt_ncr_mono_yn").val();

    var data ={
        "dvs"           : prdtDvs,
        "typ"           : $("#mt_ncr_typ").val(),
        "cate_sortcode" : sortcode,
        "amt"           : $("#mt_ncr_amt").val(),
        "stan_mpcode"   : $("#mt_ncr_size").val(),
        "affil"         : affil
    };


    if (checkBlank(paperMpcodeArr)) {
        paperMpcodeArr = [];

        paperMpcodeArr.push($("#paperTop").val());
        if ($("#paperMid1").length > 0) {
            paperMpcodeArr.push($("#paperMid1").val());
        } else if ($("#paperMid2").length > 0) {
            paperMpcodeArr.push($("#paperMid2").val());
        }
        paperMpcodeArr.push($("#paperBot").val());
    }

    data.paper_mpcode_arr     = paperMpcodeArr;
    data.bef_print_mpcode     = $("#mt_ncr_print_tmpt").val();
    data.bef_add_print_mpcode = '';
    data.aft_print_mpcode     = '';
    data.aft_add_print_mpcode = '';
    data.bef_print_name       = $("#mt_ncr_print_tmpt > option:selected").text();
    data.bef_add_print_name   = '';
    data.aft_print_name       = '';
    data.aft_add_print_name   = '';
    data.print_purp           = $("#mt_ncr_print_purp").val();
    data.page_info            = "2";
    data.flattyp_yn           = "N";
    data.amt_unit             = amtUnit;
    data.pos_num              = $("#mt_ncr_size > option:selected").attr("pos_num");
    data.diff_yn              = $("#diff_yn").prop("checked");

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
            url = "/ajax/product/load_ncr_calc_price.php";
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
    var count = 1;
    // 정상판매가
    var sellPrice = loadPrdtPrice.price.sell_price;
    if (checkBlank(sellPrice)) {
        changeData();
        return false;
    }
    sellPrice  = ceilVal(sellPrice);
    sellPrice *= count;
    // 등급 할인율
    var gradeSale = parseFloat($("#mt_ncr_grade_sale_rate").val());
    gradeSale /= 100.0;
    // 회원 할인율
    var memberSale = parseFloat($("#mt_ncr_member_sale_rate").val());
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

    // 정상판매가 변경(후공정, 옵션은 할인하지 않는다)
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
 * @brief 비규격 사이즈 선택할 경우 재단사이즈 값 초기화
 *
 * @param val  = 구분값
 * @param flag = 가격검색여부
 */
var changeSizeDvs = function(val, flag) {
    var prefix = getPrefix(prdtDvs);
    $(prefix + "similar_size").attr("divide", '1');

    // 비규격 사이즈 선택시 기본 사이즈로 데이터 변경
    if (val === "manu") {
        var str = $(prefix + "size > option:selected").text() + " 1/1 등분";

        $(prefix + "similar_size").show();
        $(prefix + "similar_size").html(str);
    } else {
        changeAmtTxt();
        $(prefix + "similar_size").hide();
    }

    changeData();
};

/**
 * @param 인쇄방식에 해당하는 인쇄도수 검색
 *
 * @param val = 인쇄방식
 */
var loadPrintTmpt = function(dvs) {
    var callback = function(result) {
        $("#mt_ncr_print_tmpt").html(result.bef_tmpt);

        if (monoYn === '1') {
            changeData();
        }
    };

    loadPrintTmptCommon.exec(dvs, callback);
};

/**
 * @brief 수량변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeAmt = function() {
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

    var prefix = getPrefix(prdtDvs);

    if (checkBlank($("#title").val().trim()) === true) {
        $("#cart_flag").val('Y');
        alert("인쇄물제목을  입력해주세요.");
        $("#title").focus();
        return false;
    }

    if (checkTmptChk()) {
        return alertReturnFalse("인쇄 색상을 선택해주세요.");
    }

    var amt = parseFloat($(prefix + "amt").val());
    var divide = parseFloat($(prefix + "similar_size").attr("divide"));

    var paperName = makePaperName();
    var sizeName  = $(prefix + "size > option:selected").text();
    var posNum    = $(prefix + "size > option:selected").attr("pos_num");

    var sellPrice  = $("#sell_price").attr("val");
    var salePrice  = $("#sale_price").attr("val");
    var afterPrice = getSumAfterPrice(prdtDvs);
    var optPrice   = getSumOptPrice();
    var tmptName   = makeTmptName();

    var orderDetail   = cateName + " / " +
                        paperName + " / " +
                        sizeName + " / " +
                        tmptName + " / " +
                        $(prefix + "binding_val > option:selected").text();

    if (!checkBlank($("#binding_typ").val())) {
        orderDetail += " / " + $("#binding_typ").val()
    }
    if ($("#diff_yn").prop("checked")) {
        orderDetail += " / 내용 다름";
    } else {
        orderDetail += " / 내용 같음";
    }

    var ret = makeAfterInfo.all(prdtDvs);

    if (ret === false) {
        return false;
    }

    setAddOptInfo();

    $("#order_detail").val(orderDetail);
    $("#mt_ncr_tmpt_name").val(tmptName);

    $frm = $("#frm");

    $frm.find("input[name='mt_ncr_amt']").val(amt * divide);
    $frm.find("input[name='mt_ncr_cate_name']").val(cateName);
    $frm.find("input[name='mt_ncr_amt_unit']").val(amtUnit);
    $frm.find("input[name='mt_ncr_paper_name']").val(paperName);
    $frm.find("input[name='mt_ncr_basisweight']").val(getPaperBasisweight());
    $frm.find("input[name='mt_ncr_bef_tmpt_name']").val($("#mt_ncr_print_tmpt")
                                                   .find("option:selected")
                                                   .text());
    $frm.find("input[name='mt_ncr_size_name']").val(sizeName);
    $frm.find("input[name='mt_ncr_pos_num']").val(posNum);

    $frm.find("input[name='mt_ncr_sell_price']").val(sellPrice);
    $frm.find("input[name='mt_ncr_sale_price']").val(salePrice);
    $frm.find("input[name='mt_ncr_after_price']").val(afterPrice);
    $frm.find("input[name='opt_price']").val(optPrice);
};

/**
 * @brief 인쇄도수 변경시 체크박스 체크해제
 */
var changeMtTmpt = function(val) {
    if (chkTmptSide()) {
        $("#binding_typ").prop("disabled", true);
        $("#binding_typ").html('<option value="">인쇄방향</option>');
    } else {
        $("#binding_typ").prop("disabled", false);
        changeBinding($("#mt_ncr_binding_val > option:selected").text());
    }

    $("input[name='mt_ncr_tmpt_chk']").prop("checked", false);
    $("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
    changeTmpt(prdtDvs, val);
};

/**
 * @brief 도수에 따라 체크박스 disabled 처리
 */
var chkTmptLim = function(pos) {
    var tmpt = $("#mt_ncr_print_tmpt").find("option:selected").text();
    var length = $("input[name='mt_ncr_tmpt_chk']:checked").length;

    if (tmpt === "단면1도") {
        if (length > 0) {
            $("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
        } else {
            $("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
        }
    } else if (tmpt === "단면2도") {
        if (length === 1) {
            // 선택한 면만 선택되도록 수정
            // 첫 번째 클릭했을 때
            if (pos === "bef") {
                $("#bef").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
                $("#aft").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
            } else {
                $("#bef").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
                $("#aft").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
            }
        } else if (length === 2) {
            // 두 번째 클릭했을 때
            $("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
        } else {
            $("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
        }
    } else if (tmpt === "양면2도") {
        var befLength = $("#bef").find("input[name='mt_ncr_tmpt_chk']:checked")
                                 .length;
        var aftLength = $("#aft").find("input[name='mt_ncr_tmpt_chk']:checked")
                                 .length;
        // 선택한 면은 disabled
        if (length === 1) {
            if (befLength === 1) {
                $("#bef").find("input[name='mt_ncr_tmpt_chk']")
                         .prop("disabled", true);
                $("#aft").find("input[name='mt_ncr_tmpt_chk']")
                         .prop("disabled", false);
            } else if (aftLength === 1) {
                $("#bef").find("input[name='mt_ncr_tmpt_chk']")
                         .prop("disabled", false);
                $("#aft").find("input[name='mt_ncr_tmpt_chk']")
                         .prop("disabled", true);
            }
        } else if (length === 2) {
            $("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
        } else {
            // 전체 체크 해제
            $("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
        }
    } else if (tmpt === "단면3도") {
        if (0 < length && length < 3) {
            // 선택한 면만 선택되도록 수정
            // 첫 번째 클릭했을 때
            if (pos === "bef") {
                $("#bef").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
                $("#aft").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
            } else {
                $("#bef").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
                $("#aft").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
            }
        } else if (length === 3) {
            // 세 번째 클릭했을 때
            $("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
        } else {
            // 전체 체크 해제
            $("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
        }
    } else if (tmpt === "전면2도+후면1도") {
        var befLength = $("#bef").find("input[name='mt_ncr_tmpt_chk']:checked").length;
        var aftLength = $("#aft").find("input[name='mt_ncr_tmpt_chk']:checked").length;

        if (pos === "bef") {
            if (befLength === 2) {
                $("#bef").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
            } else {
                $("#bef").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
            }
	} else {
            if (aftLength === 1) {
                $("#aft").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
            } else {
                $("#aft").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
            }
        }
    } else if (tmpt === "전면1도+후면2도") {
        var befLength = $("#bef").find("input[name='mt_ncr_tmpt_chk']:checked").length;
        var aftLength = $("#aft").find("input[name='mt_ncr_tmpt_chk']:checked").length;

        if (pos === "aft") {
            if (aftLength === 2) {
                $("#aft").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
            } else {
                $("#aft").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
            }
        } else {
            if (befLength === 1) {
                $("#bef").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", true);
            } else {
                $("#bef").find("input[name='mt_ncr_tmpt_chk']").prop("disabled", false);
            }
        }
    }

    $("input[name='mt_ncr_tmpt_chk']:checked").prop("disabled", false);
};

/**
 * @brief 인쇄도수 체크박스 검사
 */
var checkTmptChk = function() {
    var tmpt = $("#mt_ncr_print_tmpt").find("option:selected").text();
    var length = $("input[name='mt_ncr_tmpt_chk']:checked").length;

    if (tmpt === "단면1도") {
        if (length === 0) {
            return true;
        }
    } else if (tmpt === "단면2도" || tmpt === "양면2도") {
        if (length < 2) {
            return true;
        }
    } else if (tmpt === "단면3도" || tmpt === "전면2도+후면1도" || tmpt === "전면1도+후면2도") {
        if (length < 3) {
            return true;
        }
    }
};

/**
 * @brief 제본 변경시 제본 배치 변경
 *
 * @param val = 제본값
 */
var changeBinding = function(val) {
    if (chkTmptSide()) {
        getAfterPrice.common('binding', 'mt_ncr');
        return false;
    }

    $("#not_binding").remove();
    $("#binding_typ").html('<option value="머리+꼬리">머리+꼬리</option><option value="머리+머리">머리+머리</option>');

    if (val.indexOf("상철") > -1) {
        $("#binding_typ").val("머리+꼬리");
    } else if (val.indexOf("좌철") > -1) {
        $("#binding_typ").val("머리+머리");
    } else {
        $("#binding_typ").html('<option value="">인쇄방향</option>');
	return false;
    }

    getAfterPrice.common('binding', 'mt_ncr');
};

/**
 * @brief 도수명으로 단면/양면 구분
 *
 * @return 단면 : true / 양면 false
 */
var chkTmptSide = function() {
    var tmpt = $("#mt_ncr_print_tmpt").find("option:selected").text();
    return (tmpt.indexOf("단면") > -1) ? true : false;
};

/**
 * @brief 상하지 내용따라 출력비 재계산
 *
 * @param obj = 체크박스 객체
 */
var changeDiff = function(obj) {
    if (monoYn === '1') {
        changeData();
    }
};

/**
 * @brief 인쇄도수명 생성
 *
 * @return 인쇄도수명
 */
var makeTmptName = function() {
    var ret = $("#mt_ncr_print_tmpt").find("option:selected").text() + " - ";

    var bef = "전면 : ";
    $("#bef").find("input[name='mt_ncr_tmpt_chk']:checked").each(function() {
        bef += $(this).val();
        bef += ' ';
    });
    bef = bef.substr(0, bef.length - 1);

    var aft = "후면 : ";
    $("#aft").find("input[name='mt_ncr_tmpt_chk']:checked").each(function() {
        aft += $(this).val();
        aft += ' ';
    });
    aft = aft.substr(0, aft.length - 1);

    ret += bef + ", " + aft;

    return ret;
};

/**
 * @brief 종이명 생성
 *
 * @return 종이명
 */
var makePaperName = function() {
    var top = $("#paperTop").find("option:selected").text().split(' ');
    var bot = $("#paperBot").find("option:selected").text().split(' ');

    var ret = "상 : " + top[2] + ' ' + top[3];

    if ($("#paperMid1").length > 0) {
        var mid1 = $("#paperTop").find("option:selected").text().split(' ');
        ret += ", 중 : " + mid1[2] + ' ' + mid1[3];
    }
    if ($("#paperMid2").length > 0) {
        var mid2 =  $("#paperTop").find("option:selected").text().split(' ');
        ret += ", 중 : " + mid2[2] + ' ' + mid2[3];
    }
    ret += ", 하 : " +  bot[2] + ' ' + bot[3];

    return ret;
};

/**
 * @brief 비규격 등분에 맞춰서 상품 실수량 표기
 */
var changeAmtTxt = function() {
    var prefix = getPrefix(prdtDvs);
    var divide = parseFloat($(prefix + "similar_size").attr("divide"));

    $(prefix + "amt > option").each(function() {
        var amt = parseFloat($(this).val()) * divide;
        var txt = amt + ' ' + amtUnit;

        $(this).text(txt);
    });
};

/**
 * @brief 종이 평량 문자열 생성
 *
 * @return 평량 문자열
 */
var getPaperBasisweight = function() {
    var ret = '';
    var temp = $("#paperTop > option:selected").text().split(' ');
    ret += temp[temp.length - 1];
    if ($("#paperMid1").length > 0) {
        temp = $("#paperMid1 > option:selected").text().split(' ');
        ret += '|' + temp[temp.length - 1];
    }
    if ($("#paperMid2").length > 0) {
        temp = $("#paperMid2 > option:selected").text().split(' ');
        ret += '|' + temp[temp.length - 1];
    }
    temp = $("#paperBot > option:selected").text().split(' ');
    ret += '|' + temp[temp.length - 1];

    return ret;
};

/**
 * @brief 견적서 팝업 본문정보 생성
 */
var makeEstiPopInfo = {
    "data" : null,
    "exec" : function(dvs) {
        var prefix = getPrefix(prdtDvs);
    
        var paper = makePaperName();
        var size  = $.trim($(prefix + "size > option:selected").text());
        var tmpt  = makeTmptName();
        var amt   = $.trim($(prefix + "amt").val());
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
