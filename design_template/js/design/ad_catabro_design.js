$(document).ready(function () {
    // number only
    var optionKey = 0,
        numberOnlys = $('input[type=text].mm, input[type=text].page, ._cuttingSize input[type=text], ._workingSize input[type=text]')
    
    numberOnlys.on('keydown', function (key) {
        if (key.keyCode == 16 || key.keyCode == 17 || key.keyCode == 18 || key.keyCode == 91 || (key.keyCode > 36 && key.keyCode < 41) ) {
            optionKey++;
        }
        
        if (!optionKey && !(key.keyCode > 47 && key.keyCode < 58 || key.keyCode > 95 && key.keyCode < 106 || key.keyCode == 8 || key.keyCode == 46 || key.keyCode > 36 && key.keyCode < 41 ) && key.keyCode != 9) {
            return false;
        }
    });
    
    numberOnlys.on('keyup blur', function () {
        if (optionKey > 0) {
            optionKey -= 1;
        } else {
            $(this).val($(this).val().replace(/[^0-9]/g, ""));
        }
    });
    
    $('.contents .after ._folder ul').add('.contents .after .option').add('.contents .price ._folder').css('display', 'block');
    //3depth menu 변경
    $('._productName').on('change', function () {
        var menuText = $(this).children(':selected').text();
        $('ol.location li:last-child option').each(function () {
            this.selected = $(this).text() == menuText;
        });
    });
    
    //기본 접기
    $('.after ._folder').add('.price ._folder').add('.pages > ._folder').slideUp(0);
    
    $('.selection .option ._closed').on('click', function () {
        $(this).closest('.option').find('._folder').stop().animate({'width': '195px'}, 300);
    });
    $('.selection .option ._opened').on('click', function () {
        $(this).closest('.option').find('._folder').stop().animate({'width': '0'}, 300);
    });
    
    if (!$('.selection .option').hasClass('_on')) {
        $('.selection .option ._opened').closest('.option').find('._folder').stop().animate({'width': '0'}, 0);
    }
    
    //!! 표지 후공정 부분 슬라이드 바인딩
    $('.cover_after ._closed').on('click', function () {
        $('.cover_after_list').stop().slideDown(300);
    });
    $('.cover_after ._opened').on('click', function () {
        $('.cover_after_list').stop().slideUp(300);
    });

    //!! 내지 후공정 부분 슬라이드 바인딩
    $('.inner_after ._closed').on('click', function () {
        $('.inner_after_list').stop().slideDown(300);
    });
    $('.inner_after ._opened').on('click', function () {
        $('.inner_after_list').stop().slideUp(300);
    });

    // 옵션 부분 슬라이드 바인딩
    /*
    $('.after ._closed').on('click', function () {
        $('.after ._folder').stop().slideDown(300);
    });
    $('.after ._opened').on('click', function () {
        $('.after ._folder').stop().slideUp(300);
    });
    */

    $('.after .option').slideUp(0);
    
    /*
    $('.pages ._add').on('click', function () {
        $(this).closest('._folding').addClass('_on').children('._folder').stop().slideDown(300);
    });
    $('.pages ._del').on('click', function () {
        $(this).closest('._folding').removeClass('_on').children('._folder').stop().slideUp(300);
    });
    
    // option 보이기
    $('select._toOption').on('change', function () {
        $('._byOption._on').removeClass('_on');
        $('._byOption._' + $(this).children(':selected').text().replace(/\s/g, '').replace(/\(/g, '').replace(/\)/g, '')).addClass('_on');
        size();
    });
    $('select._toOption').each(function () {
        $('._byOption._on').removeClass('_on');
        $('._byOption._' + $(this).children(':selected').text().replace(/\s/g, '').replace(/\(/g, '').replace(/\)/g, '')).addClass('_on');
    });
    */
    
    //!! 일반명함 사이즈 부분 초기화
    size("cover");
    $('.cover_size select').on('change', function () {
        size("cover");
    });
    $('.cover_size ._cuttingSize input[type=text]').on('blur', function() {
        size("cover");
        setInnerSize();

        /*******************************
         * 재단사이즈 입력시 바로 윗 재단사이즈 검색 후
         * 표지 사이즈 변경된걸로 처리해서 가격 검색
         */
        var callback = function(result) {
            if (checkBlank(result.mpcode) === true) {
                return alertReturnFalse("용지 최대크기를 넘겼습니다.");
            }

            $("#cover_size").val(result.mpcode);
            loadSimilarSize("inner", changeSizeCover);
        };

        loadSimilarSize("cover", callback);
    });
    
    //!! 내지 사이즈 부분 초기화
    size("inner");
    $('.inner_size select').on('change', function () {
        size("inner");
    });
    $('.inner_size ._cuttingSize input[type=text]').on('blur', function () {
        size("inner");
    });
    /*
    $('.size .wings ._cutSize input[type=text]').on('blur', function () {
        size();
        calcManuPosNum.exec();
    });
    
    $('.size .wings dd.seneca button').on('click', function () {
        size();
    });
    */
    
    //사이즈 빈칸일 경우
    $('.size input[type=text]').each(function () {
        if ($(this).val() == '') {
            $(this).val(0);
        }
    });
    
    $('.size input[type=text]').on('blur', function () {
        if ($(this).val() == '') {
            $(this).val(0);
        }
    });

    //!! 제품별 후공정 초기화
    aftInit("cover");
    aftInit("inner");

    optOverview();
    
    //수량
    //수량 range bar 설정
    var amountOptions = $('.amount ._amount option');
    if (amountOptions.length !== 0) {
        //수량 select 변경 시 하단 range bar 이동
        $('.contents ._amount').on('change', function () {
            rangeBarBySelect();
        });

        var amountOptionNum = amountOptions.length,
            marks = $('.amount .range .mark'),
            markNum2 = Math.floor((amountOptionNum - 1) / 3),
            markNum3 = Math.floor((amountOptionNum - 1) * 2 / 3),
            optionRange = new Array(amountOptionNum - 1),
            rangeUnit = 100 / (amountOptionNum - 1) / 2,
            rangeAnchor = $('.range ._anchor'),
            rangeBar =  $('.range .hr.on'),
            hrWidth = $('.hr').width(),
            anchorLeft = Number(rangeAnchor.css('left').replace('px', '')),
            selectedOption = $('.contents ._amount option:selected').index('.contents ._amount option'),
            rangeBarTimer;
        // 16.01.07 드래그로 수량변경시 가격검색하기 위해서 추가
        var amt;
        
        optionPosition = new Array(amountOptionNum);
        
        //수량 option별 range bar 구간
        for (var iOption = 0; iOption < amountOptionNum - 1; iOption ++) {
            optionRange[iOption] = hrWidth / 100 * rangeUnit * ((iOption + 1) * 2 - 1);
            optionPosition[iOption] = hrWidth / 100 * rangeUnit * ((iOption + 1) * 2 - 2);
        }
        optionPosition[amountOptionNum - 1] = hrWidth;
        
        //mark 배치
        marks.append('<li>' + $(amountOptions[0]).text() + '</li>');
        marks.append('<li style="left: ' + optionPosition[markNum2] + 'px;">' + $(amountOptions[markNum2]).text() + '</li>');
        marks.append('<li style="left: ' + optionPosition[markNum3] + 'px;">' + $(amountOptions[markNum3]).text() + '</li>');
        marks.append('<li>' + $(amountOptions[amountOptionNum - 1]).text() + '</li>');
        
        //초기화
        rangeBarBySelect();
        
        //anchor drag
        rangeAnchor.draggable({
            addClasses: false,
            axis: 'x',
            containment: '.range .bar',
            cursor: false
        }, {
            start: function() {
                rangeAnchor.addClass('_on');
            },
            drag: function() {
                anchorLeft = Number(rangeAnchor.css('left').replace('px', ''));
                rangeBar.width(anchorLeft);
                amt = selectByRangeBar();
            },
            stop: function() {
                selectByRangeBar();
                
                rangeAnchor.removeClass('_on');
                rangeBarBySelect();
                orderSummary();
                // 가격검색용 함수
                changeData("amt", amt);
            }
        });
        
        //수량 range bar에 따른 수량 선택
        function selectByRangeBar () {
            selectedOption = 0;
            while (optionRange[selectedOption] < anchorLeft) {
                selectedOption += 1;
            }
            var amt;
            $(amountOptions).each(function (i) {
                if (i == selectedOption) {
                    amountOptions[i].selected = true;
                    amt = amountOptions[i].value;
                } else {
                    amountOptions[i].selected = false;
                }
            });
    
            return amt;
        }
    }
    
    //주문 내역 변경
    $('select._relatedSummary, ._amount, ._set, ._size, ._preset, ._toOption').on('change', function () { orderSummary(); });
    $('input[type=checkbox]._relatedSummary, .size .wings dd.seneca button').on('click', function () { orderSummary(); });
    $('._relatedSummary input[type=text]').on('keyup', function () { orderSummary(); });
    
    //가격
    $('.price ._closed').on('click', function () {
        $('.price ._folder').stop();
        $('.price ._folder').slideDown(300);
    });
    $('.price ._opened').on('click', function () {
        $('.price ._folder').stop();
        $('.price ._folder').slideUp(300);
    });
    
    var scrollTop = $(window).scrollTop(),
        scrollLeft = $(window).scrollLeft(),
        headerMargin = $('header.top').outerHeight() + $('header.title').outerHeight() - 10,
        quickEstimate = $('.quickEstimate'),
        quickEstimateHeight = quickEstimate.outerHeight(),
        quickEstimateHiddenHeight,
        baseMargin = 30,
        bodyWidth = $('body').width(),
        bodyHeight = $(document).height(),
        footerHeight = $('footer').outerHeight(),
        bottomDistance,
        windowWidth = $(window).width(),
        windowHeight = $(window).height(),
        topForHidden = 0;

    $(window).resize(function () {
        windowWidth = $(window).width();
        windowHeight = $(window).height();
        bodyWidth = $('body').width(),
        windowScroll();
    });
    
    $(window).scroll(function () {
        windowScroll();
    });
    
    windowScroll();
    
    function windowScroll () {
        //상하
        scrollTop = $(window).scrollTop();
        bodyHeight = $(document).height();
        
        if (scrollTop > headerMargin - baseMargin) { //스크롤이 빠른견적서 상단보다 더 내려갈 경우 top 조정
            quickEstimate.css('top', baseMargin);
        } else {
            quickEstimate.css('top', headerMargin - scrollTop);
        }
        
        quickEstimateHiddenHeight = quickEstimateHeight - windowHeight + baseMargin; //빠른견적서보다 창이 작은 경우
        
        if (quickEstimateHiddenHeight > 0 && scrollTop > headerMargin - baseMargin) {
            if(scrollTop - (headerMargin - baseMargin) > quickEstimateHiddenHeight) {
                topForHidden = quickEstimateHiddenHeight * -1;
                quickEstimate.css('top', topForHidden);
            } else {
                topForHidden = quickEstimate.css('top').replace('px','') - (scrollTop - (headerMargin - baseMargin));
                quickEstimate.css('top', topForHidden);
            }
            quickEstimateHiddenHeight = quickEstimateHiddenHeight + baseMargin;
        } else if (quickEstimateHiddenHeight <= 0) {
            quickEstimateHiddenHeight = 0;
        }
        
        bottomDistance = bodyHeight - (quickEstimateHeight + baseMargin * 2 + scrollTop + footerHeight - quickEstimateHiddenHeight); //하단과의 거리
        if (bottomDistance > 0) {
            quickEstimate.css('margin-top', 0);
        } else {
            quickEstimate.css('margin-top', bottomDistance);
        }
        
        //좌우
        if(windowWidth > bodyWidth) {
            quickEstimate.css('right', 0);
        } else {
            scrollLeft = $(window).scrollLeft();
            quickEstimate.css('right', windowWidth - bodyWidth + scrollLeft);
        }
            
    }
    
    //제품 사진
    $('figure.picture .thumb').on('click', function () {
        $(this).closest('ul').children('._on').removeClass('_on');
        $(this).closest('li').addClass('_on');
    });
    
        //초기화
    $('figure.picture li:first-child .thumb').click();
});

//사이즈
function size (dvs) {
    var size = $('.' + dvs + "_size"),
        sizeSelect = size.find('._size'),
        presetSelect = size.find('._preset'),
        cuttingSize = size.find('._cuttingSize'),
        workingSize = size.find('._workingSize'),
        designSize = size.find('._designSize'),
        thomsonSize = size.find('._thomsonSize'),
        //**************************************!!
        //roomNumber = size.find('._roomNumber'), // 자리수 계산을 위해 추가합니다.
        gap = Number(workingSize.attr('class')
                                .replace('_workingSize', '')
                                .replace('/\s/g', '')
                                .replace('_gap', '')),
        number = new Array(),
        switcher = new Boolean(),
        wings = size.find('.wings')[0] ? size.find('.wings') : false,
        sizeSelected = sizeSelect.children('option:selected'),
        totalW = 0,
        totalH = 0,
        descriptionOption;

    if (!sizeSelected.hasClass('_custom') || sizeSelected.length === 0) {
        //규격사이즈
        cuttingSize.find('input[type=text]').attr('readonly', true);
        presetSelect.addClass('_on');
        //**************************************!!
        //roomNumber.removeClass('_on');

        if (sizeSelected.length === 0 ||
                sizeSelected.attr('class').indexOf('_cuttingWH') == -1) { //preset select에서 선택하는 경우
            number = presetSelect.children('option:selected').attr('class');
        } else { //size select 따라 size가 정해지는 경우
            number = sizeSelected.attr('class');
        }
        
        if (number) {
            number = number.split(' ');
            for (var iCutting = 0; iCutting < number.length; iCutting++) {
                if (number[iCutting].indexOf('_cuttingWH') != -1) {
                    number = number[iCutting].replace('_cuttingWH', '').split('-');
                }
            }
            cuttingSize.find('input[type=text]:eq(0)').val(Number(number[0]));
            cuttingSize.find('input[type=text]:eq(1)').val(Number(number[1]));
            
            totalW += Number(number[0]);
            totalH += Number(number[1]);
        }
    } else {
        presetSelect.removeClass('_on');
        //**************************************!!
        //roomNumber.addClass('_on');
        
        //비규격사이즈
        cuttingSize.find('input[type=text]').attr('readonly', false);
        number[0] = cuttingSize.find('input[type=text]:eq(0)').val();
        number[1] = cuttingSize.find('input[type=text]:eq(1)').val();
            
        totalW += Number(number[0]);
        totalH += Number(number[1]);
    }
    
    //작업 사이즈
    if (sizeSelected.length > 0 &&
            sizeSelected.attr('class').indexOf('_workingWH') != -1) {
        //size select에서 정하는 경우
        number = sizeSelected.attr('class').split(' ');
        for (var iWorking = 0; iWorking < number.length; iWorking ++) {
            if(number[iWorking].indexOf('_workingWH') != -1) {
                number = number[iWorking].replace('_workingWH', '').split('-');
            }
        }
        $('.size dd._workingSize input[type=text]:eq(0)').val(Number(number[0]));
        $('.size dd._workingSize input[type=text]:eq(1)').val(Number(number[1]));
    } else if (presetSelect.hasClass('_on') && presetSelect.children('option:selected').attr('class').indexOf('_workingWH') != -1) {
        //preset select에서 정하는 경우
        number = presetSelect.children('option:selected').attr('class').split(' ');
        for (var iWorking = 0; iWorking < number.length; iWorking ++) {
            if(number[iWorking].indexOf('_workingWH') != -1) {
                number = number[iWorking].replace('_workingWH', '').split('-');
            }
        }
        workingSize.find('input[type=text]:eq(0)').val(Number(number[0]));
        workingSize.find('input[type=text]:eq(1)').val(Number(number[1]));
    } else {
        if (number) {
            workingSize.find('input[type=text]:eq(0)').val(Number(number[0]) + gap);
            workingSize.find('input[type=text]:eq(1)').val(Number(number[1]) + gap);
        }
    }

    //디자인 사이즈
    switcher = false;
    size.find('._size option:selected, select._on option:selected').each(function (i) {
        number = $(this).attr('class');
        if (number) {
            if (number.indexOf('_designWH') != -1) {
                number = number.split(' ');
                for (var iDesign = 0; iDesign < number.length; iDesign++) {
                    if (number[iDesign].indexOf('_designWH') != -1) {
                        number = number[iDesign].replace('_designWH', '').split('-');
                    }
                }
                designSize.find('input[type=text]:eq(0)').attr('readonly', true).val(Number(number[0]));
                designSize.find('input[type=text]:eq(1)').attr('readonly', true).val(Number(number[1]));
                switcher = true;
            }
        }
    });
    designSize.find('input[type=text]').attr('readonly', switcher);
    
    //도무송 사이즈
    switcher = false;
    size.find('._size option:selected, select._on option:selected').each(function (i) {
        number = $(this).attr('class');
        if (number) {
            if (number.indexOf('_thomsonWH') != -1) {
                number = number.split(' ');
                for (var iDesign = 0; iDesign < number.length; iDesign++) {
                    if (number[iDesign].indexOf('_thomsonWH') != -1) {
                        number = number[iDesign].replace('_thomsonWH', '').split('-');
                    }
                }
                thomsonSize.find('input[type=text]:eq(0)').val(Number(number[0]));
                thomsonSize.find('input[type=text]:eq(1)').val(Number(number[1]));
                switcher = true;
            }
        }
    });
    thomsonSize.find('input[type=text]').attr('readonly', switcher);
    
    //날개 사이즈
    if (wings && wings.hasClass('_on')) {
        wings.find('._cutSize').each(function () {
            $(this).find('input[type=text]:eq(0)').val(cuttingSize.find('input[type=text]:eq(0)').val());
            totalW += Number($(this).find('input[type=text]:eq(1)').val());
            $(this).find('input[type=text]:eq(2)').val(cuttingSize.find('input[type=text]:eq(1)').val());
            totalH += Number($(this).find('input[type=text]:eq(3)').val());
        });
        wings.find('._workSize').each(function () {
            $(this).find('input[type=text]:eq(0)').val(Number($(this).prev().prev().find('input[type=text]:eq(0)').val()));
            $(this).find('input[type=text]:eq(1)').val(Number($(this).prev().prev().find('input[type=text]:eq(1)').val()) + gap);
            $(this).find('input[type=text]:eq(2)').val(Number($(this).prev().prev().find('input[type=text]:eq(2)').val()));
            $(this).find('input[type=text]:eq(3)').val(Number($(this).prev().prev().find('input[type=text]:eq(3)').val()) + gap);
        });
    }
    
    //description
    descriptionOption = $('select._size, select._size + select._on').children('option._description:selected');
    $('.size ul._description li._on').removeClass('_on');
    $('.size ul._description li._' + descriptionOption.text().replace(/\s/g, '').replace(/\(/g, '').replace(/\)/g, '')).addClass('_on');
    
    //총 사이즈
    totalW += Number($('.size dd.seneca input[type=text]').val());
    $('.size .total input[type=text]:eq(0)').val(totalW);
    $('.size .total input[type=text]:eq(1)').val(totalH);
}

function wingSize (that) {
    $(that).closest('dd').next().next().find('input[type=text]:eq(' + $(that).closest('dd').find('input[type=text]').index($(that)) + ')').val(Number($(that).val()) + gap);
}

//수량 select에 따른 하단 바 설정
var optionPosition;
function rangeBarBySelect () {
    var value = optionPosition[$('.contents ._amount option:selected').index('.contents ._amount option')];
    
    $('.contents .range ._anchor').css('left', value);
    $('.contents .range .hr.on').css('width', value);
}

//후공정
function afterOverview () {
    var html = '',
        separator = '';

    // 추가 후공정 부분 html 생성
    var afterAddStr = "";

    var afterAddHtml = "";
    $('.aft_sec .option._on dl').each(function () {
        var after = $(this).children('dt').text();

        afterAddStr += after + '|';

        afterAddHtml += '<li>';
        afterAddHtml += after;
        afterAddHtml += ' [';
        $(this).children('dd.price + dd').each(function () {
            $(this).children().each(function () {
                if ($(this).is('select')) {
                    afterAddHtml += separator + $(this).children('option:selected').text();
                } else if ($(this).is('input[type=text]')) {
                    afterAddHtml += separator + $(this).val();
                } /*else if ($(this).is('label')) {
                    if ($(this).children(':first-child').is('input[type=text]')) {
                        html += separator + $(this).val();
                    } else if ($(this).children(':first-child').is('input[type=checkbox]:checked')) {
                        html += separator + $(this).text();
                    } else if ($(this).children(':first-child').is('input[type=radio]:checked')) {
                        html += separator + $(this).text();
                    }
                }*/
                separator = '/';
            });
        });
        afterAddHtml += ']</li> ';
        separator = '';
    });

    // 기본 후공정 추가랑 겹치는지 확인하고 html 생성
    var prdtDvsArr = $("#prdt_dvs").val().split('|');
    var prdtDvsLen = prdtDvsArr.length;

    for (var i = 0; i < prdtDvsLen; i++) {
        var prdtDvs = prdtDvsArr[i];
        var prefix  = '#' + prdtDvs + '_';
        var afterBasic    = $(prefix + "after_basic").val();
        var afterBasicArr = '';
        var afterBasicLen = 0;

        if (checkBlank(afterBasic) === false) {
            afterBasicArr = afterBasic.split('|');
            afterBasicLen = afterBasicArr.length;
        }

        var afterBasicHtml = '';
        for (var j = 0; j < afterBasicLen; j++) {
            var after = afterBasicArr[j];

            if (afterAddStr.indexOf(after) !== -1) {
                continue;
            }

            afterBasicHtml += '<li>';
            afterBasicHtml += after;
            afterBasicHtml += '</li> ';
        }

        var html = afterBasicHtml + afterAddHtml;

        if (html == '') html = '<li>없음</li>';
        $(".after ." + prdtDvs + "_overview ul").html(html);
    }

    orderSummary()
}

// 옵션
function optOverview () {
    var html = '',
        separator = '';

    $("input[name='chk_opt'][disabled='disabled']").each(function () {
        html += '<li>';
        html += $(this).val();
        html += '</li> ';
    });

    $('.opt .option._on dl').each(function () {

        html += '<li>';
        html += $(this).children('dt').text();
        html += ' [';
        $(this).children('dd.price + dd').each(function () {
            $(this).children().each(function () {
                if ($(this).is('select')) {
                    html += separator + $(this).children('option:selected').text();
                } else if ($(this).is('input[type=text]')) {
                    html += separator + $(this).val();
                } /*else if ($(this).is('label')) {
                    if ($(this).children(':first-child').is('input[type=text]')) {
                        html += separator + $(this).val();
                    } else if ($(this).children(':first-child').is('input[type=checkbox]:checked')) {
                        html += separator + $(this).text();
                    } else if ($(this).children(':first-child').is('input[type=radio]:checked')) {
                        html += separator + $(this).text();
                    }
                }*/
                separator = '/';
            });
        });
        html += ']</li> ';
        separator = '';
    });
    if (html == '') html = '<li>없음</li>';
    $('.opt .overview ul').html(html);
    orderSummary()
}

//주문내역
function orderSummary () {
    var html = '',
        afterOverview = $('.after .overview ul li').clone().addClass('after');
    
    $('._relatedSummary').each(function () {
        html += '<li>';
        if ($(this).hasClass('size')) {
	    // 책자형 상품 날개있음으로 변경시
            if ($(this).find('.wings').hasClass('_on')) {
                html += $(this).find('.wings .total input[type=text]:eq(0)').val() + '*' + $(this).find('.wings .total input[type=text]:eq(1)').val();
            } else {
                html += $(this).find('._cuttingSize input[type=text]:eq(0)').val() + '*' + $(this).find('._cuttingSize input[type=text]:eq(1)').val();
            }
        } else if ($(this).hasClass('amount')) {
            html += $(this).find('._amount option:selected').val().format();
            html += $(this).find('._amount').attr("amt_unit");
            // 건수 객체
            var $countObj = $(this).find('._set option:selected');
            if  ($countObj.length !== 0) {
                html += " * " + $countObj.text() + "건";
            }
        } else {
            html += $(this).children('option:selected').text();
        }
         html += '</li> ';
    });
    
    if (afterOverview.text() == '없음') {
        $('.summary ul').html(html)
    } else {
        $('.summary ul').html(html).append(afterOverview);
    }
}

/**
 * @brief 제품별 후공정 초기화
 *
 * @param dvs = 제품구분
 */
function aftInit(dvs) {
    var $aftObj = $('.' + dvs + "_after");
    var $aftListObj = $('.' + dvs + "_after_list");

    $aftListObj.find("input[type=checkbox]").on('click', function () {
        if (this.checked) {
            $aftObj.find('.option.' + $(this).closest('li').attr('class'))
                   .addClass('_on')
                   .slideDown(300);
        } else {
            $aftObj.find('.option.' + $(this).closest('li').attr('class'))
                   .removeClass('_on')
                   .slideUp(300);
        }
            
        afterOverview();
    });
    $aftObj.find('.option select').on('change', function () { afterOverview(); });
    
        //코팅
    $aftObj.find('._coating select._part').on('change', function () {
        if ($(this).find('option:selected').hasClass('_part')) {
            $(this).closest('dl').find('p.note._part').addClass('_on');
        } else {
            $(this).closest('dl').find('p.note._part').removeClass('_on');
        }
    });
    
        //귀도리
    $aftObj.find('._rounding select._num').on('change', function () {
        if ($(this).find('option:selected').hasClass('_all')) {
            $(this).closest('dl').find('input[type=checkbox]').prop('checked', true)
                .on('click', function () {
                alert('네귀도리는 체크를 해제 할 수 없습니다.');
                return false;
            });
        } else {
            $(this).closest('dl').find('input[type=checkbox]').prop('checked', false)
                .off('click');
        }
    });
    
        //오시
    $aftObj.find('._impression select').on('change', function () {
        $aftObj.find('.option._impression dd._on').removeClass('_on');
        $aftObj.find('.option._impression dd.' + $(this).children('option:selected').attr('class')).addClass('_on');
    });
    
    $aftObj.find('._impression dd.' + $aftObj.find('._impression select option:selected').attr('class')).addClass('_on');
    
    $aftObj.find('._impression input._custom').each(function () {
        if ($(this).is(':checked')) {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', false);
        } else {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', true);
        }
    });
    
    $aftObj.find('._impression input[type=radio]').on('click', function () {
        if ($(this).hasClass('_custom')) {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', false);
        } else {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', true);
        }
    });

    //미싱
    $aftObj.find('._dotline select').on('change', function () {
        $aftObj.find('.option._dotline dd._on').removeClass('_on');
        $aftObj.find('.option._dotline dd.' + $(this).children('option:selected').attr('class')).addClass('_on');
    });
    
    $aftObj.find('._dotline dd.' + $aftObj.find('._dotline select option:selected').attr('class')).addClass('_on');
    
    $aftObj.find('._dotline input._custom').each(function () {
        if ($(this).is(':checked')) {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', false);
        } else {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', true);
        }
    });
    
    $aftObj.find('._dotline input[type=radio]').on('click', function () {
        if ($(this).hasClass('_custom')) {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', false);
        } else {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', true);
        }
    });
    
    //타공
    $aftObj.find('._punching select._num').on('change', function () {
        var num = parseInt($(this).val());
        $(this).closest('dl').find('dd.br').each(function (i) {
            if ( num > i) {
                $(this).addClass('_on');
            } else {
                $(this).removeClass('_on');
            }
        })
    });
    
    //접지
    $aftObj.find('._foldline select').on('change', function () {
        $aftObj.find('.option._foldline dd._on').removeClass('_on');
        $aftObj.find('.option._foldline dd.' + $(this).children('option:selected').attr('class')).addClass('_on');
    });
    
    $aftObj.find('._foldline dd.' + $aftObj.find('._foldline select option:selected').attr('class')).addClass('_on');
    
    $aftObj.find('._foldline input._custom').each(function () {
        if ($(this).is(':checked')) {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', false);
        } else {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', true);
        }
    });
    
    $aftObj.find('._foldline input[type=radio]').on('click', function () {
        if ($(this).hasClass('_custom')) {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', false);
        } else {
            $(this).closest('dd').next().find('input[type=text]').attr('readonly', true);
        }
    });
    
    //도무송
    $aftObj.find('._thomson select._type').on('change', function () {
        $(this).closest('dl').find('dd._on').removeClass('_on');
        $(this).closest('dl').find('dd._' + $(this).val()).addClass('_on');
    });
    
    //넘버링
    $aftObj.find('._numbering select._num').on('change', function () {
        var target = $(this);
        $(this).closest('dl').find('dd.br').each(function (i) {
            if (target.find('option').index(target.find('option:selected')) >= i) {
                $(this).addClass('_on');
            } else {
                $(this).removeClass('_on');
            }
        });
        
    });
    
    //재단
    $aftObj.find('._cutting select').on('change', function () {
        if ($(this).val() == 'label') {
            $(this).closest('dl').find('dd.br').addClass('_on');
        } else {
            $(this).closest('dl').find('dd.br').removeClass('_on');
        }
    });
    
    //접착
    $aftObj.find('._bonding select.type').on('change', function () {
        if ($(this).val() == 'bothside') {
            $(this).closest('dl').find('dd._bothside').addClass('_on');
            $(this).closest('dl').find('dd._oneside').removeClass('_on');
        } else {
            $(this).closest('dl').find('dd._bothside').removeClass('_on');
            $(this).closest('dl').find('dd._oneside').addClass('_on');
        }
    });
    
    afterOverview ();
}
