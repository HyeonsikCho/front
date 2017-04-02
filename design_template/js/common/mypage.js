var popupMask = null;

$(document).ready(function () {
    var text1 = $('header.title .location li:eq(2) span').text().replace(/\s/g, ''),
        text2 = $('header.title .location li:eq(3) span').text().replace(/\s/g, ''),
        thisText = '';

    $('nav.lnb > ul > li > a').each(function () {
        thisText = $(this).html().replace(/\s/g, '').split('<span')[0];
        if (thisText == text1) {
            $(this).closest('li').addClass('on');
            $(this).closest('li').children('ul').find('a').each(function () {
                thisText = $(this).html().replace(/\s/g, '').split('<span')[0];

                if(thisText == text2) {
                    $(this).closest('li').addClass('on');
                }
            });
        }
    });
});

/**
* @brief 주문 취소
*/
var cancelOrder = function(seq) {

    var url = "/proc/mypage/order_all/proc_order_cancel.php";
    var data = {
    	"seqno" : seq
    };
    var callback = function(result) {
        if (result.trim() == 1) {
            alert("주문취소 되었습니다.");
            closePopup(popupMask);
            var link =  document.location.href;
            var str = link.split('/mypage/');
            if (str[1].substring(0,4) == "main") {
              getOrderList('입금');
              getOrderCnt();
            } else {
              orderSearch(30, 1);
            }

        } else if (result.trim() == 2){
            alert("이미 취소된 주문입니다.");
        } else if (result.trim() == 3){
            alert("접수 이후에는 취소할수 없습니다.");
        } else {
            alert("주문취소에 실패했습니다.");
        }
    };

    showMask();
    ajaxCall(url, "html", data, callback);
}

/**
* @brief 입금대기 or 접수카운트
*/
var getOrderCnt = function() {
    var url = "/ajax/mypage/main/load_order_cnt.php";
    var data = {
	};
    var callback = function(result) {
        var tmp = result.split('♪♭§');
        if (tmp[0].trim()) {

            $("#tot_cnt").html(tmp[0]);

            if (tmp[1].trim()) {
                $("#waiting_cnt").html(tmp[1]);
            }

            if (tmp[2].trim()) {
                $("#application_tot_cnt").html(tmp[2]);
            }
            /*
            if (tmp[3].trim()) {
                $("#application_st_cnt").html(tmp[3]);
            }

            if (tmp[4].trim()) {
                $("#application_nw_cnt").html(tmp[4]);
            }

            if (tmp[5].trim()) {
                $("#application_pr_cnt").html(tmp[5]);
            }

            if (tmp[6].trim()) {
                $("#application_de_cnt").html(tmp[6]);
            }
            */
        } else {

	    }
    };

    showMask();
    ajaxCall(url, "html", data, callback);



}

/**
* @brief 클레임요청
*/
var reqClaim = function(seq, state) {
    order_seqno = seq;

    if (state != "배송완료") {
        alert("배송완료시에 클레임요청이 가능합니다.");
        return;
    }

    var url = "/mypage/claim_write.html?order_seqno=" + seq;
    $(location).attr('href', url);

};

/**
* @brief 주문취소 팝업
*/
var showOrderCancelPop = function(seq) {
    var url = '/mypage/popup/l_ordercancel.html?order_seqno=' + seq;
    popupMask = layerPopup('l_orderCancel', url);
};

/**
* @brief 시안보기 팝업
*/
var showDraftPop = function(seq) {
    var url = '/mypage/popup/l_draft.html?seqno=' + seq;
    popupMask = layerPopup('l_draft', url);
};

/**
 * @brief 재주문 팝업 출력
 */
var showReOrderPop = function(seq) {
    commonObj.orderSeqno = seq;

    var url = '/mypage/popup/l_reorder.html';
    popupMask = layerPopup('l_reorder', url);
};

/**
 * @brief 재주문
 */
var doReOrder = function() {
    var url = "/proc/mypage/reorder.php";
    var data = {
        "order_seqno" : commonObj.orderSeqno
	};
    var callback = function(result) {
    };

    showMask();
    //ajaxCall(url, "html", data, callback);
};

/**
 * @brief 재전송 팝업 출력
 */
var showReUploadPop = function(seq, orderState) {
    $("#" + commonObj.listId).html('');

    if (!checkBlank(commonObj.uploader.files[0])) {
        commonObj.uploader.removeFile(commonObj.uploader.files[0]);
    }

    orderState = parseInt(orderState);

    if (orderState > 1360) {
        return alertReturnFalse("접수 이후에는 재전송이 불가능합니다.");
    }

    if (1330 <= orderState && orderState <= 1355) {
        return alertReturnFalse("접수중에는 재전송이 불가능합니다.");
    }

    commonObj.orderSeqno = seq;
    commonObj.orderState = orderState;

    var $modalMask =  $(".modalMask.l_orderInformation");
    var $contentsWrap = $modalMask.find('.layerPopupWrap');

    if ($modalMask.outerHeight() > $contentsWrap.height() &&
            $modalMask.outerWidth() > $contentsWrap.width()) {
        //drag
        $contentsWrap.draggable({
            addClasses  : false,
            cursor      : false,
            containment : $modalMask,
            handle      : "header"
        });
    } else {
        $("body").css("overflow", "hidden");
    }

    $modalMask.fadeIn(300, function () {
        $contentsWrap.css({
            'top' : $(window).height() > $contentsWrap.height() ?
	                ($(window).height() - $contentsWrap.height()) / 2 + 'px' : 0,
            'left' : $modalMask.width() > $contentsWrap.width() ?
	                ($modalMask.width() - $contentsWrap.width()) / 2 + 'px' : 0
        });

        orderTable($modalMask);

        var hideFunc = function() {
            $modalMask.fadeOut(300, function() {
                $("body").css("overflow", "auto");
            });
        };

        $modalMask.addClass("_on")
                  .find("button.close")
                  .on("click", hideFunc);
    });
};

/**
* @brief 주문메모 LOAD
*/
var showOrderMemoPop = function(seq) {
    var url = '/mypage/popup/l_memo.html?order_seqno=' + seq;
    popupMask = layerPopup('l_memo', url);
};

/**
 * @brief 주문파일 다운로드
 */
var downOrderFile = function(orderSeqno, fileSeqno) {
    var downUrl = "/ajax/mypage/down_order_file.php?order_seqno=" + orderSeqno;
    downUrl = "&file_seqno" + fileSeqno;

    //$("#file_ifr").attr("src", downUrl);
};
