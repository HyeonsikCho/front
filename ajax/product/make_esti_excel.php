<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/common/sess_common.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/common/excel/PHPExcel/IOFactory.php');
include_once($_SERVER["DOCUMENT_ROOT"] . "/common_define/common_config.php");
// 여기서 $param 처리
include_once($_SERVER["DOCUMENT_ROOT"] . "/product/common/esti_pop_common.php");

$base_path = $_SERVER["DOCUMENT_ROOT"] . EXCEL_TEMPLATE;
$file_name = null;

$is_booklet = false;
$is_calc    = false;
$is_ply     = false;

$cate_name = $param["cate_name_arr"][0];

if ((strpos($cate_name, "카다로그") !== false) || // 카다로그
        (strpos($cate_name, "책자") !== false)) { // 책자
    $file_name = "esti_sample_catabro";
    $is_booklet = true;
} else if ((strpos($cate_name, "독판전단") !== false) || // 독판전단
        (strpos($cate_name, "하지") !== false) || // 마스터 NCR
        $cate_name === "양식지") { // 마스터 양식지
    $file_name = "esti_sample_calc";
    $is_calc = true;
} else {
    $file_name = "esti_sample_ply";
    $is_ply = true;
}

$input_file = $base_path . $file_name .".xlsx";

$objPHPExcel = PHPExcel_IOFactory::load($input_file);

$sheet = $objPHPExcel->getActiveSheet();

$paper_arr     = $param["paper_arr"];
$size_arr      = $param["size_arr"];
$tmpt_arr      = $param["tmpt_arr"];
$page_arr      = $param["page_arr"];
$amt_arr       = $param["amt_arr"];
$amt_unit_arr  = $param["amt_unit_arr"];
$count_arr     = $param["count_arr"];
$after_arr     = $param["after_arr"];

//! 최상단 공통정보
// 견적일
$sheet->setCellValue("C5", sprintf("견적일: %s년 %s월 %s일", $param["year"]
                                                           , $param["month"]
                                                           , $param["day"]));
// 회원명
$sheet->setCellValue("E6", $param["member_name"]);
// 회원전화번호
$sheet->setCellValue("D7", $param["member_tel"]);
// 회원이메일
$sheet->setCellValue("D8", $param["member_mail"]);
// 사업장소재지
$sheet->setCellValue("K5", $param["addr"] . ' ' . $param["addr_detail"]);
// 상호
$sheet->setCellValue("K6", $param["sell_site"]);
// 대표자성명
$sheet->setCellValue("K7", $param["repre_name"]);
// 대표번호
$sheet->setCellValue("K8", $param["repre_num"]);
// 합계금액
$sheet->setCellValue("K11", "\\ " . $param["pay_price"]);


if ($is_booklet) {
    //! 카탈로그 브로셔

    // 공통 품명
    $sheet->setCellValue("E14", $cate_name);
    // 공통 사이즈
    $sheet->setCellValue("E15", $size_arr[0]);
    // 공통 수량
    $sheet->setCellValue("E16", $amt_arr[0] . $amt_unit_arr[0] . " x " . $count_arr[0] . "건");

    // 표지 재질
    $sheet->setCellValue("E19", $paper_arr[0]);
    // 표지 페이지
    $sheet->setCellValue("E20", $page_arr[0] . 'p');
    // 표지 안쇄도수
    $sheet->setCellValue("E21", $tmpt_arr[0]);
    // 표지 후공정
    $sheet->setCellValue("E22", $after_arr[0]);

    // 내지1 재질
    $sheet->setCellValue("E25", $paper_arr[1]);
    // 내지1 페이지
    $sheet->setCellValue("E26", $page_arr[1] . 'p');
    // 내지1 안쇄도수
    $sheet->setCellValue("E27", $tmpt_arr[1]);
    // 내지1 후공정
    $sheet->setCellValue("E28", $after_arr[1]);

    if (!empty($paper_arr[2])) {
        // 내지2 재질
        $sheet->setCellValue("E31", $paper_arr[2]);
        // 내지2 페이지
        $sheet->setCellValue("E32", $page_arr[2] . 'p');
        // 내지2 안쇄도수
        $sheet->setCellValue("E33", $tmpt_arr[2]);
        // 내지2 후공정
        $sheet->setCellValue("E34", $after_arr[2]);
    }

    if (!empty($paper_arr[3])) {
        // 내지3 재질
        $sheet->setCellValue("E37", $paper_arr[3]);
        // 내지3 페이지
        $sheet->setCellValue("E38", $page_arr[3] . 'p');
        // 내지3 안쇄도수
        $sheet->setCellValue("E39", $tmpt_arr[3]);
        // 내지3 후공정
        $sheet->setCellValue("E40", $after_arr[3]);
    }

    // 종이비
    $sheet->setCellValue("E43", "\\ " . $param["paper_price"]);
    // 출력비
    $sheet->setCellValue("E44", "\\ " . $param["output_price"]);
    // 인쇄비
    $sheet->setCellValue("E45", "\\ " . $param["print_price"]);
    // 후공정비
    $sheet->setCellValue("E46", "\\ " . $param["after_price"]);
    // 옵션비
    $sheet->setCellValue("E47", "\\ " . $param["opt_price"]);
    // 공급가
    $sheet->setCellValue("E48", "\\ " . $param["supply_price"]);
    // 부가세
    $sheet->setCellValue("E49", "\\ " . $param["tax"]);
    // 정상판매가
    $sheet->setCellValue("E50", "\\ " . $param["sell_price"]);
    // 할인금액
    $sheet->setCellValue("E51", "\\ " . $param["sale_price"]);
    // 결제금액
    $sheet->setCellValue("E52", "\\ " . $param["pay_price"]);

    // 담당자명
    $sheet->setCellValue("J54", $param["member_mng"]);
    // 담당자 연락처
    $sheet->setCellValue("K54", $param["member_mng_tel"]);
} else if ($is_calc) {
    //! 계산형 카테고리

    // 품명
    $sheet->setCellValue("E14", $cate_name);
    // 재질
    $sheet->setCellValue("E15", $paper_arr[0]);
    // 사이즈
    $sheet->setCellValue("E16", $size_arr[0]);
    // 안쇄도수
    $sheet->setCellValue("E17", $tmpt_arr[0]);
    // 수량
    $sheet->setCellValue("E18", $amt_arr[0] . $amt_unit_arr[0] . " x " . $count_arr[0] . "건");
    // 후공정
    $sheet->setCellValue("C21", $after_arr[0]);

    // 종이비
    $sheet->setCellValue("E24", "\\ " . $param["paper_price"]);
    // 출력비
    $sheet->setCellValue("E25", "\\ " . $param["output_price"]);
    // 인쇄비
    $sheet->setCellValue("E26", "\\ " . $param["print_price"]);
    // 후공정비
    $sheet->setCellValue("E27", "\\ " . $param["after_price"]);
    // 옵션비
    $sheet->setCellValue("E28", "\\ " . $param["opt_price"]);
    // 주문건수
    $sheet->setCellValue("E29", $count_arr[0] . "건");
    // 공급가
    $sheet->setCellValue("E30", "\\ " . $param["supply_price"]);
    // 부가세
    $sheet->setCellValue("E31", "\\ " . $param["tax"]);
    // 정상판매가
    $sheet->setCellValue("E32", "\\ " . $param["sell_price"]);
    // 할인금액
    $sheet->setCellValue("E33", "\\ " . $param["sale_price"]);
    // 결제금액
    $sheet->setCellValue("E34", "\\ " . $param["pay_price"]);

    // 담당자명
    $sheet->setCellValue("J36", $param["member_mng"]);
    // 담당자 연락처
    $sheet->setCellValue("K36", $param["member_mng_tel"]);
} else {
    //! 그 외

    // 품명
    $sheet->setCellValue("E14", $cate_name);
    // 재질
    $sheet->setCellValue("E15", $paper_arr[0]);
    // 사이즈
    $sheet->setCellValue("E16", $size_arr[0]);
    // 안쇄도수
    $sheet->setCellValue("E17", $tmpt_arr[0]);
    // 수량
    $sheet->setCellValue("E18", $amt_arr[0] . $amt_unit_arr[0] . " x " . $count_arr[0] . "건");
    // 후공정
    $sheet->setCellValue("C21", $after_arr[0]);

    // 인쇄비
    $sheet->setCellValue("E24", "\\" . $param["print_price"]);
    // 후공정비
    $sheet->setCellValue("E25", "\\" . $param["after_price"]);
    // 옵션비
    $sheet->setCellValue("E26", "\\" . $param["opt_price"]);
    // 주문건수
    $sheet->setCellValue("E27", $count_arr[0] . "건");
    // 공급가
    $sheet->setCellValue("E28", "\\" . $param["supply_price"]);
    // 부가세
    $sheet->setCellValue("E29", "\\" . $param["tax"]);
    // 정상판매가
    $sheet->setCellValue("E30", "\\" . $param["sell_price"]);
    // 할인금액
    $sheet->setCellValue("E31", "\\" . $param["sale_price"]);
    // 결제금액
    $sheet->setCellValue("E32", "\\ " . $param["pay_price"]);

    // 담당자명
    $sheet->setCellValue("J34", $param["member_mng"]);
    // 담당자 연락처
    $sheet->setCellValue("K34", $param["member_mng_tel"]);
}

$save_name = uniqid();

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save($base_path . $save_name . ".xlsx");

$objPHPExcel->disconnectWorksheets();
unset($objPHPExcel);

echo $save_name;
?>
