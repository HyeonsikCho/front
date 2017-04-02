<?

//공지사항 리스트 - 메인 요약 리스트
function makeNoticeSummary($rs) {
  
    if (!$rs) {
        return false;
    }

    $rs_html = "";
    $html  = "\n    <li><a href=\"/cscenter/notice_view.html?seqno=%s\" target=\"_self\">%s</a></li> ";

    while ($rs && !$rs->EOF) {

        $rs_html .= sprintf($html,
                $rs->fields["notice_seqno"],
                $rs->fields["title"]);
        
        $rs->moveNext();

    }

    return $rs_html;

}


//공지사항 리스트 - 일반
function makeNoticeListHtml($rs, $param) {
 
    if (!$rs) {
        return false;
    }

    $today = date("Y-m-d");

    $rs_html = "";
    $html  = "\n  <tr class='%s'>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class=\"subject\"><a href=\"/cscenter/notice_view.html?seqno=%s\" target=\"_self\">%s</a></td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n  </tr>";

    while ($rs && !$rs->EOF) {

        $class = "";
        $regi_date = date("Y-m-d", strtotime($rs->fields["regi_date"]));

        if ($today == $regi_date) {
            $class = "new";
        }

        $rs_html .= sprintf($html, $class, 
                $rs->fields["notice_seqno"],
                $rs->fields["notice_seqno"],
                $rs->fields["title"],
                "관리자",
                $regi_date,
                number_format($rs->fields["hits"]));
        
        $rs->moveNext();
    }

    return $rs_html;
}

//공지사항 리스트 - 공지
function makeNotiListHtml($rs) {
 
    if (!$rs) {
        return false;
    }

    $today = date("Y-m-d");

    $rs_html = "";
    $html  = "\n  <tr class='%s'>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class=\"subject\"><span class=\"%s\">%s</span><a href=\"/cscenter/notice_view.html?seqno=%s\" target=\"_self\"> %s</a></td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n  </tr>";

    while ($rs && !$rs->EOF) {
        if ($rs->fields["dvs"] != 0) {
            $class = "";
            $regi_date = date("Y-m-d", strtotime($rs->fields["regi_date"]));

            if ($today == $regi_date) {
                $class = "new";
            }

            $dvs = "";
            $dvs_class = "";
            if ($rs->fields["dvs"] == 1) {
                $dvs = "[호환성문제]";
                $dvs_class = "important";
            } else if ($rs->fields["dvs"] == 2) {
                $dvs = "[긴급]";
                $dvs_class = "alert";
            }

            $rs_html .= sprintf($html, $class, 
                    $rs->fields["notice_seqno"],
                    $dvs_class,
                    $dvs,
                    $rs->fields["notice_seqno"],
                    $rs->fields["title"],
                    "관리자",
                    $regi_date,
                    number_format($rs->fields["hits"]));
            $rs->moveNext();
        }
    }
    return $rs_html;
}
?>
