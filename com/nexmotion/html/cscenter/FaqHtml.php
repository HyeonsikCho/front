<?
//FAQ 리스트
function makeFAQListHtml($rs, $param) {
 
    if (!$rs) {
        return false;
    }

    $rs_html = "";
    $html  = "\n  <li class=\"\" onclick=\"viewFAQ(%s, this);\"><span class=\"no\">%s</span>";
    $html .= "\n    <dl>";
    $html .= "\n      <dt>[%s] %s</dt>";
    $html .= "\n      <dd style=\"display:none;\"><p>%s</p></dd>";
    $html .= "\n    </dl>";
    $html .= "\n  </li>";
    $i = $param[count];

    while ($rs && !$rs->EOF) {

        $rs_html .= sprintf($html, 
                            $rs->fields["faq_seqno"],
                            $i,
                            $rs->fields["sort"],
                            $rs->fields["title"],
                            $rs->fields["cont"]);
        $i--;
        $rs->moveNext();
    }

    return $rs_html;
}

//고객센터 메인페이지 FAQ 리스트 TOP 10
function makeFAQTopListHtml($rs) {
 
    if (!$rs->fields) {
        $blank = "<h3>등록된 FAQ가 없습니다.</h3>";
        return $blank;
    }

    $rs_html = "<ol>";
    $html .= "\n  <li><span class=\"number\">%s</span><a onclick=\"viewFAQ(%s)\">%s<a></li>";
    $i = 1;

    while ($rs && !$rs->EOF) {

        $rs_html .= sprintf($html, 
                            $i,
                            $rs->fields["faq_seqno"],
                            $rs->fields["title"]);

        if ($i == 5)    $rs_html .= "</ol><ol>";
        
        $i++;
        $rs->moveNext();
    }

    $rs_html .= "</ol>";

    return $rs_html;
}
?>
