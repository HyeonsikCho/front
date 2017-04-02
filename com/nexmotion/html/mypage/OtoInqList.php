<?
/**
 * @brief 1:1문의 리스트 HTML
 */
function makeOtoInquireListHtml($rs, $param) {

    $rs_html = "";
    $html  = "\n  <tr class='%s'>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class=\"subject\"><a style=\"cursor:pointer;\" onclick=\"ftfView(%s); return false;\">%s</a></td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class=\"status\">%s</td>";
    $html .= "\n  </tr>";

    while ($rs && !$rs->EOF) {

        $class = "";
        $answ_yn = "";
        if ($rs->fields["answ_yn"] == "Y") {
            $answ_yn = "<img src=\"/design_template/images/mypage/text_question_answered.png\" alt=\"답변완료\">";
        } else {
            $class .= "waiting ";
            $answ_yn = "<img src=\"/design_template/images/mypage/text_question_waiting.png\" alt=\"답변대기\">";
        }
 
        //오늘날짜보다 차이 1일차면 new
        if ( time() - strtotime($rs->fields["reply_date"]) < 60*60*24*1 ) 
            $class .= "new";


        $inq_date = "-";
        if ($rs->fields["inq_date"]) {
            $inq_date = date("Y-m-d", strtotime($rs->fields["inq_date"]));
        }

        $reply_date = "-";
        if ($rs->fields["reply_date"]) {
            $reply_date = date("Y-m-d", strtotime($rs->fields["reply_date"]));
        }

        $rs_html .= sprintf($html
                            , $class
                            , $rs->fields["oto_inq_seqno"]
                            , $inq_date
                            , $rs->fields["inq_typ"]
                            , $rs->fields["oto_inq_seqno"]
                            , $rs->fields["title"]
                            , $reply_date
                            , $answ_yn);
        $rs->moveNext();
    }

    return $rs_html;
}

function makePreEventListHtml($rs, $param) {

    $rs_html = "";
    $html  = "\n  <tr class='%s'>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class=\"subject\"><a style=\"cursor:pointer;\" onclick=\"preView(%s); return false;\">%s</a></td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class=\"status\">%s</td>";
    $html .= "\n  </tr>";

    while ($rs && !$rs->EOF) {
        $class = "";
        $inq_typ = explode('_', $rs->fields["inq_typ"])[0];
        $answ_yn = "";

        if ($rs->fields["answ_yn"] == "Y") {
            $answ_yn = "<img src=\"/design_template/images/mypage/text_question_answered.png\" alt=\"답변완료\">";
        } else {
            $class .= "waiting ";
            $answ_yn = "<img src=\"/design_template/images/mypage/text_question_waiting.png\" alt=\"답변대기\">";
        }
 
        //오늘날짜보다 차이 1일차면 new
        if ( time() - strtotime($rs->fields["reply_date"]) < 60*60*24*1 ) 
            $class .= "new";


        $inq_date = "-";
        if ($rs->fields["inq_date"]) {
            $inq_date = date("Y-m-d", strtotime($rs->fields["inq_date"]));
        }

        $reply_date = "-";
        if ($rs->fields["reply_date"]) {
            $reply_date = date("Y-m-d", strtotime($rs->fields["reply_date"]));
        }

        $rs_html .= sprintf($html
                            , $class
                            , $rs->fields["oto_inq_seqno"]
                            , $inq_date
                            , $inq_typ
                            , $rs->fields["oto_inq_seqno"]
                            , $rs->fields["title"]
                            , $reply_date
                            , $answ_yn);
        $rs->moveNext();
    }

    return $rs_html;
}
?>
