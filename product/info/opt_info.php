<?
// 옵션 체크박스 생성
$param["cate_sortcode"] = $sortcode_b;
$param["dvs"]           = $dvs;
$opt = $dao->selectCateOptHtml($conn, $param);
$template->reg("opt", $opt["html"]); 

// 옵션 가격 레이어 생성
$template->reg("add_opt", ''); 
if (empty($opt["info_arr"]) === false) {
    $add_opt = $opt["info_arr"]["name"];
    $add_opt = $dao->parameterArrayEscape($conn, $add_opt);
    $add_opt = $frontUtil->arr2delimStr($add_opt);

    $param["cate_sortcode"] = $sortcode_b;
    $param["opt_name"]      = $add_opt;
    $param["opt_idx"]       = $opt["info_arr"]["idx"];
    $add_opt = $dao->selectCateAddOptInfoHtml($conn, $param);
    unset($param);
    $template->reg("add_opt", $add_opt); 
}
?>
