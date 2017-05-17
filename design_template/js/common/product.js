/*
 *
 * Copyright (c) 2016-2017 Nexmotion, Inc.
 * All rights reserved.
 *
 * REVISION HISTORY (reverse chronological order)
 *============================================================================
 * 2016-11-08 엄준현 수정(근접한 규격 찾는부분 로직 수정)
 * 2017-01-04 엄준현 수정(후공정 로직변경으로 인한 수정)
 *============================================================================
 *
 */
var emailPop = null;

$(document).ready(function() {
    $('#pic_view').elevateZoom({scrollZoom : true, objId : "zoom_pic"});

    // 재질미리보기 처리 --> 최초이미지 없으니까 처리 안됨 필요함
    $('#paper_preview').elevateZoom({scrollZoom : true,
                                     objId : "zoom_preview",
                                     zoomWindowOffetx : -169});
/*
    var runtimes = "html5,flash,silverlight,html4";
    var mimeTypes = [
        {extensions : "zip,egg,rar,jpg,jpeg,sit,zip,ai,png,alz,cdr,cdt,eps,cmx,7z,pdf"}
    ];

    var uploader = new plupload.Uploader({
        url                 : "/proc/order/upload_file.php",
        runtimes            : runtimes,
        browse_button       : "work_file", // you can pass in id...
        flash_swf_url       : "/design_template/js/uploader/Moxie.swf",
        silverlight_xap_url : "/design_template/js/uploader/Moxie.xap",
        multi_selection     : false,

        filters : {
            max_file_size : "500mb",
            mime_types    : mimeTypes
        },
        init : {
            PostInit : function() {
                $("#work_file_list").html('');
            },
            FilesAdded : function(up, files) {
                // 파일을 새로 추가할 경우
                if (up.files.length > 1) {
                    var fileSeqno = $("#work_file_del").attr("file_seqno");

                    // 파일이 업로드 된 상태(fileSeqno !== empty)에서
                    // 다른 파일을 새로 업로드 할 경우
                    if (checkBlank(fileSeqno) === false &&
                        confirm("기존 파일은 삭제합니다." +
                            "\n계속 하시겠습니까?") === false) {
                        return false;
                    }

                    up.removeFile(up.files[0]);

                    if (checkBlank(fileSeqno) === false) {
                        removeFile(fileSeqno, false);
                    }
                }

                plupload.each(files, function(file) {
                    if (file.size > 524288000) {
                        up.removeFile(up.files[0]);
                        showWorkFileTr(false);
                        return alertReturnFalse("500MB를 넘는 파일은 웹하드를 이용해주세요.");
                    }

                    document.getElementById(listId).innerHTML =
                        "<div id=\"" + file.id + "\">" +
                        file.name + " (" +
                        plupload.formatSize(file.size) +
                        ")<b></b>" +
                        "&nbsp;" +
                        "<img src=\"/design_template/images/common/btn_circle_x_red.png\"" +
                        "     id=\"work_file_del\"" +
                        "     file_seqno=\"\"" +
                        "     alt=\"X\"" +
                        "     onclick=\"removeFile('" + idx + "', '', true);\"" +
                        "     style=\"cursor:pointer;\" /></div>";
                });
            },
            FilesRemoved : function(up, files) {
                document.getElementById(listId).innerHTML = '';
                $("#work_file_seqno").val('');
            },
            UploadProgress : function(up, file) {
                document.getElementById(file.id)
                    .getElementsByTagName("b")[0]
                    .innerHTML = "<span>" + file.percent + "%</span>";
            },
            FileUploaded : function(up, file, response) {
                var jsonObj   = JSON.parse(response.response);
                var fileSeqno = jsonObj.file_seqno;
                var operSys   = jsonObj.oper_sys;

                $("#" + delId).attr(
                    {"onclick"    : "removeFile('" + fileSeqno + "', true);",
                        "file_seqno" : fileSeqno}
                );

                $("#work_file_seqno").val(fileSeqno);
                $("#oper_sys").val(operSys);
            },
            Error : function(up, err) {
                if (checkBlank(err.code) === false &&
                    parseInt(err.code) === -600) {
                    showWorkFileTr(false);
                    return alertReturnFalse("500MB를 넘는 파일은 웹하드를 이용해주세요.");
                }

                $("#work_file_list").html("\nError #" + err.code + ": " + err.message);
            }
        }
    });

    uploader.init();

    $("#submit_ifr").on("load", function() {
        hideMask();

	$(this).find("#success").val();
    });
*/
});

/**
 * @brief 작업파일 업로드 구분에 따라 작업파일 부분 출력
 *
 * @param flag = 출력여부
var showWorkFileTr = function(flag) {
    if (flag === false) {
        $("#work_file_dl").hide();
        $("#webhard_dl").show();
    } else {
        $("#work_file_dl").show();
        $("#webhard_dl").hide();
    }
};
 */

/**
 * @brief 웹하드페이지로 이동
 *
 * @return 웹하드 팝업 오픈
var goWebhardPage = function() {

    //window.open("/ajax/order/load_webhard_page.php", "POP");
    //window.open("http://www.webhard.co.kr/webII/page/member/?load=2", "POP");
    window.open("http://www.webhard.co.kr", "POP");
}
 */

/**
 * @brief 비규격 사이즈 입력시 자리수 재계산
 * product_design.js에서 호출한다
 */
var calcManuPosNum = {
    "defWid"  : 0,
    "defVert" : 0,
    "maxWid"  : 0,
    "maxVert" : 0,
    "exec"    : function(dvs) {
        if ($("#no_pos").length > 0) {
            return false;
        }

        var prefix = '';

        if (checkBlank(dvs)) {
            if ($("#common_prdt_dvs").length > 0) {
                dvs = commonDvs;
            } else {
                dvs = prdtDvs;
            }
            prefix = getPrefix(dvs);
        } else {
            prefix = getPrefix(dvs);
        }

        if ($(prefix + "manu_pos_num").length === 0) {
            loadSimilarSize(dvs);
            return false;
        }

        var w = parseFloat($(prefix + "cut_wid_size").val());
        var v = parseFloat($(prefix + "cut_vert_size").val());
        var calW = Math.ceil(w / this.defWid) * Math.ceil(v / this.defVert);
        var calV = Math.ceil(v / this.defWid) * Math.ceil(w / this.defVert);

        if (calW === 1 || calV === 1) {
            $(prefix + "manu_pos_num").val(1);
        } else if (calW > calV) {
            $(prefix + "manu_pos_num").val(calW);
        } else {
            $(prefix + "manu_pos_num").val(calV);
        }

        calcPrice(prdtDvs);
    }
};

/**
 * @brief 전단류의 상품에서 비규격 사이즈 입력시 가장 근접한 규격사이즈 계산
 *
 * @param dvs = 제품구분값
 * @param callback = 콜백함수
 */
var loadSimilarSize = function(dvs, callback) {
    var prefix = getPrefix(dvs);

    var w = parseFloat($(prefix + "cut_wid_size").val());
    var v = parseFloat($(prefix + "cut_vert_size").val());

    if (w === 0 || v === 0) {
        return false;
    }

    var url = "/ajax/product/load_similar_size.php";
    var data = {
        'w' : w,
        'v' : v,
        "cate_sortcode" : $(prefix + "cate_sortcode").val()
    };

    if (checkBlank(callback) === true) {
        callback = function(result) {
            if (!checkBlank(result.max_wid)) {
                alert("용지 최대크기를 넘겼습니다.");

                $(prefix + "cut_wid_size").val(result.max_wid);
                $(prefix + "cut_vert_size").val(result.max_vert);
                size();
                orderSummary();
            }

            var str = result.name + " 1/" + result.divide + " 등분";

            $(prefix + "similar_size").attr("divide", result.divide);
            $(prefix + "similar_size").html(str);
            $(prefix + "size").val(result.mpcode);
            changeSize.exec(dvs);
        };
    }

    ajaxCall(url, "json", data, callback);
};

/**
 * @brief 지질느낌 검색
 *
 * @param dvs    = changeData(). callback()에서 쓰일 구분값
 * @param mpcode = 종이 맵핑코드
 */
var loadPaperDscr = {
    "obj"  : null,
    "exec" : function(dvs, mpcode) {
        var prefix = getPrefix(dvs);
        this.obj = prefix + "paper_sense";

        var url = "/ajax/product/load_paper_dscr.php";
        var data = {
            "mpcode" : mpcode
        };
        var callback = function(result) {
            $(loadPaperDscr.obj).html(result);
        };

        ajaxCall(url, "text", data, callback);

        changeData(dvs);
    }
};

/**
 * @brief 상품 수량과 후공정/옵션의 기준단위를 비교해서
 * 값을 통일시키는 함수
 *
 * @detail case1 : 후공정 R, 상품 장 = 상품 / 500
 * case2 : 후공정 장, 상품 R = 상품 * 500
 *
 * @param amt      = 상품수량
 * @param amtUnit  = 수량단위
 * @param crtrUnit = 후공정/옵션 기준단위
 */
var amtCalc = function(dvs, amt, amtUnit, crtrUnit) {
    if (amtUnit === "R" && crtrUnit === "장") {
        amt = calcSheetCount(dvs);
    } else if (amtUnit === "장" && crtrUnit === "R") {
        amt = calcRCount(dvs);
    }

    return amt;
};

/**
 * @brief 인쇄도수 변경시 인쇄용도 변경
 */
var changeTmpt = function(dvs) {
    var prefix = getPrefix(dvs);
    var val = $(prefix + "print_tmpt > option:selected").text();

    var url = "/ajax/product/load_print_purp.php";
    var data = {
        "cate_sortcode" : $(prefix + "cate_sortcode").val(),
        "val"           : val
    };
    var callback = function(result) {
        $(prefix + "print_purp").html(result);
        changeData(dvs);
    };

    ajaxCall(url, "html", data, callback);
};

/**
 * @param 인쇄방식과 사이즈 계열에 해당하는 인쇄도수 검색
 *
 * @param dvs = 제품구분값
 * @param callback = ajax callback 함수
 */
var loadPrintTmptCommon = {
    "exec" : function(dvs, callback) {
        var prefix = getPrefix(dvs);

        var url = "/ajax/product/load_print_tmpt.php";
        var data = {
            "cate_sortcode" : $(prefix + "cate_sortcode").val(),
            "tmpt_name"     : $(prefix + "print_tmpt > option:selected").text(),
            "bef_tmpt_name" : $(prefix + "bef_tmpt > option:selected").text(),
            "aft_tmpt_name" : $(prefix + "aft_tmpt > option:selected").text(),
            "purp_dvs"      : $(prefix + "print_purp").val(),
            "affil"         : $(prefix + "size > option:selected").attr("affil")
        };

        ajaxCall(url, "json", data, callback);
    }
};

/**
 * @brief 견적서 엑셀 다운로드
 */
var downEstiExcel = function() {
    var url = "/ajax/product/make_esti_excel.php";
    var callback = function(result) {
        var downUrl  = "/common/down_esti_excel.php?"
        downUrl += "filename=" + result;
        downUrl += "&dvs=esti";
        $("#file_ifr").attr("src", downUrl);
    };

    ajaxCall(url, "html", makeEstiPopInfo.data, callback);
};

/**
 * @brief 견적서 출력 팝업 출력
 */
var showEstiPop = function() {
    var $modalMask =  $(".modalMask.l_estimate");
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

        makeEstiPopInfo.exec("pop");

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
 * @breif 견적서 팝업 공통부분 생성
 *
 */
var getEstiPopHtml = function(data) {

    var url = "/ajax/product/load_estimate_pop.php";
    var callback = function(result) {
        $("#esti_cont").html(result);
    };

    ajaxCall(url, "html", data, callback);
};

/**
 * @brief 견적서 출력 > 이메일 발송 팝업 출력
 */
var showEmailPop = function() {
    var url = "/ajax/product/load_email_pop.php";
    emailPop = layerPopup("l_email", url);
};

/**
 * @brief 견적서 팝업에서 이메일 발송 클릭시
 */
var sendEmail = function() {
    var url = "/ajax/product/send_email.php";
    var data = makeEstiPopInfo.data;
    data.email_dvs = $("input[name='emailAddressType']:checked").val();
    data.m_acc = $("#m_acc").val();
    data.m_dom = $("#m_dom").val();
    data.d_acc = $("#d_acc").val();
    data.d_dom = $("#d_dom").val();
    var callback = function(result) {
        closePopup(emailPop);
        emailPop = null;

        if (result === 'F') {
            return alertReturnFalse("이메일 전송에 실패했습니다.");
	}

        return alertReturnFalse("이메일 전송에 성공했습니다.");
        hideMask();
    };

    showMask();
    ajaxCall(url, "text", data, callback);
};

/**
 * @brief 즉시주문
 */
var purProduct = function() {
    goCart(true);
};

/**
 * @brief 관심상품 등록
 */
var goWishlist = function() {
    if (setSubmitParam() === false) {
        return false;
    }

    var url      = "/mypage/add_wishlist.html";
    var data     = $frm.serialize();
    var callback = function(result) {
        if (!checkBlank(result)) {
            alert(result);
        } else {
            alert("해당 상품을 관심상품에 추가했습니다.");
	}
    };

    showMask();
    ajaxCall(url, "text", data, callback);
};

/**
 * @brief 장바구니로 이동
 */
var goCart = function(flag) {
    if (setSubmitParam() === false) {
        return false;
    }

    if (!flag) {
        if (confirm("계속 쇼핑하시겠습니까?")) {
            $("#cart_flag").val('A');

            var url      = "/order/add_cart.html";
            var data     = $frm.serialize();
            var callback = function(result) {
                if (!checkBlank($.trim(result))) {
                    alert(result);
                }
            };

            showMask();
            ajaxCall(url, "text", data, callback);

            return false;
        }
    }

    if (flag === true) {
        $("#cart_flag").val('N');
    } else {
        $("#cart_flag").val('Y');
    }

    $("#frm").attr("action", "/order/add_cart.html");
    $("#frm").submit();
};

/**
 * @brief 제품별 접두사 생성
 *
 * @return 제품 구분값별 접두사
 */
var getPrefix = function(dvs) {
    if (checkBlank(dvs) === true) {
        return '#';
    } else {
        return '#' + dvs + '_';
    }
}

/**
 * @brief 사이즈에 따른 연단위별 종이 장수 계산
 *
 * @param dvs = 제품 구분값
 */
var calcSheetCount = function(dvs) {
    var prefix = getPrefix(dvs);

    var amt    = $(prefix + "amt").val();
    var posNum = $(prefix + "size").find("option:selected").attr("pos_num");
    var divide = $(prefix + "similar_size").attr("divide");

    if (checkBlank(divide)) {
        divide = 1;
    }

    amt    = parseFloat(amt);
    posNum = parseFloat(posNum);
    divide = parseFloat(divide);

    var sheetCount = amt * divide * posNum * 500.0;

    // 재단 추가후공정 on
    /*
    if (divide === 1.0) {
        $(prefix + "cutting").prop("checked", false);
    } else {
        $(prefix + "cutting").prop("checked", true);
    }

    $(prefix + "cutting").trigger("click");
    */

    $(prefix + "sheet_count").val(sheetCount);
    $("#sheet_count_span").html(sheetCount.format());
};

/**
 * @brief 사이즈에 따른 장수별 종이 연수 계산
 */
var calcRCount = function(dvs) {
    var prefix = getPrefix(dvs);

    var amt    = $(prefix + "amt").val();
    var posNum = $(prefix + "size").find("option:selected").attr("pos_num");
    var divide = $(prefix + "similar_size").attr("divide");

    if (checkBlank(divide)) {
        divide = 1;
    }

    amt    = parseFloat(amt);
    posNum = parseFloat(posNum);
    divide = parseFloat(divide);

    var sheetCount = amt / divide / posNum / 500.0;
    /*
    sheetCount *= 10.0;
    sheetCount  = Math.ceil(sheetCount) / 10.0;
    */

    // 재단 추가후공정 on
    /*
    if (divide === 1.0) {
        $(prefix + "cutting").prop("checked", false);
    } else {
        $(prefix + "cutting").prop("checked", true);
    }

    $(prefix + "cutting").trigger("click");
    */

    $(prefix + "r_count").val(sheetCount);
    $("#r_count_span").html(sheetCount.format());
};

/**
 * @brief 등분에 따라 실인쇄 수량 계산
 *
 * @param dvs = 제품 구분값
 */
var calcPrdtAmt = function(dvs) {
    var prefix  = getPrefix(dvs);
    var amtUnit = $(prefix + "amt").attr("amt_unit");
    var divide  = parseInt($(prefix + "similar_size").attr("divide"));

    $(prefix + "size > option").each(function() {
        var val = parseInt($(this).val()) * divide;

        $(this).val(val);
    });
};

//특수문자 값 빈값 리턴
function inputCheckSpecial() {
    var re = /[\{\}\[\]\/?.,;:|\)*~`!^\+<>@\#$%&\\\=\(\'\"]/gi; //-_제외
    var tmp = $("#title").val();
    return $("#title").val(tmp.replace(re, ""));
}

//제품보기
var productPreview = function() {
    $("#mat_btn").show();
    $("#pro_btn").hide();
    $("#mov_btn").hide();

    $("#picture").show();
    $("#mat_preview").hide();
    $("#mov_preview").hide();

    $("#zoom_pic").show();
    $("#zoom_preview").hide();
}

//재질보기
var matPreview = function() {
    var aftStr = $.trim($(".overview").text());

    $("#mat_btn").hide();

    if (aftStr.indexOf("접지") > -1) {
        $("#pro_btn").hide();
        $("#mov_btn").show();
    } else {
        $("#pro_btn").show();
        $("#mov_btn").hide();
    }


    $("#picture").hide();
    $("#mat_preview").show();
    $("#mov_preview").hide();

    $("#zoom_pic").hide();
    $("#zoom_preview").show();
}

//동영상보기
var movPreview = function() {
    $("#util_btns").hide();
    $("#mov_btns").show();

    $("#picture").hide();
    $("#mat_preview").hide();
    $("#mov_preview").show();

    $("#zoom_pic").hide();
    $("#zoom_preview").hide();

    document.getElementById("mov").currentTime = 0;
};

// 동영상 경로 변경
var setAfterMovSrc = function(dvs) {
    var prefix = getPrefix(dvs);

    var url = "/ajax/product/load_after_mov_src.php";
    var data = {
        "depth1" : $(prefix + "foldline_dvs > option:selected").text(),
        "depth2" : $(prefix + "foldline_val > option:selected").text()
    };
    var callback = function(result) {
        $("#mov_src").attr("src", result);

        var mov = document.getElementById("mov");
        mov.load();
        mov.play();
        movPreview();
    };

    ajaxCall(url, "text", data, callback);
};

// 동영상 확대
var movZoomIn = function() {
    $("#mov").attr({"width":720, "height":720});
    $("._anchor.ui-draggable-handle").hide();

    $("#mov_zoom_in").hide();
    $("#mov_zoom_out").show();
};

// 동영상 축소
var movZoomOut = function() {
    $("#mov").attr({"width":400, "height":400});
    $("._anchor.ui-draggable-handle").show();

    $("#mov_zoom_in").show();
    $("#mov_zoom_out").hide();
};

// 동영상 닫기
var closeMovPreview = function() {
    $("#util_btns").show();
    $("#mov_btns").hide();

    movZoomOut();
    productPreview();
};

//상세보기
var imgDetailView = function() {
    var src = $("#pic_view").attr("src");
    layerPopup('l_imgDetailView','/product/popup/l_imgDetailView.html?src='+src);
}

/**
 * @brief 빠른 견적서 내용 변경
 *
 * @param param = 견적서 내용값
 */
var changeQuickEsti = function(param) {
    var paper  = param.paper;
    var print  = param.print;
    var output = param.output;
    var after  = param.after;
    var opt    = param.opt;
    var count  = param.count;

    var gradeSaleRate = param.gradeSaleRate;
    var sellPrice     = param.sellPrice;
    var salePrice     = sellPrice * gradeSaleRate;
    salePrice = ceilVal(salePrice);

    sellPrice += after + opt;

    var supplyPaper  = ceilVal(paper / 1.1);
    var supplyPrint  = ceilVal(print / 1.1);
    var supplyOutput = ceilVal(output / 1.1);
    var supplyAfter  = ceilVal(after / 1.1);
    var supplyOpt    = ceilVal(opt / 1.1);

    var supplyPrice  = supplyPaper +
                       supplyOutput +
                       supplyPrint +
                       supplyAfter +
                       supplyOpt;

    var tax = ceilVal(supplyPrice / 10);
    var temp = supplyPrice + tax;

    if (sellPrice !== temp) {
        tax +=
            (sellPrice < temp) ? (temp - sellPrice) * -1 : sellPrice - temp;
    }

    if (supplyPaper === 0) {
        $(".esti_paper_info").hide();
    } else {
        $(".esti_paper_info").show();
    }

    if (supplyOutput === 0) {
        $(".esti_output_info").hide();
    } else {
        $(".esti_output_info").show();
    }

    if (supplyPrint === 0) {
        $(".esti_print_info").hide();
    } else {
        $(".esti_print_info").show();
    }

    // 견적서 종이비 변경
    $("#esti_paper").html(supplyPaper.format());
    // 견적서 출력비 변경
    $("#esti_output").html(supplyOutput.format());
    // 견적서 인쇄비 변경
    $("#esti_print").html(supplyPrint.format());
    // 견적서 후공정비 변경
    $("#esti_after").html(supplyAfter.format());
    // 견적서 옵션비 변경
    $("#esti_opt").html(supplyOpt.format());
    // 견적서 건수 변경
    $("#esti_count").html(count.format());
    // 견적서 공급가 변경
    $("#esti_supply").html(supplyPrice.format());
    // 견적서 부가세 변경
    $("#esti_tax").html(tax.format());
    // 견적서 정상판매가 변경
    $("#esti_sell_price").html(sellPrice.format());
    // 견적서 할인금액 변경
    $("#esti_sale_price").html(salePrice.format());
    // 견적서 결제금액 변경
    $("#esti_pay_price").html((sellPrice + salePrice).format());
};

/**
 * @brief 종이 분류 변경시 해당하는 종이명 검색
 *
 * @param dvs  = 위치구분
 * @param sort = 종이분류
 */
var loadPaperName = function(dvs, sort) {
    var url = "/ajax/product/load_paper_name.php";
    var data = {
        "cate_sortcode" : sortcode,
        "sort"          : sort,
    };
    var callback = function(result) {
        var prefix = getPrefix(dvs);
        $(prefix + "paper_name").html(result);

        loadPaperInfo(dvs, $(prefix + "paper_name").val(), sort);
    };

    ajaxCall(url, "html", data, callback);
};

/**
 * @brief 종이명 변경시 해당하는 색상-구분-평량 검색
 *
 * @param dvs  = 위치구분
 * @param name = 종이명
 * @param sort = 종이분류
 */
var loadPaperInfo = function(dvs, name, sort) {
    var url = "/ajax/product/load_paper_info.php";
    var data = {
        "cate_sortcode" : sortcode,
        "sort"          : sort,
        "name"          : name
    };
    var callback = function(result) {
        var prefix = getPrefix(dvs);
        $(prefix + "paper").html(result);

        loadPaperPreview(dvs);
        loadPaperDscr.exec(dvs, $(prefix + "paper").val());
        reCalcAfterPrice(dvs, null);
    };

    ajaxCall(url, "html", data, callback);
};

/**
 * @brief 후공정 체크된 것 중 다시 맵핑코드 다시 불러올 것만 처리
 *
 * @param dvs = 제품구분
 */
var getAftInfoArr = function(dvs) {
    var ret = [];
    var prefix = getPrefix(dvs);

    $("input[type='checkbox'][name='" + dvs + "_chk_after[]']").each(function() {
        //if ($(this).prop("checked") === false) {
        //    return true;
        //}

        var aftEnName = $(this).attr("aft");
        var aftKoName = $(this).val();
        var info = {};

        if (aftKoName === "코팅") {
            var depth1 =
                $(prefix + aftEnName + "_val > option:selected").text();
            depth1 = depth1.split(' ')[0];

            info.name   = aftKoName;
            //info.depth1 = depth1;
            ret.push(info);

            return true;
        }
        if (aftKoName === "오시") {
            var depth1 =
                $(prefix + aftEnName + "_cnt > option:selected").text();

            info.name   = aftKoName;
            info.depth1 = depth1;
            ret.push(info);

            return true;
        }
        if (aftKoName === "미싱") {
            var depth1 =
                $(prefix + aftEnName + "_cnt > option:selected").text();

            info.name   = aftKoName;
            info.depth1 = depth1;
            ret.push(info);

            return true;
        }
        if (aftKoName === "접지") {
            var depth1 =
                $(prefix + aftEnName + "_dvs > option:selected").text();

            info.name   = aftKoName;
            info.depth1 = depth1;
            ret.push(info);

            return true;
        }
        if (aftKoName === "넘버링") {
            var depth1 =
                $(prefix + aftEnName + "_dvs > option:selected").text();

            info.name   = aftKoName;
            info.depth1 = depth1;
            ret.push(info);

            return true;
        }
        if (aftKoName === "제본") {
            var depth1 =
                $(prefix + aftEnName + "_dvs > option:selected").text();

            info.name   = aftKoName;
            info.depth1 = depth1;
            ret.push(info);

            return true;
        }
    });

    return ret;
};

/**
 * @brief 책자형에서 실제 종이수량 계산
 *
 * @param = 계산용 파라미터
 */
var getBookletPaperAmt = function(param) {
    var amt    = param.amt;
    var posNum = param.posNum;
    var page   = param.page;

    return (amt / posNum) / (2.0 / page);
};

/**
 * @brief 템플릿 다운로드 팝업 출력
 */
var showTemplatePop = function() {
    var url = "/ajax/product/template_pop/" + sortcode + ".html";
    layerPopup("l_information", url);
};

/**
 * @brief 출고일 확인 팝업 출력
 */
var showDeliveryPop = function() {
    var url = "/ajax/product/load_delivery_pop.php?";
    url += "cs=" + sortcode;
    url += "&cn=" + encodeURI(cateName);
    layerPopup("l_delivery", url);
};

/**
 * @brief 미리보기 배경이미지 칼선으로 변경
 */
var changePreviewImg = function(stanName) {
    var url = "/design_template/images/product/preview/"+ stanName + ".jpg";
    $(".paper .content").hide();
    $(".paper").css({"background-image" : "url(" + url + ')',
                     "background-size"  : "100% 100%",
                     "border"           : "0px"});
};

/**
 * @brief 책자형 실제 종이수량 계산
 */
var getPaperRealPrintAmt = function(dvs) {
    var prefix = getPrefix(dvs);

    var amt    = parseFloat($(prefix + "amt").val());
    var posNum = parseFloat($(prefix + "size > option:selected").attr("pos_num"));
    var page   = 0;

    var arrLength = dvsArr.length;
    for (var i = 0; i < arrLength; i++) {
        var dvs = dvsArr[i];
        var pfx = getPrefix(dvs);

        if (!dvsOnOff[dvs]) {
            continue;
        }

        var val = parseInt($(pfx + "page").val());
        page += val;
    }

    return (amt / posNum) / (2.0 / page);
};

/**
 * @brief 재질 미리보기 이미지 load
 *
 * @param dvs = 제품구분값
 * @parma id  = 객체 아이디 직접입력
 */
var loadPaperPreview = function(dvs, id) {
    var prefix = getPrefix(dvs);

    if (checkBlank(id)) {
        id = prefix + "paper";
    }

    var url = "/json/product/load_paper_preview.php";
    var data = {
        "paper_mpcode" : $(id).val()
    };
    var callback = function(result) {
        $("#paper_preview").attr({
            "src"             : result.zoom,
	    "data-zoom-image" : result.zoom
        });

        $("#zoom_preview .zoomWindow").css("background-image",
                                           "url(" + result.zoom + ")");
    };

    ajaxCall(url, "json", data, callback);
};

/**
 * @brief 인쇄용도 바뀌었을 때 설명 출력, 가격검색
 *
 * @param dvs = 제품구분
 */
var changePrintPurp = function(dvs) {
    showUvDescriptor(dvs);

    if (isFunc("loadPrintTmpt")) {
        loadPrintTmpt(dvs);
    }
};

/**
 * @brief 인쇄용도 UV일 경우 설명 출력
 *
 * @param dvs = 제품구분
 */
var showUvDescriptor = function(dvs) {
    var html = "<dt id=\"uv_descriptor\" style=\"width:100%;\">";
    html +=    "    * UV인쇄는 특수잉크를 사용하며<br/>";
    html +=    "    &nbsp;&nbsp;인쇄기자체에 말리는 장치가 달려있어,<br/>";
    html +=    "    &nbsp;&nbsp;인쇄후 건조가 잘 안되는 인쇄물을<br/>";
    html +=    "    &nbsp;&nbsp;인쇄할 때 효과가 탁월히 나타납니다.";
    html +=    "</dt>";

    var prefix = getPrefix(dvs);
    var purp = $(prefix + "print_purp").val();

    if (purp.indexOf("UV") > -1) {
        $(prefix + "print_purp").parent().parent().append(html);
    } else {
        $("#uv_descriptor").remove();
    }
};

/**
 * @brief 재단사이즈 최대/최소 사이즈 체크
 */
var chkMaxMinSize = {
    "exec" : function(dvs) {
        var prefix = getPrefix(dvs);
        var $wid   = $(prefix + "cut_wid_size");
        var $vert  = $(prefix + "cut_vert_size");
        var cutWid  = parseInt($wid.val());
        var cutVert = parseInt($vert.val());
        var sortcodeT = sortcode.substr(0, 3);
        var sortcodeM = sortcode.substr(0, 6);

        if (sortcodeT === "001") {
            if (cutWid < 40) {
                alert("최소사이즈는 40*40입니다.");
                $wid.val(40);
            }
            if (cutVert < 40) {
                alert("최소사이즈는 40*40입니다.");
                $vert.val(40);
            }
        }

        var maxArr = null;
        if (!checkBlank(chkMaxMinSize[sortcode])) {
            maxArr = chkMaxMinSize[sortcode];
        } else if (!checkBlank(chkMaxMinSize[sortcodeM])) {
            maxArr = chkMaxMinSize[sortcodeM];
        }

        var maxWid  = maxArr.wid;
        var maxVert = maxArr.vert;
        var str = "최대사이즈는 " + maxWid + "*" +
                  maxVert + "입니다.";

        if (cutWid > maxWid) {
            alert(str);
            $wid.val(maxWid);
        }
        if (cutVert > maxVert) {
            alert(str);
            $vert.val(maxVert);
        }

        aftRestrict.cutting.common(dvs);
    },
    "001001001" : {
        "wid" : 520, "vert" : 750
    },
    "001001004" : {
        "wid" : 600, "vert" : 900
    },
    "001002" : {
        "wid" : 500, "vert" : 700
    },
    "001005002" : {
        "wid" : 520, "vert" : 750
    },
    "002001001" : {
        "wid" : 920, "vert" : 620
    },
    "002001002" : {
        "wid" : 920, "vert" : 620
    },
    "002001003" : {
        "wid" : 735, "vert" : 520
    },
    "002001004" : {
        "wid" : 735, "vert" : 520
    },
    "002001005" : {
        "wid" : 735, "vert" : 520
    }
};

/**
 * @brief 재단, 작업사이즈 가로세로 교환
 *
 * @parma dvs     = 제품구분값
 * @parma checked = 값을 변경할지, 되돌릴지 여부
 */
var swapSize = {
    "orgCutW" : 0,
    "orgCutV" : 0,
    "orgWorkW" : 0,
    "orgWorkV" : 0,
    "exec" : function(dvs, checked) {
        var prefix = getPrefix(dvs);

        var cutW = $(prefix + "cut_wid_size").val();
        var cutV = $(prefix + "cut_vert_size").val();
        //var workW = $(prefix + "work_wid_size").val();
        //var workV = $(prefix + "work_vert_size").val();
        var workW = $("#work_wid_size").val();
        var workV = $("#work_vert_size").val();

        // 적용값
        var aplyCutW = 0;
        var aplyCutV = 0;
        var aplyWorkW = 0;
        var aplyWorkV = 0;

        if (checked) {
            this.orgCutW = cutW;
            this.orgCutV = cutV;
            this.orgWorkW = workW;
            this.orgWorkV = workV;

            if (parseInt(cutW) < parseInt(cutV)) {
                aplyCutW = cutV;
                aplyCutV = cutW;
            } else {
                aplyCutW = cutW;
                aplyCutV = cutV;
            }

            if (parseInt(workW) < parseInt(workV)) {
                aplyWorkW = workV;
                aplyWorkV = workW;
            } else {
                aplyWorkW = workW;
                aplyWorkV = workV;
            }
        } else {
            aplyCutW = this.orgCutW;
            aplyCutV = this.orgCutV;
            aplyWorkW = this.orgWorkW;
            aplyWorkV = this.orgWorkV;
        }


        $(prefix + "cut_wid_size").val(aplyCutW);
        $(prefix + "cut_vert_size").val(aplyCutV);
        //$(prefix + "work_wid_size").val(workV);
        //$(prefix + "work_vert_size").val(workW);
        $("#work_wid_size").val(aplyWorkW);
        $("#work_vert_size").val(aplyWorkV);

        preview.paperSize();
    }
};

/**
 * @brief 카테고리 템플릿 파일 다운로드
 *
 * @param seqno = 템플릿 일련번호
 * @param dvs   = 파일 구분값
 */
var downloadTemplate = function(dvs, seqno) {
    if (checkBlank(seqno)) {
        return false;
    }

    var url = "/product/common/template_file_down.php?";
    url += "&seqno=" + seqno;
    url += "&dvs=" + dvs;

    $("#file_ifr").attr("src", url);
};

/*
var goOrder = function() {
    showMask();
    $("#frm").attr("action", "/order/add_cart.html");
    $("#frm").attr("target", "submit_ifr");
    $frm.submit();
};
*/

/**
 * @brief 파일업로드 팝업 출력
var showUploadPop = function(isCart) {
    if (isCart === true) {
        $("#cart_flag").val('Y');
    } else {
        $("#cart_flag").val('N');
    }

    if (setSubmitParam() === false) {
        return false;
    }

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
 */
