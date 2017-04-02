<?
//공유자료실
function makeFileListHtml($rs, $param) {
 
    if (!$rs) {
        return false;
    }

    $today = date("Y-m-d");

    $rs_html = "";
    $html  = "\n  <tr class='%s'>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class=\"subject\"><a href=\"/service/file_view.html?seqno=%s\" target=\"_self\">%s</a></td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n  </tr>";
    $i = $param["count"] - $param["s_num"];

    while ($rs && !$rs->EOF) {

        $class = "";
        $regi_date = date("Y-m-d", strtotime($rs->fields["regi_date"]));

        if ($today == $regi_date) {
            $class = "new";
        }

        $rs_html .= sprintf($html, $class, 
                $i,
                $rs->fields["share_library_seqno"],
                $rs->fields["title"],
                $rs->fields["member_name"],
                $regi_date,
                $rs->fields["hits"]);
        $i--;
        $rs->moveNext();
    }

    return $rs_html;
}
?>
