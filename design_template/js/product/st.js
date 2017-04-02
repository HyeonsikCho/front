var tmptDvs  = null;
var monoYn   = null;
var prdtDvs  = null;
var sortcode = null;
var cateName = null;
var amtUnit  = null;

$(document).ready(function() {
    // 건수 초기화
    initCount(99, "count");

    monoYn   = $("#st_mono_yn").val();
    tmptDvs  = $("#st_tmpt_dvs").val();
    prdtDvs  = $("#prdt_dvs").val();
    sortcode = $("#st_cate_sortcode").val();
    cateName = $("#st_cate_sortcode").find("option:selected").text();
    amtUnit  = $("#st_amt").attr("amt_unit");

    calcManuPosNum.defWid  = parseFloat($("#st_size").attr("def_cut_wid"));
    calcManuPosNum.defVert = parseFloat($("#st_size").attr("def_cut_vert"));
	
	showUvDescriptor(prdtDvs);

    if (sortcode === "002002010") {
        $(".esti_print_info").show();
        $("._cuttingSize input").val(10);
        $("._cuttingSize input").prop({"disabled" : false,
                                       "readonly" : false});

        getFreeTomsonPrice('', prdtDvs, getFreeTomsonParam());
    }

    /*
    if ($("#coating_yn").length > 0) {
        $("#coating_yn").on("change", function() {
            if ($(this).val() === "무코팅") {
                $(".basic_after[aft='코팅']").hide();
            } else {
                $(".basic_after[aft='코팅']").show();
            }
        });
    }
    */

    if (sortcode === "002001006") {
        $("input[name='st_bg']").prop("disabled", true);
        $($("input[name='st_bg']")[1]).prop("checked", true);
        $("#size_dvs").find("._custom").remove();
    }

    if (sortcode.substr(0, 6) === "002002") {
        changePreviewImg(getStanName());
    }

    if (sortcode === "002002010" || sortcode === "002001004") {
        chkCoatingYn(prdtDvs);
    }
});

/**
 * @brief 도무송 미리보기 이미지 변경용 규격명 반환
 */
var getStanName = function() {
    var prefix = getPrefix(prdtDvs);
    var stanName = $(prefix + "size > option:selected").text();

    if (sortcode === "002002008") {
        var cuttingSize = $(prefix + "size > option:selected").attr("class")
                                                              .split(' ')[1];
        cuttingSize = cuttingSize.replace("_cuttingWH", '').split('-');

        stanName = "vcc-" + cuttingSize[0] + cuttingSize[1];
    } else if (sortcode === "002002007") {
        stanName = stanName.split('-');
        stanName = "bohum" + '-' + stanName[1];
    }

    return stanName;
};

/**
 * @brief 종이 변경시 코팅여부 체크후 재질느낌 검색
 *
 * @param dvs = 제품구분값
 * @param val = 맵핑코드
 */
var changePaper = function(dvs, val) {
    loadPaperPreview(dvs);
    chkCoatingYn(dvs);
    loadPaperDscr.exec(dvs, val);
};

/**
 * @brief 도무송 스티커일 때 사이즈 변경시 미리보기 이미지 변경
 *
 * @param obj = 자기자신 객체
 */
var changeSize = function(obj) {
    changePreviewImg(getStanName());
    changeData();
};

/**
 * @brief 코팅 사용여부 체크
 *
 * @param dvs = 제품구분
 */
var chkCoatingYn = function(dvs) {
    var prefix = getPrefix(dvs);
    var paperName = $(prefix + "paper > option:selected").text();

    var html = '';
    if (paperName.indexOf("아트지") > -1) {
        html = "<option value=\"코팅\">코팅</option><option value=\"무코팅\">무코팅</option>";
        $("#coating_yn").html(html);
        $("#coating_yn").prop("readonly", false);
    } else if (paperName.indexOf("데드롱") > -1) {
        html = "<option value=\"코팅\">코팅</option>";
        $("#coating_yn").html(html);
        $("#coating_yn").prop("readonly", true);
    } else {
        html = "<option value=\"무코팅\">무코팅</option>";
        $("#coating_yn").html(html);
        $("#coating_yn").prop("readonly", true);
    }

    $("#coating_yn").trigger("change");
};

/**
 * @brief 자유형 도무송 계산횽 파라미터 생성
 *
 * @return 파라미터
 */
var getFreeTomsonParam = function() {
    var prefix   = getPrefix(prdtDvs);

    return {
        "stanName" : $(prefix + "size > option:selected").text(),
        "amt"      : $(prefix + "amt").val(),
        "wid"      : parseInt($(prefix + "cut_wid_size").val()),
        "vert"     : parseInt($(prefix + "cut_vert_size").val()),
        "callback" : freeTomsonCallback
    };
};

/**
 * @brief 자유형 도무송 재단사이즈 변경시 도무송 가격 재검색
 */
var changeTomsonSize = function() {
    chkMaxMinSize.exec(prdtDvs);
    getFreeTomsonPrice('', prdtDvs, getFreeTomsonParam());
};

/**
 * @brief 가격 구성요소 셀렉트박스 변경시 변경된 정보로 가격 검색
 */
var changeData = function() {
    if (sortcode === "002002010") {
        getFreeTomsonPrice('', prdtDvs, getFreeTomsonParam());
        return false;
    }

    monoYn = $("#st_mono_yn").val();

    var data ={
        "dvs"           : prdtDvs,
        "mono_yn"       : monoYn,
        "cate_sortcode" : sortcode,
        "amt"           : $("#st_amt").val(),
        "stan_mpcode"   : $("#st_size").val(),
        "tmpt_dvs"      : tmptDvs
    };

    data.flag = $("#frm").find("input[name='flag']").val();
    data.paper_mpcode       = $("#st_paper").val();
    data.bef_print_mpcode     = $("#st_print_tmpt").val();
    data.bef_add_print_mpcode = '';
    data.aft_print_mpcode     = '';
    data.aft_add_print_mpcode = '';
    data.print_purp         = $("#st_print_purp").val();
    data.page_info          = "2";

    if ($("#size_dvs").val() === "manu") {
        data.stan_mpcode   =
            $("#st_size").attr("def_val");
        data.def_stan_name =
            $("#st_size > option[value='" + data.stan_mpcode + "']").html();
        data.manu_pos_num  =
            $("#manu_pos_num").val();
        data.amt_unit      =
            $("#st_amt").attr("amt_unit");
    }

    loadPrdtPrice.data = data;
    loadPrdtPrice.exec();

    setSizeWarning();
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
    // 자리수
    var posNum = 1;
    if ($("#size_dvs").val() === "manu") {
        posNum = parseFloat($("#st_manu_pos_num").val());
    }
    // 건수
    var count = parseInt($("#count").val());
    // 정상판매가
    var sellPrice  = loadPrdtPrice.price.sell_price;
    if (checkBlank(sellPrice)) {
        changeData();
        return false;
    }
    sellPrice  = ceilVal(sellPrice);
    sellPrice *= posNum;
    sellPrice *= count;
    // 등급 할인율
    var gradeSale = parseFloat($("#st_grade_sale_rate").val());
    gradeSale /= 100.0;
    // 회원 할인율
    var memberSale = parseFloat($("#st_member_sale_rate").val());
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
        $("#st_print_tmpt").html(result.bef_tmpt);

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
    // 비규격 사이즈 선택시 기본 사이즈로 데이터 변경
    if (val === "manu") {
        $("#st_manu_pos_num").val('1');
        $("#cut_wid_size").val($("#st_size").attr("def_cut_wid"));
        $("#cut_vert_size").val($("#st_size").attr("def_cut_vert"));
    }

    changeData();
};

/**
 * @brief 수량변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeAmt = function() {
    reCalcAfterPrice(prdtDvs, null);
    changeData();
}

/**
 * @brief 건수변경시 후공정 가격 재계산 및 상품가격 재검색
 */
var changeCount = function() {
    reCalcAfterPrice(prdtDvs, null);
    calcPrice();
}

/**
 * @brief 가격 배열에서 후공정 가격 계산해서 반환
 *
 * @param priceArr = 가격 배열
 *
 * @return 계산된 가격
 */
var getAfterCalcPrice = function(priceArr) {
    var crtrUnit = priceArr.crtr_unit;
    var amt      = parseInt($("#st_amt").val());

    // 표지 종이수량
    amt = amtCalc(amt, amtUnit, crtrUnit);

    return calcAfterPrice(priceArr, amt);
};

/**
 * @brief 관심상품 등록이나 장바구니 전에 데이터 세팅
 */
var setSubmitParam = function() {
    if ($("#il").val() === "0") {
        $("#cart_flag").val('Y');
        return alertReturnFalse("로그인 후 확인 가능합니다.");
    }

    if (checkBlank($("#title").val().trim()) === true) {
        $("#cart_flag").val('Y');
        $("#title").focus();
        return alertReturnFalse("인쇄물 제목을 입력해주세요.");
    }

    if (!aftRestrict.all(prdtDvs)) {
        return false;
    }

    var paperName = $("#st_paper").find("option:selected").text();
    var tmptName  = $("#st_print_tmpt").find("option:selected").text();
    var sizeName  = $("#st_size").find("option:selected").text();

    var sellPrice     = $("#sell_price").attr("val");
    var salePrice     = $("#sale_price").attr("val");
    var afterPrice    = getSumAfterPrice(prdtDvs);
    var optPrice      = getSumOptPrice();
    var gradeSaleRate = $("#st_member_sale_rate").val();

    var ret = makeAfterInfo.all(prdtDvs);

    var sizeDvs = $("#size_dvs").val();
    if (sizeDvs === "manu") {
        sizeName = "비규격";
    }


    var orderDetail = cateName + " / " +
                      paperName + " / " +
                      sizeName + " / " +
                      tmptName;

    if ($("input[name='st_bg']").length > 0) {
        var bg = $("input[name='st_bg']:checked").val();
        bg = (bg === "원터치재단") ? "없음(원터치)" : "있음(투터치)";

        orderDetail += " / 빼다 : " + bg;
    }

    if ($("#coating_yn").length > 0) {
        orderDetail += " / " + $("#coating_yn").val();
    }

    $("#order_detail").val(orderDetail);

    if (ret === false) {
        return false;
    }

    setAddOptInfo();

    $frm = $("#frm");

    $frm.find("input[name='st_cate_name']").val(cateName);
    $frm.find("input[name='st_amt_unit']").val(amtUnit);
    $frm.find("input[name='st_paper_name']").val(paperName);
    $frm.find("input[name='st_bef_tmpt_name']").val(tmptName);
    $frm.find("input[name='st_size_name']").val(sizeName);

    $frm.find("input[name='st_sell_price']").val(sellPrice);
    $frm.find("input[name='st_sale_price']").val(salePrice);
    $frm.find("input[name='st_after_price']").val(afterPrice);
    $frm.find("input[name='opt_price']").val(optPrice);
};

/**
 * @brief 원터치/투터치 변경시 사이즈 재변경
 */
var loadSizeInfo = function(dvs, val) {
    var prefix = getPrefix(dvs);

    var url = "/ajax/product/load_typ_size.php";
    var data = {
        "cate_sortcode" : sortcode,
        "size_name"     : $("#st_size").find("option:selected").text(),
        "size_typ"      : $("input[name='st_bg']:checked").val()
    };
    var callback = function(result) {
        var cls = "_workingSize _gap" + result.gap;

        $("#st_size").html(result.html);
        $("#size_gap").attr("class", cls);
        size();
        changeData();
    };

    ajaxCall(url, "json", data, callback);
};

/**
 * @brief 자유형 도무송 가격 계산
 */
var freeTomsonCallback = function(result) {
    loadPrdtPrice.price.sell_price = result;
    size();
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

        if ($("input[name='st_bg']").length > 0) {
            var bg = $("input[name='st_bg']:checked").val();
            tmpt += ' ' + bg;
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

var setSizeWarning = function() {
    var prefix = getPrefix(prdtDvs);
    var size = $(prefix + "size > option:selected").text();
    if(size == "60*40" || size == "70*40") {
        $("#st_warning").text("* 5cm이하 제작물은 후지 반칼이 걸리지 않을 수 있으며 가급적 도무송으로 주문하시면 편리합니다.");
    } else {
        $("#st_warning").text("");
    }
};
