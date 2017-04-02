/**
 * @brief 카테고리별 옵션 제약사항 스크립트
 */
var optRestrict = {
    "msg" : '',
    "alertFlag" : null,
    "all" : function(dvs) {
        var ret = true;
        optRestrict.msg = '';

        $("input[name='chk_opt']").each(function() {
            if ($(this).prop("checked") === false) {
                return true;
            }

            opt = $(this).val()
            funcObj = optRestrict[opt];

            if (checkBlank(funcObj) === true) {
                return true;
            }

            var idx = parseInt($(this).attr("id").split('_')[1]);

            ret = funcObj.common(dvs, idx);

            if (ret === false) {
                ret = false;
                alert(optRestrict.msg);
                setOptPrice(idx, 0);
                return true;
            }
        });
        console.log(ret);

        return ret;
    },
    "unchecked" : function(idx) {
        var obj = "#opt_" + idx
        $(obj).prop("checked", false);
        $(obj).trigger("click");
        optSlideUp(idx);
    },
    "당일판" : {
        "common" : function(dvs, idx, alertFlag) {
            var prefix = getPrefix(dvs);
            var sortcode = $(prefix + "cate_sortcode").val();
            var sortcodeT = sortcode.substr(0, 3);

            if (sortcodeT === "001" || sortcodeT === "002") {
                // 명함, 스티커류 2000장까지 가능
                var amt = parseInt($(prefix + "amt").val());

                if (amt > 2000) {
                    optRestrict.msg = "2000매 초과는 당일판이 불가합니다.";
                    optRestrict.unchecked(idx);
                    return false;
                }
            } else if (sortcodeT === "003") {
                // 전단류 A3, 8절일 때 1R, 3R 안됨
                // 5R 초과 안됨
                var size  = $(prefix + "size > option:selected").text();
                var amt   = parseFloat($(prefix + "amt").val());
                var paper = $(prefix + "paper > option:selected").text();
                paper = paper.split(' ');
                var basisweight = parseInt(paper[paper.length - 1]);

		/*
                if (amt > 5) {
                    optRestrict.msg = "5R 초과는 당일판이 불가합니다.";
		    optRestrict.unchecked(idx);
		    return false;
                }
		*/

                if (basisweight > 90) {
                    //$(this).prop("disabled", true);

                    optRestrict.msg = "평량 90g이상은 당일판이 불가합니다.";
                    optRestrict.unchecked(idx);
                    return false;
                }

                //$(this).prop("disabled", false);

                if (size === "A4" || size === "8절") {
                    if (amt === 0.5 || amt === 3.0) {
                        optRestrict.msg = size + "사이즈 " +
                                          amt + "R은 당일판이 불가합니다.";

                        if (alertFlag === false) {
                            optRestrict.alertFlag = alertFlag;
                            optRestrict.msg = '';
                        }
                        optRestrict.unchecked(idx);
                        return false;
                    }
                }
            }


            return true;
        }
    },
    "포장방법" : {
        "common" : function(dvs, idx) {
            var prefix = getPrefix(dvs);
            var size = $(prefix + "size > option:selected").text();

            // A1, A2, 2절 별도포장 안됨
	    if (size === "A1" ||
                    size === "A2" ||
                    size === "2절") {
                optRestrict.msg = size + " 사이즈는 별도포장이 불가합니다.";
                optRestrict.unchecked(idx);
                return false;
	    }
        }
    }
};
