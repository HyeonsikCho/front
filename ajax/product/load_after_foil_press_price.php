<?
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/entity/FormBean.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/ConnectionPool.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/common/util/FrontCommonUtil.php");
include_once($_SERVER["DOCUMENT_ROOT"] . "/com/nexmotion/job/common/ProductCommonDAO.php");

$connectionPool = new ConnectionPool();
$conn = $connectionPool->getPooledConnection();

$util = new FrontCommonUtil();
$dao = new ProductCommonDAO();
$fb = new FormBean();

const FRONT     = 1; // 전면
const BACK      = 2; // 후면
const BOTH      = 3; // 양면같음
const BOTH_DIFF = 4; // 양면다름

$cate_sortcode = $fb->form("cate_sortcode");
$aft_dvs       = $fb->form("aft");
$amt           = intval($fb->form("amt"));
$aft_1         = $fb->form("aft_1");
$dvs_1         = $fb->form("dvs_1");
$aft_2         = $fb->form("aft_2");
$dvs_2         = $fb->form("dvs_2");
$wid_1         = intval($fb->form("wid_1"));
$vert_1        = intval($fb->form("vert_1"));
$wid_2         = intval($fb->form("wid_2"));
$vert_2        = intval($fb->form("vert_2"));

$is_bl = false;
if (substr($cate_sortcode, 0, 3) === "003") {
    $is_bl = true;
}

$dvs = chkDvs($aft_1, $dvs_1, $aft_2, $dvs_2);

$param = array();
$param["cate_sortcode"] = $cate_sortcode;
$param["amt"]           = $amt;

$json_form = "{\"price\" : \"%s\", \"val_1\" : \"%s\", \"val_2\" : \"%s\"}";

//$conn->debug = 1;

// 박인지 형압인지 구분
// 박일 때 depth1 값을 기준으로 after_name 찾아야됨
if ($aft_dvs === "foil") {
    if ($wid_1 < 20) {
        $wid_1  = 20;
    }
    if ($vert_1 < 20) {
        $vert_1 = 20;
    }

    $temp = array();
    $temp["cate_sortcode"] = $cate_sortcode;
    $temp["after_name"] = '박';
    $bef_foil_mpcode = null;
    $aft_foil_mpcode = null;

    if (!empty($aft_1)) {
        $temp["depth1"] = $aft_1;
        $bef_foil_mpcode = $dao->selectCateAfterInfo($conn, $temp)
                               ->fields["mpcode"];
    }
    if (!empty($aft_2)) {
        $temp["depth1"] = $aft_2;
        $aft_foil_mpcode = $dao->selectCateAfterInfo($conn, $temp)
                               ->fields["mpcode"];
    }
    unset($temp);

    $sum = 0;

    if ($dvs === BOTH) {
        // 양면같음
        $param["after_name"] = getFoilAfterName($aft_1);
        $param["dvs"]        = "양면";

        $price = $dao->selectAfterFoilPressPrice($conn, $param);

        $wid_1  = calcAreaVal($wid_1, $amt, $is_bl);
        $vert_1 = calcAreaVal($vert_1, $amt, $is_bl);

        $sum = $price + $wid_1 + $vert_1;
    } else if ($dvs === FRONT) {
        // 전면만
        $param["after_name"] = getFoilAfterName($aft_1);
        $param["dvs"]        = "단면";

        $price = $dao->selectAfterFoilPressPrice($conn, $param);

        $wid_1  = calcAreaVal($wid_1, $amt, $is_bl);
        $vert_1 = calcAreaVal($vert_1, $amt, $is_bl);

        $sum = $price + $wid_1 + $vert_1;
    } else if ($dvs === BACK) {
        // 후면만
        $param["after_name"] = getFoilAfterName($aft_2);
        $param["dvs"]        = "단면";

        $price = $dao->selectAfterFoilPressPrice($conn, $param);

        $wid_2  = calcAreaVal($wid_2, $amt, $is_bl);
        $vert_2 = calcAreaVal($vert_2, $amt, $is_bl);

        $sum = $price + $wid_2 + $vert_2;
    } else if ($dvs === BOTH_DIFF) {
        // 양면다름
        $param["after_name"] = getFoilAfterName($aft_1);
        $param["dvs"]        = "단면";

        $bef_price = $dao->selectAfterFoilPressPrice($conn, $param);

        $param["after_name"] = getFoilAfterName($aft_2);

        $aft_price = $dao->selectAfterFoilPressPrice($conn, $param);

        $wid_1  = calcAreaVal($wid_1, $amt, $is_bl);
        $vert_1 = calcAreaVal($vert_1, $amt, $is_bl);
        $wid_2  = calcAreaVal($wid_2, $amt, $is_bl);
        $vert_2 = calcAreaVal($vert_2, $amt, $is_bl);

        $sum = $bef_price + $aft_price + $wid_1 + $vert_1 + $wid_2 + $vert_2;
    } else {
        echo -1;
        exit;
    }

    echo sprintf($json_form, $sum
                           , $bef_foil_mpcode
                           , $aft_foil_mpcode);
} else {
    if ($wid_1 < 20) {
        $wid_1  = 20;
    }
    if ($vert_1 < 20) {
        $vert_1 = 20;
    }

    $param["after_name"] = "형압";
    $param["dvs"]        = "단면";

    $price = $dao->selectAfterFoilPressPrice($conn, $param);

    $wid_1  = calcAreaVal($wid_1, $amt, $is_bl);
    $vert_1 = calcAreaVal($vert_1, $amt, $is_bl);

    $sum = $price + $wid_1 + $vert_1;

    echo sprintf($json_form, $sum
                           , ''
                           , '');
}

$conn->Close();
exit;

/**
 * @brief 박일 때 금박유광 이런식으로 넘어오는 이름을 금박만 반환
 *
 * @param $aft = 넘어온 후공정명 
 *
 * @return 잘라낸 후공정명
 */
function getFoilAfterName($aft) {
    if (strpos($aft, "금박") !== false) {
        return "금박";
    } else if (strpos($aft, "녹박") !== false) {
        return "녹박";
    } else if (strpos($aft, "먹박") !== false) {
        return "먹박";
    } else if (strpos($aft, "은박") !== false) {
        return "은박";
    } else if (strpos($aft, "적박") !== false) {
        return "적박";
    } else if (strpos($aft, "청박") !== false) {
        return "청박";
    } else {
        return $aft;
    }
}

/**
 * @brief 각 너비 가중값 계산
 *
 * @param $val   = 너비/높이값
 * @param $amt   = 수량
 * @param $is_bl = 전단이면 true / 아니면 false
 *
 * @return 계산값
 */
function calcAreaVal($val, $amt, $is_bl) {
    $weight = 10;
    if ($is_bl) {
        $weight = 0.7;
    }

    return (($val / 10) - 2) * $weight * $amt;
}

/**
 * @brief 양면/전면/후면/양면다름 판단
 *
 * @param $aft1 = 앞부분 박 선택값
 * @param $dvs1 = 앞부분 구분 선택값
 * @param $aft2 = 뒷부분 박 선택값
 * @param $dvs2 = 뒷부분 구분 선택값
 *
 * @return 구분값
 */
function chkDvs($aft_1, $dvs_1, $aft_2, $dvs_2) {
    if ($dvs_1 === "양면") {
        return BOTH;
    } else if (!empty($aft_1) && !empty($dvs_1) &&
            empty($aft_2) && empty($dvs_2)) {
        return FRONT;
    } else if (empty($aft_1) && empty($dvs_1) &&
            !empty($aft_2) && !empty($dvs_2)) {
        return BACK;
    } else if (!empty($aft_1) && !empty($dvs_1) &&
            !empty($aft_2) && !empty($dvs_2)) {
        if ($aft_1 === $aft_2) {
            return BOTH;
        }

        return BOTH_DIFF;
    }
}
?>
