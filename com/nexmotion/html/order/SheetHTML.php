<?
/*
 * Copyright (c) 2015-2016 Nexmotion, Inc.
 * All rights reserved.
 * 
 * REVISION HISTORY (reverse chronological order)
 *=============================================================================
 * 2016/09/02 엄준현 수정(원파일업체 파일업로드 부분 중복되던거 수정)
 * 2016/10/12 엄준현 수정(가격 표시 로직 수정)
 *=============================================================================
 *
 */

/**
 * @param 원파일 업체 주문서 작성 html 생성
 *
 * @param $conn = connection identifier
 * @param $dao  = 후공정, 사진경로 검색용
 * @param $rs   = 검색결과
 * @param $price_info_arr = 가격정보배열
 *
 * return 주문서 작성 html
 */
function makeOnefileOrderListHtml($conn, $dao, $rs, &$price_info_arr) {
    $ret  = "<table class=\"list _details order fileUploads\" id=\"table_1\">";
    $ret .= "    <colgroup>";
    $ret .= "        <col width=\"40\">";
    $ret .= "        <col width=\"80\">";
    $ret .= "        <col width=\"180\">";
    $ret .= "        <col width=\"400\">";
    $ret .= "        <col width=\"90\">";
    $ret .= "        <col width=\"100\">";
    $ret .= "        <col width=\"60\">";
    $ret .= "        <col width=\"50\">";
    $ret .= "    </colgroup>";
    $ret .= "    <thead>";
    $ret .= "        <tr>";
    $ret .= "            <th>번호</th>";
    $ret .= "            <th>담은날</th>";
    $ret .= "            <th>인쇄물제목</th>";
    $ret .= "            <th style=\"width: 390px;\">상품정보</th>";
    $ret .= "            <th>수량(건)</th>";
    $ret .= "            <th>결제예정금액</th>";
    $ret .= "            <th>상세/수정</th>";
    $ret .= "            <th>삭제</th>";
    $ret .= "        </tr>";
    $ret .= "    </thead>";

    $upper_tr  = "<tr id=\"tr_%s\">"; //#0
    $upper_tr .= "    <td>";
    $upper_tr .= "        <span class=\"idx\" idx=\"%s\">%s</span>"; //#1 순번
    $upper_tr .= "        <input type=\"hidden\" name=\"seq[]\" value=\"%s\" />"; //#2 일련번호
    $upper_tr .= "        <input type=\"hidden\" name=\"order_num_%s\" class=\"order_num\" value=\"%s\" />"; //#2-1 주문번호
    $upper_tr .= "    </td>";
    $upper_tr .= "    <td>%s</td>"; //#3 등록일
    $upper_tr .= "    <td  class=\"_name\" id=\"title_td_%s\">%s</td>"; //#4 seqno, title
    $upper_tr .= "    <td class=\"subject\">";
    $upper_tr .= "        <ul class=\"information\">";
    $upper_tr .= "            %s"; //#5 order_detail
    $upper_tr .= "        </ul>";
    $upper_tr .= "    </td>";
    $upper_tr .= "    <td>%s%s(%s건)</td>"; //#6 amt, amt_unit_dvs, count
    $upper_tr .= "    <td>";
    $upper_tr .= "        %s 원"; //#7 sell_price
    $upper_tr .= "    </td>";

    $upper_tr .= "    <td>";
    $upper_tr .= "\n      <button type=\"button\" class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\" onclick=\"openOrderDetail('%s', '%s', '8');\"><img src=\"/design_template/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>"; //#8 번호, seqno
    $upper_tr .= "\n      <button type=\"button\" class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\" onclick=\"closeOrderDetail('%s');\"><img src=\"/design_template/images/common/btn_table_circle_top_green.png\" alt=\"▲\"></button>"; //#9 번호
    $upper_tr .= "    </td>";
    $upper_tr .= "    <td><button type=\"button\" class=\"deleteOrder\" title=\"삭제\" onclick=\"removeOrder('O', '%s');\"><img src=\"/design_template/images/common/btn_circle_x_red.png\" alt=\"X\"></button></td>"; //#10
    $upper_tr .= "    <input type=\"hidden\" name=\"sell_price_%s\" class=\"sell_price\" value=\"%s\" />"; //#11 sell_price
    $upper_tr .= "    <input type=\"hidden\" name=\"grade_sale_price_%s\" class=\"grade_sale_price\" value=\"%s\" />"; //#12 grade_sale_price
    $upper_tr .= "    <input type=\"hidden\" name=\"event_price_%s\" class=\"event_price\"  value=\"%s\" />"; //#13 event_price
    $upper_tr .= "    <input type=\"hidden\" id=\"cate_sortcode_%s\" class=\"cate_sortcode\"  value=\"%s\" />"; //#14 cate_sortcode
    $upper_tr .= "</tr>";

    $lower_tr .= "<tr class=\"_orderDetails\" id=\"detail%s\">";
    $lower_tr .= "</tr>";

    $upload_tbody  = "<tbody class=\"fileUploads\">";
    $upload_tbody .= "    <tr>";
    $upload_tbody .= "        <td colspan=\"9\">";
    $upload_tbody .= "            <table class=\"input\">";
    $upload_tbody .= "                <colgroup>";
    $upload_tbody .= "                    <col width=\"125\">";
    $upload_tbody .= "                    <col>";
    $upload_tbody .= "                </colgroup>";
    $upload_tbody .= "                <tbody>";
    $upload_tbody .= "                    <tr>";
    $upload_tbody .= "                        <th>자사 이미지 사용 여부</th>";
    $upload_tbody .= "                        <td>";
    $upload_tbody .= "                            <label><input type=\"radio\" name=\"owncompany_img_use_yn_1\" value=\"Y\" onclick=\"$('#owncompany_img_num_1').show();\"> 예</label>";
    $upload_tbody .= "                            <input type=\"text\" name=\"owncompany_img_num_1\" id=\"owncompany_img_num_1\" value=\"\" style=\"display:none;\" />";
    $upload_tbody .= "                            <label><input type=\"radio\" name=\"owncompany_img_use_yn_1\" checked=\"checked\" value=\"N\" onclick=\"$('#owncompany_img_num_1').hide().val('');\"> 아니오</label>";
    $upload_tbody .= "                            <span class=\"note\">당사에서 제공하는 이미지를 사용하셨을 경우, “예”로 체크하고 번호를 입력해주시기 바랍니다.</span>";
    $upload_tbody .= "                        </td>";
    $upload_tbody .= "                    </tr>";
    $upload_tbody .= "                    <tr>";
    $upload_tbody .= "                        <th>작업파일 업로드 구분</th>";
    $upload_tbody .= "                        <td>";
    $upload_tbody .= "                            <label><input type=\"radio\" name=\"file_upload_dvs_1\" value=\"Y\" onclick=\"showWorkFileTr(true, '1');\" checked=\"checked\" \"> 직접 업로드</label>";
    $upload_tbody .= "                            <label><input type=\"radio\" name=\"file_upload_dvs_1\" value=\"N\" onclick=\"showWorkFileTr(false, '1');\" \"> 웹하드 업로드</label>";
    $upload_tbody .= "                        </td>";
    $upload_tbody .= "                    </tr>";
    $upload_tbody .= "                    <tr id=\"work_file_tr_1\">";
    $upload_tbody .= "                        <th>작업파일</th>";
    $upload_tbody .= "                        <td>";
    $upload_tbody .= "                            <button type=\"button\" name=\"work_file\" id=\"work_file_1\">파일첨부</button>";
    $upload_tbody .= "                            <span id=\"work_file_list_1\" style=\"font-size: 20px; position: relative; top: 6px; float: right;\"><blink class=\"_blink\">파일을 업로드 해주세요.</blink></span>";
    $upload_tbody .= "                            <input type=\"hidden\" id=\"work_file_seqno_1\" name=\"work_file_seqno[]\" />";
    $upload_tbody .= "                            <input type=\"hidden\" id=\"oper_sys_1\" name=\"oper_sys_1\" class=\"oper_sys\" />";
    $upload_tbody .= "                            <p class=\"note\">후공정 작업파일과 인쇄물 작업파일을 같이 압축해서 올려 주십시오.</p>";
    $upload_tbody .= "                            <p class=\"note\">후공정 작업파일은 한 파일로 작업하여 올려 주십시오.</p>";
    $upload_tbody .= "                        </td>";
    $upload_tbody .= "                    </tr>";

    $upload_tbody .= "                    <tr id=\"webhard_tr_1\" style=\"display:none;\">"; 
    $upload_tbody .= "                        <th>웹하드</th>";
    $upload_tbody .= "                        <td>";
    $upload_tbody .= "                            <button type=\"button\" onclick=\"goWebhardPage();\">웹하드 사이트 열기</button>";
    $upload_tbody .= "                            <p class=\"note\">웹하드 사이트에 로그인하여 파일을 업로드 해주세요.</p>";
    $upload_tbody .= "                            <p class=\"note\">아이디 : dp123, 비밀번호 : 1234 </p>";
    $upload_tbody .= "                        </td>";
    $upload_tbody .= "                    </tr>";

    $upload_tbody .= "                    <tr>";
    $upload_tbody .= "                        <th>고객메모</th>";
    $upload_tbody .= "                        <td>";
    $upload_tbody .= "                            <div class=\"textareaWrap\"><textarea name=\"cust_memo_1\" class=\"memo\" name=\"\"></textarea></div>";
    $upload_tbody .= "                            <p class=\"note\">인쇄 시 작업자가 참고해야 할 사항을 적어주세요.</p>";
    $upload_tbody .= "                        </td>";
    $upload_tbody .= "                    </tr>";
    $upload_tbody .= "                </tbody>";
    $upload_tbody .= "            </table>";
    $upload_tbody .= "        </td>";
    $upload_tbody .= "    </tr>";
    $upload_tbody .= "</tbody>";

    $sum_sell_price       = 0;
    $sum_grade_sale_price = 0;
    $sum_event_price      = 0;
    $sum_add_opt_price    = 0;
    $sum_add_after_price  = 0;

    $i = 1;
    while ($rs && !$rs->EOF) {
        $fields = $rs->fields;

        /*
        $amt          = '';
        $amt_unit_dvs = '';
        $count        = '';
        $order_detail_dvs_num = '';

        $s_amt = doubleval($fields["s_amt"]);
        if (empty($s_amt) === false) {
            if ($s_amt < 1) {
                $amt = number_format($s_amt, 1);
            } else {
                $amt = number_format($s_amt);
            }

            $amt_unit_dvs = $fields["s_amt_unit"];
            $count        = $fields["s_count"];
            $order_detail_dvs_num = $fields["s_order_detail_dvs_num"];
        }

        $b_amt = doubleval($fields["b_amt"]);
        if (empty($b_amt) === false) {
            if (empty($amt) === true) {
                if ($b_amt < 1) {
                    $amt = number_format($b_amt, 1);
                } else {
                    $amt = number_format($b_amt);
                }

                $amt_unit_dvs = $fields["b_amt_unit"];
                $count        = '1';
                $order_detail_dvs_num = $fields["b_order_detail_dvs_num"];
            } else {
                $amt = "혼합형";
            }
        }
        */

        $cate_sortcode = $fields["cate_sortcode"];

        $order_common_seqno = $fields["order_common_seqno"];
        $order_num          = $fields["order_num"];
        $order_regi_date    = $fields["order_regi_date"];
        $title              = $fields["title"];
        $order_detail       = $fields["order_detail"];
        $sell_price         = doubleval($fields["sell_price"]);
        $grade_sale_price   = doubleval($fields["grade_sale_price"]);
        $add_opt_price      = doubleval($fields["add_opt_price"]);
        $add_after_price    = doubleval($fields["add_after_price"]);
        $event_price        = doubleval($fields["event_price"]);
        $expec_weight       = $fields["expec_weight"];
        $amt                = $fields["amt"];
        $amt_unit_dvs       = $fields["amt_unit_dvs"];
        $count              = $fields["count"];

        $calc_sum_price = $sell_price + $add_opt_price + $add_after_price +
                          $grade_sale_price + $event_price;

        // 가격합산 정보 생성
        $sum_sell_price       += $sell_price;
        $sum_grade_sale_price += $grade_sale_price;
        $sum_event_price      += $event_price;
        $sum_add_opt_price    += $add_opt_price;
        $sum_add_after_price  += $add_after_price;

        // 전체 html 생성
        $ret .= "<tbody class=\"info\">";

        $ret .= sprintf($upper_tr, $i                                //#0
                                 , $i                                //#1
                                 , $i                                //#1
                                 , $order_common_seqno               //#2
                                 , $order_common_seqno               //#2-1
                                 , $order_num                        //#2-1
                                 , explode(' ', $order_regi_date)[0] //#3
                                 , $order_common_seqno               //#4
                                 , $title                            //#4
                                 , $order_detail                     //#5
                                 , $amt                              //#6
                                 , $amt_unit_dvs                     //#6
                                 , $count                            //#6
                                 , number_format($calc_sum_price)    //#7
                                 , $i                                //#8
                                 , $order_common_seqno               //#8
                                 , $i                                //#9
                                 , $i                                //#10
                                 , $order_common_seqno               //#11
                                 , $calc_sum_price                   //#11
                                 , $order_common_seqno               //#12
                                 , $grade_sale_price                 //#12
                                 , $order_common_seqno               //#13
                                 , $event_price                      //#13
                                 , $order_common_seqno			     //#14
                                 , $cate_sortcode                    //#14
                                 , $order_common_seqno);             //#15

        $ret .= sprintf($lower_tr, $i++);

        $ret .= "</tbody>";

        $rs->MoveNext();
    }

    $ret .= $upload_tbody;

    $ret .= "</table>";

    $price_info_arr["sell"]  = $sum_sell_price +
                               $sum_add_after_price +
                               $sum_add_opt_price;
    $price_info_arr["grade"] = $sum_grade_sale_price;
    $price_info_arr["event"] = $sum_event_price;
    $price_info_arr["sum"] = $sum_sell_price +
                             $sum_add_opt_price +
                             $sum_add_after_price +
                             $sum_grade_sale_price +
                             $sum_event_price;

    return $ret;
}

/**
 * @param 개별파일 주문서 작성 html 생성
 *
 * @param $conn = connection identifier
 * @param $dao  = 후공정, 사진경로 검색용
 * @param $rs   = 검색결과
 * @param $price_info_arr = 가격정보배열
 *
 * return 주문서 작성 html
 */
function makeEachfileOrderListHtml($conn, $dao, $rs, &$price_info_arr) {
    $ret = "";

    $table_base  = "<table class=\"list _details order fileUploads\" id=\"table_%s\">";
    $table_base .= "    <colgroup>";
    $table_base .= "        <col width=\"40\">";
    $table_base .= "        <col width=\"80\">";
    $table_base .= "        <col width=\"180\">";
    $table_base .= "        <col widrh=\"400\">";
    $table_base .= "        <col width=\"90\">";
    $table_base .= "        <col width=\"100\">";
    $table_base .= "        <col width=\"60\">";
    $table_base .= "        <col width=\"50\">";
    $table_base .= "    </colgroup>";
    $table_base .= "    <thead>";
    $table_base .= "        <tr>";
    $table_base .= "            <th>번호</th>";
    $table_base .= "            <th>담은날</th>";
    $table_base .= "            <th>인쇄물제목</th>";
    $table_base .= "            <th style=\"width: 390px;\">상품정보</th>";
    $table_base .= "            <th>수량(건)</th>";
    $table_base .= "            <th>결제예정금액</th>";
    $table_base .= "            <th>상세/수정</th>";
    $table_base .= "            <th>삭제</th>";
    $table_base .= "        </tr>";
    $table_base .= "    </thead>";
    $table_base .= "    %s";
    $table_base .= "</table>";

    $upper_tr  = "<tr>";
    $upper_tr .= "    <td>";
    $upper_tr .= "        <span class=\"idx\" idx=\"%s\">%s</span>"; //#1 순번
    $upper_tr .= "        <input type=\"hidden\" name=\"seq[]\" value=\"%s\" />"; //#2 순번
    $upper_tr .= "        <input type=\"hidden\" name=\"order_num_%s\" value=\"%s\" />"; //#2-1 주문번호
    $upper_tr .= "    </td>";
    $upper_tr .= "    <td>%s</td>"; //#3 등록일
    $upper_tr .= "    <td  class=\"_name\" id=\"title_td_%s\">%s</td>"; //#4 seqno, title
    $upper_tr .= "    <td class=\"subject\">";
    $upper_tr .= "        <ul class=\"information\">";
    $upper_tr .= "            %s"; //#5 order_detail
    $upper_tr .= "        </ul>";
    $upper_tr .= "    </td>";
    $upper_tr .= "    <td>%s%s(%s건)</td>"; //#6 amt, amt_unit_dvs, count
    $upper_tr .= "    <td>";
    $upper_tr .= "        %s 원"; //#7 sell_price
    $upper_tr .= "    </td>";

    $upper_tr .= "    <td>";
    $upper_tr .= "\n      <button type=\"button\" class=\"viewOrderDetails _showOrderDetails _on\" title=\"상세보기\" onclick=\"openOrderDetail('%s', '%s', '8');\"><img src=\"/design_template/images/common/btn_table_circle_bottom.png\" alt=\"▼\"></button>"; //#8 번호, seqno
    $upper_tr .= "\n      <button type=\"button\" class=\"viewOrderDetails _hideOrderDetails\" title=\"상세접기\" onclick=\"closeOrderDetail('%s');\"><img src=\"/design_template/images/common/btn_table_circle_top_green.png\" alt=\"▲\"></button>"; //#9 번호
    $upper_tr .= "    </td>";
    $upper_tr .= "    <td><button type=\"button\" class=\"deleteOrder\" title=\"삭제\" onclick=\"removeOrder('E', '%s');\"><img src=\"/design_template/images/common/btn_circle_x_red.png\" alt=\"X\"></button></td>"; //#10 idx
    $upper_tr .= "    <input type=\"hidden\" name=\"sell_price_%s\" class=\"sell_price\" value=\"%s\" />"; //#11 sell_price
    $upper_tr .= "    <input type=\"hidden\" name=\"grade_sale_price_%s\" class=\"grade_sale_price\" value=\"%s\" />"; //#12 grade_sale_price
    $upper_tr .= "    <input type=\"hidden\" name=\"event_price_%s\" class=\"event_price\" value=\"%s\" />"; //#13 event_price
	$upper_tr .= "    <input type=\"hidden\" id=\"cate_sortcode_%s\" class=\"cate_sortcode\" value=\"%s\" />"; //#14 cate_sortcode
    $upper_tr .= "</tr>";

    $lower_tr .= "<tr class=\"_orderDetails\" id=\"detail%s\">";
    $lower_tr .= "</tr>";

    $upload_tbody  = "<tbody class=\"fileUploads\">";
    $upload_tbody .= "    <tr>";
    $upload_tbody .= "        <td colspan=\"9\">";
    $upload_tbody .= "            <table class=\"input\">";
    $upload_tbody .= "                <colgroup>";
    $upload_tbody .= "                    <col width=\"125\">";
    $upload_tbody .= "                    <col>";
    $upload_tbody .= "                </colgroup>";
    $upload_tbody .= "                <tbody>";
    $upload_tbody .= "                    <tr>";
    $upload_tbody .= "                        <th>자사 이미지 사용 여부</th>";
    $upload_tbody .= "                        <td>";
    $upload_tbody .= "                            <label><input type=\"radio\" name=\"owncompany_img_use_yn_%s\" onclick=\"$('#owncompany_img_num_%s').show();\"> 예</label>"; //#1
    $upload_tbody .= "                            <input type=\"text\" name=\"owncompany_img_num_%s\" id=\"owncompany_img_num_%s\" value=\"\" style=\"display:none;\" />"; //#1-1
    $upload_tbody .= "                            <label><input type=\"radio\" name=\"owncompany_img_use_yn_%s\" checked=\"checked\" onclick=\"$('#owncompany_img_num_%s').hide().val('');\"> 아니오</label>"; //#2
    $upload_tbody .= "                            <span class=\"note\">당사에서 제공하는 이미지를 사용하셨을 경우, “예”로 체크하고 번호를 입력해주시기 바랍니다.</span>";
    $upload_tbody .= "                        </td>";
    $upload_tbody .= "                    </tr>";
    $upload_tbody .= "                    <tr>";
    $upload_tbody .= "                        <th>작업파일 업로드 구분</th>";
    $upload_tbody .= "                        <td>";
    $upload_tbody .= "                            <label><input type=\"radio\" name=\"file_upload_dvs_%s\" value=\"Y\" onclick=\"showWorkFileTr(true, '%s');\" checked=\"checked\" \"> 직접 업로드</label>"; //#12
    $upload_tbody .= "                            %s"; //#13 webhard
    $upload_tbody .= "                        </td>";
    $upload_tbody .= "                    </tr>";
    $upload_tbody .= "                    <tr id=\"work_file_tr_%s\">"; //#14
    $upload_tbody .= "                        <th>작업파일</th>";
    $upload_tbody .= "                        <td>";
    $upload_tbody .= "                            <button type=\"button\" name=\"work_file\" id=\"work_file_%s\">파일첨부</button>"; //#7
    $upload_tbody .= "                            <span id=\"uploaded_work_file_list_%s\" style=\"font-size: 20px; position: relative; top: 6px; float: right;\">%s</span>"; //#9
    $upload_tbody .= "                            <span id=\"work_file_list_%s\" style=\"font-size: 20px; position: relative; top: 6px; float: right;\"><blink class=\"_blink\">파일을 업로드 해주세요.</blink></span>"; //#9-1
    $upload_tbody .= "                            <input type=\"hidden\" id=\"work_file_seqno_%s\" name=\"work_file_seqno[]\" />"; //#10
    $upload_tbody .= "                            %s";
    $upload_tbody .= "                            <p class=\"note\">후공정 작업파일과 인쇄물 작업파일을 같이 압축해서 올려 주십시오.</p>";
    $upload_tbody .= "                            <p class=\"note\">후공정 작업파일은 한 파일로 작업하여 올려 주십시오.</p>";
    $upload_tbody .= "                        </td>";
    $upload_tbody .= "                    </tr>";

    $upload_tbody .= "                    <tr id=\"webhard_tr_%s\" style=\"display:none;\">"; #15
    $upload_tbody .= "                        <th>웹하드</th>";
    $upload_tbody .= "                        <td>";
    $upload_tbody .= "                            <button type=\"button\" onclick=\"goWebhardPage();\">웹하드 사이트 열기</button>";
    $upload_tbody .= "                            <p class=\"note\">웹하드 사이트에 로그인하여 파일을 업로드 해주세요.</p>";
    $upload_tbody .= "                            <p class=\"note\">아이디 : dp123, 비밀번호 : 1234 </p>";
    $upload_tbody .= "                        </td>";
    $upload_tbody .= "                    </tr>";

    $upload_tbody .= "                    <tr>";
    $upload_tbody .= "                        <th>고객메모</th>";
    $upload_tbody .= "                        <td>";
    $upload_tbody .= "                            <div class=\"textareaWrap\"><textarea class=\"memo\" name=\"cust_memo_%s\"></textarea></div>"; //#11
    $upload_tbody .= "                            <p class=\"note\">인쇄 시 작업자가 참고해야 할 사항을 적어주세요.</p>";
    $upload_tbody .= "                        </td>";
    $upload_tbody .= "                    </tr>";
    $upload_tbody .= "                </tbody>";
    $upload_tbody .= "            </table>";
    $upload_tbody .= "        </td>";
    $upload_tbody .= "    </tr>";
    $upload_tbody .= "</tbody>";

    $webhard_html = "                            <label><input type=\"radio\" name=\"file_upload_dvs_%s\" value=\"N\" onclick=\"showWorkFileTr(false, '%s');\" \"> 웹하드 업로드</label>"; //#13

    $sum_sell_price       = 0;
    $sum_grade_sale_price = 0;
    $sum_event_price      = 0;
    $sum_add_opt_price    = 0;
    $sum_add_after_price  = 0;

    $i = 1;
    while ($rs && !$rs->EOF) {
        $fields = $rs->fields;

        $cate_sortcode = $fields["cate_sortcode"];

        $order_common_seqno = $fields["order_common_seqno"];
        $order_num          = $fields["order_num"];
        $order_regi_date    = $fields["order_regi_date"];
        $title              = $fields["title"];
        $order_detail       = $fields["order_detail"];
        $sell_price         = doubleval($fields["sell_price"]);
        $grade_sale_price   = doubleval($fields["grade_sale_price"]);
        $add_after_price    = doubleval($fields["add_after_price"]);
        $add_opt_price      = doubleval($fields["add_opt_price"]);
        $event_price        = doubleval($fields["event_price"]);
        $expec_weight       = $fields["expec_weight"];
        $amt                = $fields["amt"];
        $amt_unit_dvs       = $fields["amt_unit_dvs"];
        $count              = $fields["count"];

        // 주문_파일 목록 검색
        $param = array();
        $param["order_common_seqno"] = $order_common_seqno;
        $param["dvs"]                = '1';
        $order_file_rs = $dao->selectOrderFileList($conn, $param);

        $oper_sys = makeOperSysHtml($order_file_rs->fields,
                                    $i,
                                    $order_common_seqno);

        $uploaded_file = makeUploadedFileHtml($order_file_rs,
                                              $i,
                                              $order_common_seqno);

        unset($order_file_rs);

        $calc_sum_price = $sell_price + $add_opt_price + $add_after_price +
                          $grade_sale_price + $event_price;

        // 가격합산 정보 생성
        $sum_sell_price       += $sell_price;
        $sum_grade_sale_price += $grade_sale_price;
        $sum_event_price      += $event_price;
        $sum_add_opt_price    += $add_opt_price;
        $sum_add_after_price  += $add_after_price;

        // 명함, 스티커일 때 웹하드 업로드 안함
        $sortcode_t = substr($cate_sortcode, 0, 3);
        $sortcode_m = substr($cate_sortcode, 0, 6);
        if ($sortcode_t === "001" || $sortcode_t === "002" ||
                $sortcode_m === "003001") {
            $webhard = '';
        } else {
            $webhard = sprintf($webhard_html, $order_common_seqno
                                            , $i);
        }

        // tbody html 생성
        $temp  = "<tbody class=\"info\">";

        $temp .= sprintf($upper_tr, $i                                //#1
                                  , $i                                //#1
                                  , $order_common_seqno               //#2
                                  , $order_common_seqno               //#2-1
                                  , $order_num                        //#2-1
                                  , explode(' ', $order_regi_date)[0] //#3
                                  , $order_common_seqno               //#4
                                  , $title                            //#4
                                  , $order_detail                     //#5
                                  , $amt                              //#6
                                  , $amt_unit_dvs                     //#6
                                  , $count                            //#6
                                  , number_format($calc_sum_price)    //#7
                                  , $i                                //#8
                                  , $order_common_seqno               //#8
                                  , $order_common_seqno               //#9
                                  , $i                                //#10
                                  , $order_common_seqno               //#11
                                  , $calc_sum_price                   //#11
                                  , $order_common_seqno               //#12
                                  , $grade_sale_price                 //#12
                                  , $order_common_seqno               //#13
                                  , $event_price                      //#13
  								  , $order_common_seqno			      //#14
								  , $cate_sortcode                    //#14
  								  , $order_common_seqno);             //#15

        $temp .= sprintf($lower_tr, $i);
        $temp .= "</tbody>";
        $temp .= sprintf($upload_tbody, $order_common_seqno  //#1
                                      , $order_common_seqno  //#1
                                      , $order_common_seqno  //#1-1
                                      , $order_common_seqno  //#1-1
                                      , $order_common_seqno  //#2
                                      , $order_common_seqno  //#2
                                      , $order_common_seqno  //#12
                                      , $i                   //#12
                                      , $webhard             //#13
                                      , $i                   //#14
                                      , $i                   //#7
                                      , $i                   //#9
                                      , $uploaded_file       //#9
                                      , $i                   //#9-1
                                      , $i                   //#10
                                      , $oper_sys            //#10-1
                                      , $i                   //#15
                                      , $order_common_seqno); //#11

        // 전체 html 생성
        $ret .= sprintf($table_base, $i++, $temp);

        $rs->MoveNext();
    }


    $price_info_arr["sell"]  = $sum_sell_price +
                               $sum_add_after_price +
                               $sum_add_opt_price;
    $price_info_arr["grade"] = $sum_grade_sale_price;
    $price_info_arr["event"] = $sum_event_price;
    $price_info_arr["sum"] = $sum_sell_price +
                             $sum_add_opt_price +
                             $sum_add_after_price +
                             $sum_grade_sale_price +
                             $sum_event_price;

    return $ret;
}

/**
 * @brief 운영체제값 파라미터 생성
 *
 * @param $rs                 = 검색결과
 * @param $idx                = 테이블 위치 인덱스
 * @param $order_common_seqno = 주문공통 일련번호
 *
 * @return html
 */
function makeOperSysHtml($fields, $idx, $order_common_seqno) {
    $oper_sys = '';
    $ret = "<input type=\"hidden\" id=\"oper_sys_%s\" name=\"oper_sys_%s\" class=\"oper_sys\" value=\"%s\" />";
    if (empty($fields["origin_file_name"])) {
        return sprintf($ret, $idx, $order_common_seqno, $oper_sys);
    }

    $ext = explode('.', $fields["origin_file_name"]);
    $ext = strtolower(array_pop($ext));

    if ($ext === "sit") {
        $oper_sys = "MAC";
    } else {
        $oper_sys = "IBM";
    }

    return sprintf($ret, $idx, $order_common_seqno, $oper_sys);
}

/**
 * @brief 업로드 된 작업파일 html 생성
 *
 * @param $rs                 = 검색결과
 * @param $idx                = 테이블 위치 인덱스
 *
 * @return html
 */
function makeUploadedFileHtml($rs, $idx) {
    $ret = '';

    $html_form  = "<div class=\"uploaded_work_file\">";
    $html_form .= "    %s (%s)&nbsp;<span style=\"font-weight:bold;\">100%%</span>"; //#1
    $html_form .= "    <img src=\"/design_template/images/common/btn_circle_x_red.png\"";
    $html_form .= "         id=\"uploaded_work_file_del_%s\""; //#2
    $html_form .= "         alt=\"X\"";
    $html_form .= "         onclick=\"removeFile('%s', '%s', 1);\""; //#3
    $html_form .= "         file_seqno=\"%s\""; //#4
    $html_form .= "         style=\"cursor:pointer; top:-3px; position:relative;\" />";
    $html_form .= "</div>";

    while ($rs && !$rs->EOF) {
        $fields = $rs->fields;
        $file_seqno = $fields["order_file_seqno"];
        $file_name  = $fields["origin_file_name"];
        $file_size  = intval($fields["size"]);

        if ($file_size < 1024) {
            $file_size = ceil($file_size) . " b";
        } else if (1024 <= $file_size && $file_size < 1048576) {
            $file_size = ceil($file_size / 1024) . " Kb";
        } else if (1048576 <= $file_size && $file_size < 1073741824) {
            $file_size = ceil($file_size / 1048576) . " Mb";
        } else {
            $file_size = ceil($file_size / 1073741824) . " Gb";
        }

        $ret .= sprintf($html_form, $file_name   //#1
                                  , $file_size   //#1
                                  , $idx         //#2
                                  , $idx         //#2
                                  , $file_seqno  //#3
                                  , $file_seqno);//#4
        $rs->MoveNext();
    }

    return $ret;
}
?>
