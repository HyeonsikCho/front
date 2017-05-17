/**
 * @brief 카테고리별 후공정 제약사항 스크립트
 */
var aftRestrict = {
    "msg" : '',
    "all" : function(dvs) {
        var ret = true;
        aftRestrict.msg = '';

        $("input[name='" + dvs + "_chk_after[]']").each(function() {
            if ($(this).prop("checked") === false) {
                return true;
            }

            aft = $(this).attr("aft");
            funcObj = aftRestrict[aft];

            if (checkBlank(funcObj) === true) {
                return true;
            }

            ret = funcObj.common(dvs);

            if (ret === false) {
                ret = false;
                alert(aftRestrict.msg);
                setAfterPrice(dvs, aft, 0);
                return true;
            }
        });

        return ret;
    },
    "unchecked" : function(dvs, aft) {
        var obj = getPrefix(dvs) + aft;
        $(obj).prop("checked", false);
        $(obj).trigger("click");
	quickEstiAftHide(aft);
    },
    // 코팅
    "coating"    : {
        "common" : function(dvs) {
            aftRestrict.msg = '';

            // 공통제약사항 >>>
            var prefix = getPrefix(dvs);
            var paper = $(prefix + "paper > option:selected").text();
            paper = paper.split(' ');
            var basisweight = parseInt(paper[paper.length - 1]);
            var cutWid = parseInt($(prefix + "cut_wid_size").val());

            if (basisweight === 150) {
                this.util(dvs);
            } else if (basisweight >= 180) {
                $(prefix + "coating_val > option").show();
            }

            var aft = $(prefix + "coating_val > option:selected").text();

            if (basisweight === 150 && aft.indexOf("양면") > -1) {
                aftRestrict.msg = "양면코팅은 평량 180g 이상에서만 가능합니다.";
                aftRestrict.unchecked(dvs, "coating");
                return false;
            } else if (basisweight < 150) {
                aftRestrict.msg = "코팅은 평량 150g 이상에서만 가능합니다.";
                aftRestrict.unchecked(dvs, "coating");
                return false;
            }

            var sortcodeB = $(prefix + "cate_sortcode").val();
            var sortcodeT = sortcodeB.substr(0, 3);
            var sortcodeM = sortcodeB.substr(0, 6);

            var ret = true;
            if (!checkBlank(this[sortcodeT])) {
                ret = this[sortcodeT](dvs);
            } else if (!checkBlank(this[sortcodeM])) {
                ret = this[sortcodeM](dvs);
            } else if (!checkBlank(this[sortcodeB])) {
                ret = this[sortcodeB](dvs);
            }

            if (!ret) {
                aftRestrict.unchecked(dvs, "coating");
            }

            return ret;
        },
        "util" : function(dvs) {
            // 공통제약사항중 낮은 평량에서 단면코팅 감춤
            var prefix = getPrefix(dvs);
	    var tmp = null;
	    var flag = true;

            $(prefix + "coating_val > option").each(function() {
                var dep = $(this).text();

                if (dep.indexOf("양면") > -1) {
                    $(this).hide();
		} else if (flag) {
                    tmp = $(this).val();
                    flag = false;
		}
            });

            $(prefix + "coating_val").val(tmp);
        }
    },
    // 오시
    "impression" : {
        "common" : function(dvs) {
            aftRestrict.msg = '';

            var ret = true;

            var prefix = getPrefix(dvs);
            var sortcodeB = $(prefix + "cate_sortcode").val();
            var sortcodeT = sortcodeB.substr(0, 3);
            var sortcodeM = sortcodeB.substr(0, 6);

            if (!checkBlank(this[sortcodeT])) {
                ret = this[sortcodeT](dvs);
            } else if (!checkBlank(this[sortcodeM])) {
                ret = this[sortcodeM](dvs);
            } else if (!checkBlank(this[sortcodeB])) {
                ret = this[sortcodeB](dvs);
            }

            if (!ret) {
                aftRestrict.unchecked(dvs, "impression");
                return ret;
            }

            // 공통제약사항 >>>
            // 평량 121g 이상, 오시간 20mm 이상
            // 라미넥스랑 같이 불가능
            var paper = $(prefix + "paper > option:selected").text();
            paper = paper.split(' ');
            var basisweight = parseInt(paper[paper.length - 1]);

            if(basisweight < 121) {
                aftRestrict.msg = "오시는 평량 121g 이상에서만 가능합니다.";
                aftRestrict.unchecked(dvs, "impression");
                return false;
            }

            $("input[name='" + dvs + "_chk_after[]']").each(function() {
                if ($(this).prop("checked") === false) {
                    return true;
                }

                var aft = $(this).val();

                if (aft === "라미넥스") {
                    aftRestrict.msg = "오시는 라미넥스와 같이할 수 없습니다.";
                    ret = false;
                    return false;
                }
            });

            if (!ret) {
                aftRestrict.unchecked(dvs, "impression");
                return false;
            }

            var val    = $(prefix + "impression_cnt").val();
            var preVal = null;

            $('.' + dvs + "_impression_" + val + "_mm").each(function() {
                if ($(this).prop("readonly")) {
                    return false;
                }
                if (checkBlank($(this).val())) {
                    return false;
                }

                var nowVal = Math.abs(parseInt($(this).val()));

                if (nowVal > cutWid) {
                    nowVal = cutWid;
                    $(this).val(cutWid);
                }

                if (preVal === null) {
                    preVal = nowVal;
                    return true;
                }

                if (Math.abs(nowVal - preVal) < 20) {
                    aftRestrict.msg = "오시간 간격은 20mm 이상입니다.";

                    var gap = 0;
                    if (cutWid < nowVal + 20) {
                        gap = preVal - 20;
                    } else {
                        gap = nowVal + 20;
                    }

                    $(this).val(gap);
                    return false;
                }
            });
            preview.impression();
            // <<<

            return ret;
        },
        "004003003" : function(dvs) {
            var prefix = getPrefix(dvs);
            var paper = $(prefix + "paper > option:selected").text();
            paper = paper.split(' ');
            var basisweight = parseInt(paper[paper.length - 1]);

            if(basisweight < 180) {
                aftRestrict.msg = "오시는 평량 180g 이상에서만 가능합니다.";
                aftRestrict.unchecked(dvs, "impression");
                return false;
            }

	    return true;
        }
    },
    // 미싱
    "dotline"    : {
        "common" : function(dvs) {
            aftRestrict.msg = '';

            var ret = true;

            // 공통제약사항 >>>
            // 평량 121g 이상, 미싱간 20mm 이상
            // 라미넥스랑 같이 불가능
            var prefix = getPrefix(dvs);
            var cutWid = parseInt($(prefix + "cut_wid_size").val());

            var sortcodeB = $(prefix + "cate_sortcode").val();
            var sortcodeT = sortcodeB.substr(0, 3);
            var sortcodeM = sortcodeB.substr(0, 6);

            if (!checkBlank(this[sortcodeT])) {
                ret = this[sortcodeT](dvs);
            } else if (!checkBlank(this[sortcodeM])) {
                ret = this[sortcodeM](dvs);
            } else if (!checkBlank(this[sortcodeB])) {
                ret = this[sortcodeB](dvs);
            }

            if (!ret) {
                aftRestrict.unchecked(dvs, "dotline");
                return false;
            }

            $("input[name='" + dvs + "_chk_after[]']").each(function() {
                if ($(this).prop("checked") === false) {
                    return true;
                }

                var aft = $(this).val();

                if (aft === "라미넥스") {
                    aftRestrict.msg = "미싱은 라미넥스와 같이할 수 없습니다.";
                    ret = false;
                    return false;
                }
            });

            if (!ret) {
                aftRestrict.unchecked(dvs, "dotline");
                return false;
            }

            var val    = $(prefix + "dotline_cnt").val();
            var preVal = null;

            $('.' + dvs + "_dotline_" + val + "_mm").each(function() {
                if ($(this).prop("readonly")) {
                    return false;
                }

                if (checkBlank($(this).val())) {
                    return false;
                }

                var nowVal = Math.abs(parseInt($(this).val()));

                if (nowVal > cutWid) {
                    nowVal = cutWid;
                    $(this).val(cutWid);
                }

                if (preVal === null) {
                    preVal = nowVal;
                    return true;
                }

                if (Math.abs(nowVal - preVal) < 20) {
                    aftRestrict.msg = "미싱간 간격은 20mm 이상입니다.";

                    var gap = 0;
                    if (cutWid < preVal + 20) {
                        gap = preVal - 20;
                    } else {
                        gap = preVal + 20;
                    }

                    $(this).val(gap);
                    return false;
                }
            });
            preview.dotline();
            // <<<

            return ret;
        }
    },
    // 접지
    "foldline"   : {
        "common" : function(dvs) {
            aftRestrict.msg = '';

            var prefix = getPrefix(dvs);
            var paper = $(prefix + "paper > option:selected").text();
            paper = paper.split(' ');
            var basisweight = parseInt(paper[paper.length - 1]);
            var ret = true;

            // 공통제약사항 >>>
            // 평량 250g 이상이고 양면코팅이면 접지불가
            // 라미넥스랑 같이 불가능
            if ($(prefix + "foldline").prop("checked") &&
                    $(prefix + "laminex").prop("checked")) {
                aftRestrict.msg = "접지는 라미넥스와 같이할 수 없습니다.";
                aftRestrict.unchecked(dvs, "foldline");
                return false;
            }

            if (250 <= basisweight) {
                if (!$(prefix + "coating").prop("checked")) {
                    return true;
                }

                var coating = $(prefix + "coating_val > option:selected").text();

                if (coating.indexOf("양면") > -1) {
                    aftRestrict.msg = "평량 250g 이상이며 양면코팅인 종이는 접지가 불가능 합니다.";
                    return false;
                }
            }
            // <<<

            var prefix = getPrefix(dvs);
            var sortcodeB = $(prefix + "cate_sortcode").val();
            var sortcodeT = sortcodeB.substr(0, 3);
            var sortcodeM = sortcodeB.substr(0, 6);

            if (!checkBlank(this[sortcodeT])) {
                ret = this[sortcodeT](dvs);
            } else if (!checkBlank(this[sortcodeM])) {
                ret = this[sortcodeM](dvs);
            } else if (!checkBlank(this[sortcodeB])) {
                ret = this[sortcodeB](dvs);
            }

            return ret;
        },
        "003003001" : function(dvs) {
            // 80mm부터 가능
            var prefix = getPrefix(dvs);
            var pos = parseInt($(prefix + "foldline_info").val());
            var aftD1 = $(prefix + "foldline_dvs").val();
            var aftD2 = $(prefix + "foldline_val > option:selected").text();

            if (aftD1 === "2단접지" && aftD2 === "비중앙") {
                if (isNaN(pos) || pos < 80) {
                    return alertReturnFalse("2단접지 비중앙은 80mm 부터 가능합니다.");
                }
            }

            return true;
        }
    },
    // 도무송
    "thomson"    : {
        "common" : function(dvs) {
            aftRestrict.msg = '';

            var ret = true;

	    // 공통제약사항 >>>
            var prefix = getPrefix(dvs);
            var wid  = $(prefix + "thomson_wid").val();
            var vert = $(prefix + "thomson_vert").val();

            if (checkBlank(wid)) {
                wid  = 10;
                $(prefix + "thomson_wid").val(wid);
            }

            if (checkBlank(vert)) {
                vert = 10;
                $(prefix + "thomson_vert").val(vert);
            }

            wid  = parseInt(wid);
            vert = parseInt(vert);

            if (wid < 10) {
                //$(prefix + "thomson_wid").val(10);
                aftRestrict.msg = "도무송 최소사이즈는 10*10 입니다.";
                return false;
            }

            if (vert < 10) {
                //$(prefix + "thomson_vert").val(10);
                aftRestrict.msg = "도무송 최소사이즈는 10*10 입니다.";
                return false;
            }
            // <<<
            
            var prefix = getPrefix(dvs);
            var sortcodeB = $(prefix + "cate_sortcode").val();
            var sortcodeT = sortcodeB.substr(0, 3);
            var sortcodeM = sortcodeB.substr(0, 6);

            if (!checkBlank(this[sortcodeT])) {
                ret = this[sortcodeT](dvs, sortcode);
            } else if (!checkBlank(this[sortcodeM])) {
                ret = this[sortcodeM](dvs, sortcode);
            } else if (!checkBlank(this[sortcodeB])) {
                ret = this[sortcodeB](dvs, sortcode);
            }

            if (!ret) {
                aftRestrict.unchecked(dvs, "thomson");
            }

            return ret;
        }
    },
    // 재단
    "cutting"    : {
        "common" : function(dvs) {
            aftRestrict.msg = '';
            var ret = true;

            // 공통제약사항 >>>
            // 40mm 이하 재단불가
            // 라미넥스랑 같이 불가능
            var prefix = getPrefix(dvs);
            var cutWid  = parseInt($(prefix + "cut_wid_size").val());
            var cutVert = parseInt($(prefix + "cut_vert_size").val());

            if (cutWid < 40 || cutVert < 40) {
                if (cutWid < 40) {
                    $(prefix + "cut_wid_size").val(40);
                }
                if (cutVert < 40) {
                    $(prefix + "cut_vert_size").val(40);
                }

                if (!$(prefix + "cutting").prop("checked")) {
                    return ret;
                }

                aftRestrict.msg = "재단은 40mm 이상에서만 가능합니다.";
                aftRestrict.unchecked(dvs, "cutting");

                return false;
            }

            $("input[name='" + dvs + "_chk_after[]']").each(function() {
                if ($(this).prop("checked") === false) {
                    return true;
                }

                var aft = $(this).val();

                if (aft === "라미넥스") {
                    aftRestrict.msg = "재단은 라미넥스와 같이할 수 없습니다.";
                    ret = false;
                    return false;
                }
            });

            if (!ret) {
                aftRestrict.unchecked(dvs, "cutting");
                return false;
            }
            // <<<

            var sortcodeB = $(prefix + "cate_sortcode").val();
            var sortcodeT = sortcodeB.substr(0, 3);
            var sortcodeM = sortcodeB.substr(0, 6);

            if (!checkBlank(this[sortcodeT])) {
                ret = this[sortcodeT](dvs, sortcode);
            } else if (!checkBlank(this[sortcodeM])) {
                ret = this[sortcodeM](dvs, sortcode);
            } else if (!checkBlank(this[sortcodeB])) {
                ret = this[sortcodeB](dvs, sortcode);
            }

            if (!ret) {
                aftRestrict.unchecked(dvs, "cutting");
            }

            return ret;
        }
    },
    "lotterysilk" : {
        "common" : function(dvs) {
            aftRestrict.msg = '';

            var ret = true;

            // 공통제약사항 >>>
            // 500매 이상
            var prefix = getPrefix(dvs);
            // <<<

            var prefix = getPrefix(dvs);
            var sortcodeB = $(prefix + "cate_sortcode").val();
            var sortcodeT = sortcodeB.substr(0, 3);
            var sortcodeM = sortcodeB.substr(0, 6);

            if (!checkBlank(this[sortcodeT])) {
                ret = this[sortcodeT](dvs, sortcode);
            } else if (!checkBlank(this[sortcodeM])) {
                ret = this[sortcodeM](dvs, sortcode);
            } else if (!checkBlank(this[sortcodeB])) {
                ret = this[sortcodeB](dvs, sortcode);
            }

            if (!ret) {
                aftRestrict.unchecked(dvs, "impression");
            }

            return ret;
        }
    },
    // 라미넥스
    "laminex"     : {
        "min" : 10,
        "max" : 1000,
        "common" : function(dvs) {
            aftRestrict.msg = '';

            var min = aftRestrict.laminex.min;
            var max = aftRestrict.laminex.max;

            var ret = true;

            // 공통제약사항 >>>
            // A3, A4, 8절, 16절만 가능
            // 오시, 미싱, 재단, 접지 선택되어있을경우 불가능
            var prefix    = getPrefix(dvs);
            var aftPrefix = getPrefix(dvs) + 'laminex_';
            var size      = $(prefix + "size > option:selected").text();
            var paper     = $(prefix + "paper > option:selected").text();

            var aftStr = '';
            $("input[name='" + dvs + "_chk_after[]']").each(function() {
                if ($(this).prop("checked") === false) {
                    return true;
                }

                var aft = $(this).val();

                if (aft === "미싱" || aft === "오시" ||
                        aft === "재단" || aft === "접지") {
                    aftStr += aft + ', ';
                    ret = false;
                }
            });

            if (paper === "모조지 백색 70g" || paper === "모조지 백색 80g") {
                aftRestrict.msg = "라미넥스는 " + paper + " 에 추가할 수 없습니다..";
                aftRestrict.unchecked(dvs, "laminex");
                return false;
            }

            if (!ret) {
                aftStr = aftStr.substr(0, aftStr.length - 2);
                aftRestrict.msg = "라미넥스는 " + aftStr + " 후공정과 같이할 수 없습니다.";
                aftRestrict.unchecked(dvs, "laminex");
                return false;
            }

            if (paper === "아트지 백색 90g") {
                if (size !== "A3" && size !== "A4" &&
                        size !== "8절" && size !== "16절") {
                    aftRestrict.msg = paper + " 종이는 A3, A4, 8절, 16절 사이즈만 가능합니다.";
                    aftRestrict.unchecked(dvs, "laminex");
                    return false;
                }
	    }

            var amt = $(aftPrefix + "info").val();

            amt = parseInt(amt);

            if (amt < min) {
                aftRestrict.msg = "라미넥스 최소수량은 " + min.format() + "장입니다.";
                //aftRestrict.unchecked(dvs, "laminex");
                $(aftPrefix + "info").val(min);
                getLaminexPrice("laminex", dvs);
                return true;
            }
            if (amt > max) {
                aftRestrict.msg = "라미넥스 최대수량은 " + max.format() + "장입니다.";
                //aftRestrict.unchecked(dvs, "laminex");
                $(aftPrefix + "info").val(max);
                getLaminexPrice("laminex", dvs);
                return true;
            }
            // <<<

            var prefix = getPrefix(dvs);
            var sortcodeB = $(prefix + "cate_sortcode").val();
            var sortcodeT = sortcodeB.substr(0, 3);
            var sortcodeM = sortcodeB.substr(0, 6);

            if (!checkBlank(this[sortcodeT])) {
                ret = this[sortcodeT](dvs, sortcode);
            } else if (!checkBlank(this[sortcodeM])) {
                ret = this[sortcodeM](dvs, sortcode);
            } else if (!checkBlank(this[sortcodeB])) {
                ret = this[sortcodeB](dvs, sortcode);
            }

            if (!ret) {
                aftRestrict.unchecked(dvs, "laminex");
            }

            return ret;
        }
    },
    // 접착
    "bonding" : {
        "common" : function(dvs) {
            aftRestrict.msg = '';

            var prefix = getPrefix(dvs);

            var ret = true;
            var sortcodeB = $(prefix + "cate_sortcode").val();
            var sortcodeT = sortcodeB.substr(0, 3);
            var sortcodeM = sortcodeB.substr(0, 6);

            if (!checkBlank(this[sortcodeT])) {
                ret = this[sortcodeT](dvs);
            } else if (!checkBlank(this[sortcodeM])) {
                ret = this[sortcodeM](dvs);
            } else if (!checkBlank(this[sortcodeB])) {
                ret = this[sortcodeB](dvs);
            }

            return ret;
        },
        "004003006" : function(dvs) {
            var prefix    = getPrefix(dvs);
            var aftPrefix = getPrefix(dvs) + 'laminex_';
            var size      = $(prefix + "size > option:selected").text();

            if (size.indexOf("7번") > -1) {
                aftRestrict.msg = size + " 사이즈는 접착이 불가합니다.";
                aftRestrict.unchecked(dvs, "bonding");
                return false;
            }

            return true;
        }
    }
};
