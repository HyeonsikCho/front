/*
 *
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 
 *============================================================================
 *
 */
var popupMask = null;

//새로고침 F5 막기
//document.onkeydown = processKey;
/*
function processKey() { 
    if((event.ctrlKey == true &&
          (event.keyCode == 78 || event.keyCode == 82)) ||
          (event.keyCode >= 112 && event.keyCode <= 123) ||
          (event.keycode==8)) {
        event.keyCode = 0; 
        event.cancelBubble = true; 
        event.returnValue = false; 
    }
}
*/

// html escape 대상 배열
var entityMap = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    '"': '&quot;',
    "'": '&#39;',
    "/": '&#x2F;'
};

/**
 * @brief 문자열에 들어있는 escape대상 문자 변환
 *
 * @param string = 대상 문자열
 */
function escapeHtml(str) {
    return String(str).replace(/[&<>"'\/]/g, function(s) {
       return entityMap[s];
    });
}

// 숫자 타입에서 쓸 수 있도록 format() 함수 추가
Number.prototype.format = function(){
    if(this==0) return 0;

    var reg = /(^[+-]?\d+)(\d{3})/;
    var n = (this + '');

    while (reg.test(n)) n = n.replace(reg, '$1' + ',' + '$2');

    return n;
};

// 문자열 타입에서 쓸 수 있도록 format() 함수 추가
String.prototype.format = function(){
    var num = parseFloat(this);
    if( isNaN(num) ) return "0";

    return num.format();
};


//어떤 값이 공백값이거나 undefined 값이면 false 반환
var checkBlank = function(val) {
   if (val === ""
           || val === ''
           || val === null
           || typeof val === "undefined") {
       return true;
   } else {
       return false;
   }
};

var isFunc = function(funcName) {
   if (typeof(window[funcName]) === "function") {
       return true;
   } else {
       return false;
   }
};

// Ajax Call 공통 함수
// 사용 예제 ajaxCall('호출주소', 'html', {data:data}, callback);
var ajaxCall  = function(url, dataType, data, sucCallback) {
    if (checkBlank(url) === true) {
        return false;
    }

    $.ajax({
        type     : "POST",
        url      : url,
        dataType : dataType,
        data     : data,
        success  : function(result) {
            hideMask(); 
            return sucCallback(result);
        },
        error    : getAjaxError 
    });
};

//Ajax error 공통 함수
var getAjaxError = function(request,status,error) {
    alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
    console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
    hideBgMask();
    hideMask();
};

//로딩 중 이미지 보이기
var showMask = function() {
    showBgMask();

    $obj = $("#loading_img");
    $obj.css("position","absolute");
    $obj.css("top", Math.max(0, (($(window).height() - $obj.height()) / 2) + $(window).scrollTop()) + "px");
    $obj.css("left", Math.max(0, (($(window).width() - $obj.width()) / 2) + $(window).scrollLeft()) + "px");
    $("#loading_img").show();
}

//로딩 중 이미지 숨기기
var hideMask = function() { 
    $("#loading_img").hide() 
    hideBgMask();
}

//Background 마스크 show
var showBgMask = function() {
    var maskHeight = $(document).height();  
    var maskWidth = $(window).width();  

    //마스크의 높이와 너비를 화면 것으로 만들어 전체 화면을 채운다.
    $("#black_mask").css({'width':maskWidth,'height':maskHeight}); 
    $("#black_mask").show();
}

//Background 마스크 hide 
var hideBgMask = function() {
    $("#black_mask").hide();
}

/**
 * 로그인 처리함수
 */
var login = function(el) {

    if (checkBlank($("#" + el + "id").val())) {
        location.href = "/member/join_1.html";
        //alert("아이디를 입력 해주세요.");
        //$("#" + el + "id").focus();
        return false;
    }

    if (checkBlank($("#" + el + "pw").val())) {
        alert("비밀번호를 입력 해주세요.");
        $("#" + el + "pw").focus();
        return false;
    }

    var url = "/common/login.php";
    var data = {
        "id" : $("#" + el + "id").val(),
        "pw" : $("#" + el + "pw").val()
    };

    var save_yn = "N";
    if ($("input:checkbox[id='id_save']").is(":checked")) {
        save_yn = "Y";
    }
    
    data.id_save = save_yn;

    var callback = function(result) {
        console.log(result);
        if (result.success === false) {
            alert("로그인에 실패했습니다.");
            location.href = "/member/login.html";
            return false;
        } else {
            location.href = result.ref;
        }
    };

    ajaxCall(url, "json", data, callback);
};

/**
 * @brief 로그인
 */
var loginKey = function(event, el) {

    if (event.keyCode == 13) {
        login(el);
    }
}

/**
 * @brief 암호 입력란으로 이동
 */
var idkey = function(event, el) {
    if (event.keyCode == 13) {
        $("#" + el + "pw").focus();
    }
}

/**
 * @brief 로그아웃 처리함수
 */
var logout = function() {
    location.href = "/common/logout.php";
};

/**
 * @brief 주문 요약정보 가져옴
 *
 * @param dvs = 1주일, 해당월 구분
 */
var getOrderSummary = function(dvs) {
    var url = "/json/common/load_order_summary.php";
    var data = {
        "dvs" : dvs
    };
    var callback = function(result) {
        if (checkBlank(result.err) === false) {
            alert("로그아웃되서 메인화면으로 이동합니다.");
            location.href = "/common/logout.php";
        }

        $("#summary_wait").html(result.wait);
        $("#summary_rcpt").html(result.rcpt);
        $("#summary_prdc").html(result.prdc);
        $("#summary_rels").html(result.rels);
        $("#summary_dlvr").html(result.dlvr);
        $("#summary_comp").html(result.comp);
    };

    ajaxCall(url, "json", data, callback);
};

//검색 날짜 범위 설정
var dateSet = function(num) {

    var day = new Date();
    var time = day.getHours();
    var d_day = new Date(day - (num * 1000 * 60 * 60 * 24));
    var last = new Date(day - (365 * 1000 * 60 * 60 * 24));

    //전체 범위 검색시 날짜 범위 초기화
    if (num == "last") {
        $("#from").datepicker("setDate", last);
        $("#to").datepicker("setDate", last);
    } else if (num == "all"){
        $("#from").val("");
        $("#to").val("");
    } else {
        $("#from").datepicker("setDate", d_day);
        $("#to").datepicker("setDate", '0');
    }
};

//인풋박스 숫자만 가능
var onlyNumber = function(event) {

    event = event || window.event;

    var keyID = (event.which) ? event.which : event.keyCode;
    if (keyID == 8 || keyID == 46 || keyID == 37 || keyID == 39) {
        return;
    } else {
        event.target.value = event.target.value.replace(/[^0-9]/g, "");
    }
};

var chkMaxLength = function(obj) {

    if (obj.value.length > obj.maxLength) {
        obj.value = obj.value.slice(0, obj.maxLength);
    }
}

/**
 * @brief 원단위 반올림
 *
 * @param val = 반올림할 값
 *
 * @return 계산된 값
 */
var ceilVal = function(val) {
    val = parseFloat(val);

    val = Math.round(val * 0.1) * 10;

    return val;
};

/**
 * @brief 전달받은 메세지 alert으로 띄우고 false값 반환
 *
 * @param str = 메세지
 *
 * @return false
 */
var alertReturnFalse = function(str) {
    alert(str);
    return false;
};

//중복체크버튼 보이기
var showBtn = function(el) {
    $(el).next().show().next().text('');
    $("#id_over_yn").val("N");
}

//아이디 중복체크
var getIdOver = function(el) {

    var id = $("#member_id").val();

    //아이디 유효성 검사
    var id_pattern = /^[a-z0-9_-]{8,20}$/;
    if (!id_pattern.test(id)) {
        alert("아이디가 올바르지 않습니다.");
        $("#member_id").focus();
        return false;
    }

    showMask();
    var url = "/ajax/common/load_id_over_check.php";
    var data = {
        "member_id" : $("#member_id").val()
    };
    var callback = function(result) {
        if (result == "true") {
            $(el).hide();
            $(el).next().addClass('ok').text('사용 가능한 아이디입니다.');
            $("#id_over_yn").val("Y");
        } else {
            $(el).next().removeClass('ok').text('이미 사용 중인 아이디 입니다.');
        }
    }

    ajaxCall(url, "html", data, callback);
}

/**
 * @brief 충전하기 버튼 클릭시 레이어 팝업 출력
 */
var showPrepaymentPop = function() {
    var url = "/ajax/common/load_prepayment_pop.php";
    popupMask = layerPopup("l_prepayment", url);
};

/*
 * 2016-06-23 김상기(추가)
 * 금액 입력창에 숫자, 백스페이스, 딜리트, 탭, F5, 좌우 방향키만
 * 입력 가능하고 우클릭 방지하는 함수
 * 인자를 받아서 허용 가능 키 추가 가능하도록 수정
 */
var numKeyCheck = function(id, arr) {
    //한글입력 불가능하도록 변경
    $("#" + id).css("ime-mode", "disabled");

    $("#"+ id).keydown(function(event) {
        var code = event.which;
        var shift = event.shiftKey;

        var refuseKeycode = [46, 8, 9, 37, 39, 116];
        var ret = false;

        if (arr) {
            refuseKeycode.push(arr);
        }

        if (shift) {
            return false;
        }

        //입력받은 키값을 검사
        if ((code > 47 && code < 58) || (code > 95 && code < 106)) {
            ret = true;
        }

        for (var i = 0; i < refuseKeycode.length; i++) {
            if (code == refuseKeycode[i]) {
                ret = true;
            }
        }

        return ret;
    });

    //입력창에 우클릭메뉴 방지
    $("#" + id).bind("contextmenu", function(e) {
        return false;
    });
}

/**
 * @brief 선입금 충전 팝업 출력
 */
var doCharge = function() {
    var ts = new Date();
    ts = ts.getTime();
    var chargePrice =
            $("input[type='radio'][name='charge_price']:checked").val();
    chargePrice = chargePrice.replace(/,/g, '');

    $("#P_EP_product_amt").val(chargePrice);
    $("#P_EP_pay_type").val($("#pay_type").val());
    $("#P_EP_order_no").val(ts);

    if (chargePrice === '0') {
        return alertReturnFalse("결제금액이 0원입니다.");
    };

    easypay_webpay(document.p_frm_pay,
                   "/webpay_card_prepay/web/normal/iframe_req.php",
                   "hiddenifr",
                   "0",
                   "0",
                   "iframe",
                   30);

    //getPrepaymentList(10, 1);
};

/**
 * @brief 선입금 승인요청 submit
 */
var prepaySubmit = function() {
    showBgMask();

    var frm = document.p_frm_pay;
    frm.target = "p_iframe_pay";
    frm.action = "/webpay_card_prepay/web/easypay_request.php";
    frm.submit();
};

/**
 * @brief 선입금 충전여부 판단
 */
var goCharge = function() {
    var chargePrice =
            $("input[type='radio'][name='charge_price']:checked").val();
    chargePrice = chargePrice.replace(/,/g, '');
    closePopup(popupMask);
    hideBgMask();

    var $obj = $("#p_iframe_pay").contents().find("body");
    if (checkBlank($obj) === true) {
        return alertReturnFalse("PG사 서버가 제대로 동작하지 않습니다.");
    }

    var resCd  = $obj.find("#res_cd").val();
    var amount = $obj.find("#amount").val();

    if (resCd !== "0000") {
        return alertReturnFalse($obj.find("#res_msg").val());
    }

    if (amount !== chargePrice) {
        return alertReturnFalse("결제 승인금액이 실제와 상이합니다.\n관리자에게 문의하세요.");
    }

    $("#side_prepay_price").html($obj.find("#prepay_bal").val().format());

    if (isFunc("getPrepaymentList") === true) {
        location.reload();
    }
};

//사이드메뉴 아코디언
var showAccordion = function(dvs) {
    if($("#" + dvs).css("display") == "none"){
        $("#myOrder").hide();
        $("#favorite").hide();
        $("#contact").hide();
        $("#" +  dvs).show("200");
    } else {
        $("#" +  dvs).hide();
    }
}

//가상계좌 변경
var modiBa = function(url) {
    var frm_pay = document.frm_pay;

    var today = new Date();
    var year  = today.getFullYear();
    var month = today.getMonth() + 1;
    var date  = today.getDate();
    var time  = today.getTime();

    if(parseInt(month) < 10) {
        month = "0" + month;
    }

    if(parseInt(date) < 10) {
        date = "0" + date;
    }

    frm_pay.return_url.value = url;
    frm_pay.EP_order_no.value = "ORDER_" + year + month + date + time;   //가맹점주문번호
    frm_pay.EP_expire_date.value = "" + year + month + date; // 무통장입금 입금만료일(YYYYMMDD)
    frm_pay.EP_expire_time.value = "235959";                 // 무통장입금 입금만료시간(HHMMSS)

    frm_pay.submit();
}

/**
 * @brief 상품 페이지에서 셀렉트 박스 변경시 화면 이동
 *
 * @param cateSortcode = 카테고리 분류코드(대 or 중 or 소)
 */
var moveProduct = function(cateSortcode) {
    var url  = "/product/common/move_product.php?cs=" + cateSortcode;
        url += "&t=" + encodeURI($("#title").val());
    location.href = url;
};

/**
 * @brief 주문 상세정보 펼치기
 *
 * @param idx     = 행 위치
 * @param seqno   = 주문공통일련번호
 * @param colspan = 열병합 값
 * @param id      = 행 id
 */
var openOrderDetail = function(idx, seqno, colspan, id) {

    if (checkBlank(id) === true) {
        id = "detail";
    }

    var url = "/ajax/common/load_order_view.php";
    var data = {
    	"order_common_seqno" : seqno,
    	"colspan"            : colspan,
    };
    var callback = function(result) {
        $("#" + id + idx).html(result);
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

//상세보기 접기
var closeOrderDetail = function(idx) {
    $("#detail" + idx).html("");
}

/**
 * @brief 건수 초기화
 *
 * @param val = 건수
 * @param id  = 객체 아이디
 */
var initCount = function(val, id) {
    val = parseInt(val);

    // 건수 초기화
    var option = "";
    for (var i = 1; i <= val; i++) {
        option += "<option value=\"" + i + "\">" + i + "</option>";
    }
    $("#" + id).html(option);
};


/**
 * @brief 빠른견적서에서 가격항목 데이터 추가
 *
 * @param data = 기존 data 파라미터
 *
 * @return data = 추가된 data 파라미터
 */
var getEstiPopData = function(data) {
    var paperPrice  = $.trim($("#esti_paper").text());
    var outputPrice = $.trim($("#esti_output").text());
    var printPrice  = $.trim($("#esti_print").text());
    var optPrice    = $.trim($("#esti_opt").text());
    var supplyPrice = $.trim($("#esti_supply").text());
    var tax         = $.trim($("#esti_tax").text());
    var sellPrice   = $.trim($("#esti_sell_price").text());
    var salePrice   = $.trim($("#esti_sale_price").text());
    var payPrice    = $.trim($("#esti_pay_price").text());

    data.paper_price  = paperPrice;
    data.print_price  = printPrice;
    data.output_price = outputPrice;
    data.opt_price    = optPrice;
    data.supply_price = supplyPrice;
    data.tax          = tax;
    data.sell_price   = sellPrice;
    data.sale_price   = salePrice;
    data.pay_price    = payPrice;

    $.each(aftArr, function(aftKo, aftEn) {
        data[aftEn + "_price"] = $.trim($("#esti_" + aftEn).text());
    });

    return data;
};
