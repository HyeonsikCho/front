/*
 *
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016/06/14 임종건 생성
 *============================================================================
 *
 */

$(document).ready(function() {

    //일자별 검색 datepicker 기본 셋팅
    $("#from").datepicker({
        dateFormat: 'yy-mm-dd',
        prevText: '이전 달',
        nextText: '다음 달',
        monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        dayNames: ['일','월','화','수','목','금','토'],
        dayNamesShort: ['일','월','화','수','목','금','토'],
        dayNamesMin: ['일','월','화','수','목','금','토'],
        showMonthAfterYear: true,
        yearSuffix: '년'
    });

    $("#to").datepicker({
        dateFormat: 'yy-mm-dd',
        prevText: '이전 달',
        nextText: '다음 달',
        monthNames: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
        dayNames: ['일','월','화','수','목','금','토'],
        dayNamesShort: ['일','월','화','수','목','금','토'],
        dayNamesMin: ['일','월','화','수','목','금','토'],
        showMonthAfterYear: true,
        yearSuffix: '년'
    });

    dateSet('0');
    getPaymentList(10, 1);
});

var listCnt = "";

/**
 * @brief 결재내역 리스트
 */
var getPaymentList = function(listSize, page) {

    if (listSize == "") {
        listSize = listCnt;
    }

    var url = "/ajax/mypage/payment_list/load_payment_list.php";
    var blank = "<tr><td colspan=\"5\">검색 된 내용이 없습니다.</td></tr>";
    var data = {
    	"from"     : $("#from").val(),
    	"to"       : $("#to").val(),
    	"dvs"      : $("#dvs").val()
	};
    var callback = function(result) {
        var rs = result.split("♪");
        if (rs[0].trim() == "") {
            $("#list").html(blank);
            $("#paging").html("<li><button class='on'>1</button><li>");
            $("#resultNum").html("<em>0</em>건의 검색결과가 있습니다.");

        } else {
            $("#list").html(rs[0]);
            $("#paging").html(rs[1]);
            $("#resultNum").html(rs[2]);
        }
    };

    data.list_num      = listSize;
    data.page          = page;

    showMask();
    ajaxCall(url, "html", data, callback);
}

/**
* @brief 보여줄 페이지 수 설정
*/
var changeListNum = function(val) {
    listCnt = val;
    getPaymentList(listCnt, 1);
}

/**
* @brief 페이지 이동
*/
var movePage = function(val) {

    getPaymentList(listCnt, val);
}
