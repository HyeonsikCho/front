<?
function makeTomsonSizeTypOption(&$rs, $default) {
    $ret = '';

    while ($rs && !$rs->EOF) {
        $typ = $rs->fields["typ"];
        $attr = '';

        if ($typ === $default) {
            $attr = "selected=\"selected\"";
        }

        $ret .= option($typ, $typ, $attr);
        
        $rs->MoveNext();
    }

    return $ret;
}
?>
