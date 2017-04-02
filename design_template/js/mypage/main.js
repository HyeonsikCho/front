$(document).ready(function() {
    getOrderList("입금");
    $('#waiting').addClass('_on');
});

//견적 문의
var estimateView = function(seq) {
    var url = "/mypage/estimate_view.html";

    $("#seq").val(seq);
    $("#frm").attr("action", url);
    $("#frm").attr("method", "post");
    $("#frm").submit();
};

//1:1 문의
var ftfView = function(seq) {
    var url = "/mypage/ftf_view.html"
 
    $("#seq").val(seq);
    $("#frm").attr("action", url);
    $("#frm").attr("method", "post");
    $("#frm").submit();
}

//주문 상태별 정보
var getOrderStatus = function(typ) {

    var dvs = $("#dvs").val();
    var url = "/ajax/mypage/main/load_order_status_info.php";
    var data = {
        "typ" : typ
    };
    var callback = function(result) {
        var rs = result.split("♪");
        $("#order_status").html(rs[0]);
        $("#period").html(rs[1]);
        $("#period_from").val(rs[2]);
        $("#period_to").val(rs[3]);
        getOrderList(dvs);

	if (dvs == "입금") {
            $('#waiting').addClass('_on');
	} else if (dvs == "접수") {
            $('#application').addClass('_on');
	} else if (dvs == "조판") {
            $('#set').addClass('_on');
	} else if (dvs == "출력") {
            $('#print').addClass('_on');
	} else if (dvs == "인쇄") {
            $('#process').addClass('_on');
	} else if (dvs == "후공정") {
            $('#post').addClass('_on');
	} else if (dvs == "입고") {
            $('#stock').addClass('_on');
	} else if (dvs == "출고") {
            $('#print').addClass('_on');
	} else if (dvs == "배송") {
            $('#delivery').addClass('_on');
	} else if (dvs == "구매확정") {
            $('#complete').addClass('_on');
	}

        //switch
        $('._switch button, ._toggle button').on('click', function () {
            if ($(this).closest('li').hasClass('_on')) {
                if ($(this).closest('._toggle').hasClass('_toggle')) {
                    $(this).closest('li').removeClass('_on');
                }
                return false;
            } else {
                $(this).closest('ul').children('li._on').removeClass('_on');
                $(this).closest('li').addClass('_on');
            }
        });
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

//주문 리스트
var getOrderList = function(dvs) {

    var url = "/ajax/mypage/main/load_order_list.php";
    var data = {
        "dvs"  : dvs,
        "from" : $("#period_from").val(),
        "to"   : $("#period_to").val()
    };
    var callback = function(result) {
	$("#dvs").val(dvs);
        $("#order_list").html(result);
        orderTable($('body'));
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

// 페이지 이동
var goPage = function(url) {
    window.location.href = url;
}

// 팝업 페이지 이동
var goPopPage = function(url) {
    window.open(url, "_blank");
}
