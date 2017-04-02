<?
//FAQ 리스트
function makeFAQListHtml($rs, $param) {
 
    if (!$rs) {
        return false;
    }

    $rs_html = "";
    $html  = "\n  <li class=\"\" onclick=\"viewFAQ(this);\"><span class=\"no\">%s</span>";
    $html .= "\n    <dl>";
    $html .= "\n      <dt>[%s] %s</dt>";
    $html .= "\n      <dd style=\"display:none;\"><p>%s</p></dd>";
    $html .= "\n    </dl>";
    $html .= "\n  </li>";

    while ($rs && !$rs->EOF) {

        $rs_html .= sprintf($html, 
                            $rs->fields["faq_seqno"],
                            $rs->fields["sort"],
                            $rs->fields["title"],
                            $rs->fields["cont"]);
        $rs->moveNext();
    }

    return $rs_html;
}
?>
