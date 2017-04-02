<?
/**
 * @brief 1:1문의 리스트 HTML
 */
function makeBusinessRegistrationListHtml($rs, $param) {

    $rs_html = "";
    $html  = "\n  <tr class='waiting'>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n    <td class='address'>%s</td>";
    $html .= "\n    <td>%s</td>";
    $html .= "\n  </tr>";

    while ($rs && !$rs->EOF) {

        $btn = "<button class=\"modify\" onclick=\"layerPopup('l_businessRegistration', 'popup/pop_registration_edit.html?seq=".$rs->fields["admin_licenseeregi_seqno"]."');\">수정/삭제</button>";

        $rs_html .= sprintf($html
                            , $rs->fields["admin_licenseeregi_seqno"]
                            , $rs->fields["crn"]
                            , $rs->fields["corp_name"]
                            , $rs->fields["repre_name"]
                            , $rs->fields["tel_num"]
                            , $rs->fields["addr"] . " " . $rs->fields["addr_detail"]
                            , $btn);
        $rs->moveNext();
    }

    return $rs_html;
}
?>
