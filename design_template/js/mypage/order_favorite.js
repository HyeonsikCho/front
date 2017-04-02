/*
 *
 * Copyright (c) 2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016/02/26 왕초롱 생성
 *============================================================================
 *
 */

$(document).ready(function() {
    /*
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
    */
    searchPrdt(10,1);
});

var listCnt = "";
var modalMask = "";

/**
 * @brief 선택조건으로 검색 클릭시
 */
var searchPrdt = function(listSize, page) {

    var url = "/ajax/mypage/order_favorite/load_prdt_list.php";
    var blank = "<tbody name=\"prdt_list\"><tr><td colspan=\"3\">검색 된 내용이 없습니다.</td></tr></tbody>";
    var data = {
    	//"from"          : $("#from").val(),
    	//"to"            : $("#to").val(),
    	"order_detail"  : $("#order_detail").val()
	};
    var callback = function(result) {
        var rs = result.split("♪");
        if (rs[0].trim() == "") {

            $("tbody[name='prdt_list']").remove();
            $("#list").after(blank);
            $("#paging").html("<li><button class='on'>1</button><li>");
            $("#resultNum").html("<em>0</em>건의 검색결과가 있습니다.");
            return false;

        } else {

            $("tbody[name='prdt_list']").remove();
            $("#list").after(rs[0]);
            $("#paging").html(rs[1]);
            $("#resultNum").html(rs[2]);

        }

        orderTable($('body'));
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
    searchPrdt(listCnt, 1);
}

/**
* @brief 페이지 이동
*/
var movePage = function(val) {

    searchPrdt(listCnt, val);
}

/**
* @brief 조건 검색
*/
var searchKey = function(event) {
    if(event.keyCode != 13) {
        return false;
    }
    searchPrdt(listCnt, 1);
}

/**
* @brief 조건 검색
*/
var searchTxt = function() {
    searchPrdt(listCnt, 1);
}

/**
 * @brief 선택조건으로 검색 클릭시
 */
var removeSelect = function() {

    var select_prdt = getselectedNo();

    if (select_prdt == "") {

        alert("삭제할 목록을 선택해주세요");
        return false;
    }

    if(confirm("선택한 상품을 삭제하시겠습니까?") == false)
    {

	    return false;
    }

    var url = "/proc/mypage/order_favorite/del_interest_prdt.php";
    var data = {
    		"select_prdt"    : select_prdt
	};
    var callback = function(result) {
        if (result.trim() == "1") {

                alert("삭제했습니다.");
		searchPrdt(listCnt, 1);

        } else {

                alert("삭제에 실패했습니다.");
	}

        $("input[name=allCheck]").prop("checked", false);
	searchPrdt();
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

/**
 * @brief 선택조건으로 검색 클릭시
 */
var putSelectPrdt = function() {

    var select_prdt = getselectedNo();

    if (select_prdt == "") {

        alert("장바구니에 담을 목록을 선택해주세요.");
        return false;
    }

    var url = "/proc/mypage/order_favorite/proc_interest_prdt.php";
    var data = {
    		"select_prdt"    : select_prdt
	};
    var callback = function(result) {
        if (result.trim() == "1") {

            alert("선택한 상품을 장바구니에 담았습니다..");

        } else {

            alert("장바구니 담기에 실패했습니다.");
        }
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

/**
 * @brief 전체 장바구니행
 */
var putAllPrdt = function() {

    var select_prdt = "";
    $("input[name='chk[]']").each(function() {
        select_prdt += ","+ $(this).val();		    
    });

    select_prdt = select_prdt.substring(1);

    var url = "/proc/mypage/order_favorite/proc_interest_prdt.php";
    var data = {
    		"select_prdt"    : select_prdt
	};
    var callback = function(result) {
        if (result.trim() == "1") {

            alert("전체 상품을 장바구니에 담았습니다..");

        } else {

            alert("장바구니 담기에 실패했습니다.");
	    }
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

//체크박스 선택시 value값 가져오는 함수
var getselectedNo = function(el) {

    var selectedValue = ""; 
    
    $("input[name='chk[]']:checked").each(function() {
        selectedValue += ","+ $(this).val();		    
    });

    if (selectedValue != "") {
        selectedValue = selectedValue.substring(1);
    }

    return selectedValue;
}
