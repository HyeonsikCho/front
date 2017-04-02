<?
include_once($_SERVER["DOCUMENT_ROOT"] . '/com/nexmotion/html/common/MakeCommonHtml.php');

/*! 공통 DAO Class */
class CommonDAO { 

    var $errorMessage = "";

    function __construct() {
    }

    /** 
     * @brief 다중 데이터 수정 쿼리 함수 (공통) <br>
     *        param 배열 설명 <br>
     *        $param : <br>
     *        $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "수정데이터" (다중)<br>
     *        $param["prk"] = "primary key colulm"<br>
     *        $param["prkVal"] = "primary data"  ex) 1,2,3,4
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */ 
    function updateMultiData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" || 
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $prkArr = str_replace(" ", "", $param["prkVal"]);
        $prkArr = str_replace("'", "", $prkArr); 
        $prkArr = explode(",", $prkArr);

        $parkVal = "";

        for ($i = 0; $i < count($prkArr); $i++) {
            $prkVal .= $conn->qstr($prkArr[$i], get_magic_quotes_gpc()) . ","; 
        }
        $prkVal = substr($prkVal, 0, -1);

        $query = "\n UPDATE " . $param["table"]  . " set";

        $i = 0;
        $col = "";
        $value = "";

        while (list($key, $val) = each($param["col"])) {

            $inchr = $conn->qstr($val,get_magic_quotes_gpc());

            if ($i == 0) {
                $value  .= "\n " . $key . "=" . $inchr;
            } else {
                $value  .= "\n ," . $key . "=" . $inchr;
            }

            $i++;
        }

        $query .= $value;
        $query .= " WHERE " . $param["prk"] . " in(";
        $query .= $prkVal . ")";

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 수정에 실패 하였습니다.";
            return false;
        } else { 
            return true;
        }

    } 

    /** 
     * @brief 데이터 수정 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "수정데이터" (다중)<br>
     *        $param["prk"] = "primary key colulm"<br>
     *        $param["prkVal"] = "primary data" <br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */ 
    function updateData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" || 
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query = "\n UPDATE " . $param["table"]  . " set";

        $i = 0;
        $col = "";
        $value = "";

        while (list($key, $val) = each($param["col"])) {

            //   $inchr = $val;
            $inchr = $conn->qstr($val,get_magic_quotes_gpc());

            if ($i == 0) {
                $value  .= "\n " . $key . "=" . $inchr;
            } else {
                $value  .= "\n ," . $key . "=" . $inchr;
            }

            $i++;
        }

        $query .= $value;
        $query .= " WHERE " . $param["prk"] . "=" . $conn->qstr($param["prkVal"], get_magic_quotes_gpc());

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            return false;
        } else {
            return true;
        }
    }

    /** 
     * @brief 데이터 삽입 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "데이터" (다중)<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */ 
    function insertData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" || 
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query = "\n INSERT INTO " . $param["table"] . "(";

        $i = 0;
        $col = "";
        $value = "";

        while (list($key, $val) = each($param["col"])) {

            $inchr = $conn->qstr($val,get_magic_quotes_gpc());

            if ($i == 0) {
                $col  .= "\n " . $key;
                $value  .= "\n " . $inchr;
            } else {
                $col  .= "\n ," . $key;
                $value  .= "\n ," . $inchr;
            }

            $i++;
        }

        $query .= $col;
        $query .= "\n ) VALUES (";
        $query .= $value;
        $query .= "\n )";

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 입력에 실패 하였습니다.";
            return false;
        } else {
            return true;
        }
    }

    /** 
     * @brief 데이터 삽입/수정 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"]["컬럼명"] = "데이터" (다중)<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */ 
    function replaceData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" || 
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query = "\n INSERT INTO " . $param["table"] . "(";

        $i = 0;
        $col = "";
        $value = "";

        while (list($key, $val) = each($param["col"])) {

            $inchr = $conn->qstr($val,get_magic_quotes_gpc()); 
            if ($i == 0) {
                $col  .= "\n " . $key;
                $value  .= "\n " . $inchr;
            } else {
                $col  .= "\n ," . $key;
                $value  .= "\n ," . $inchr;
            }

            $i++;
        }

        $query .= $col;
        $query .= "\n ) VALUES (";
        $query .= $value;
        $query .= "\n )";
        $query .= "\n ON DUPLICATE KEY UPDATE";

        $i = 0;
        $col = "";
        $value = ""; 

        reset($param["col"]);

        while (list($key, $val) = each($param["col"])) {

            $inchr = $conn->qstr($val,get_magic_quotes_gpc());

            if ($i == 0) {
                $value  .= "\n " . $key . "=" . $inchr;
            } else {
                $value  .= "\n ," . $key . "=" . $inchr;
            }

            $i++;
        }
        $query .= $value;

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 입력에 실패 하였습니다.";
            return false;
        } else {
            return true;
        } 
    }

    /** 
     * @brief 다중 데이터 삭제 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["prk"] = "primary key colulm" <br>
     *        $param["prkVal"] = "primary data"  ex) 1,2,3,4 <br>
     *        $param["not"] = "제외 검색"  ex) Y<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */ 
    function deleteMultiData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" || 
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query  = "\n DELETE ";
        $query .= "\n   FROM " . $param["table"];
        $query .= "\n  WHERE " . $param["prk"];
        $query .= "\n     IN (";

        $prkValCount = count($param["prkVal"]);
        for ($i = 0; $i < $prkValCount; $i++) {
            $val = $conn->qstr($param["prkVal"][$i], get_magic_quotes_gpc());
            $query .= $val;

            if ($i !== $prkValCount - 1) {
                $query .= ",";
            }
        }
        $query .= ")";

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 삭제에 실패 하였습니다.";
            return false;
        } else {
            return true;
        } 
    }

    /** 
     * @brief 데이터 삭제 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["prk"] = "primary key column"<br>
     *        $param["prkVal"] = "primary data" <br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */ 
    function deleteData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" || 
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query  = "\n DELETE ";
        $query .= "\n   FROM " . $param["table"];
        $query .= "\n  WHERE " . $param["prk"];
        $query .= "\n       =" . $conn->qstr($param["prkVal"], get_magic_quotes_gpc());

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 삭제에 실패 하였습니다.";
            return false;
        } else {
            return true;
        } 
    }

    /** 
     * @brief 전체 데이터 삭제 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["prk"] = "primary key colulm"<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */ 
    function deleteAllData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" || 
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query  = "\n DELETE ";
        $query .= "\n   FROM " . $param["table"];
        $query .= "\n  WHERE " . $param["prk"] . " >= 0";

        $resultSet = $conn->Execute($query);

        if ($resultSet === false) {
            $errorMessage = "데이터 삭제에 실패 하였습니다.";
            return false;
        } else {
            return true;
        } 
    }

    /** 
     * @brief DISTINCT 데이터 검색 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"] = "컬럼명"<br>
     *        $param["where"]["컬럼명"] = "조건" (다중)<br>
     *        $param["order"] = "ORDER BY 컬럼"<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */ 
    function distinctData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" || 
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query = "\n SELECT DISTINCT " . $param["col"] . " FROM " . $param["table"];
        $i = 0;
        $value = "";

        if ($param["where"]) {

            while (list($key, $val) = each($param["where"])) {

                $inchr = $conn->qstr($val, get_magic_quotes_gpc());

                if ($i == 0) {
                    $value  .= "\n WHERE " . $key . "=" . $inchr;
                } else {
                    $value  .= "\n   AND " . $key . "=" . $inchr;
                }
                $i++;
            }
        }

        $query .= $value;

        if ($param["order"]) {
            $query .= "\n ORDER BY " . $param["order"]; 
        }

        //Query Cache 
        if ($param["cache"] == 1) {
            $rs = $conn->CacheExecute(1800, $query);
        } else {
            $rs = $conn->Execute($query);
        }

        return $rs;
    }

    /** 
     * @brief 데이터 검색 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["col"] = "컬럼명"<br>
     *        $param["where"]["컬럼명"] = "조건" (다중)<br>
     *        $param["not"]["컬럼명"] = "조건" (다중)<br>
     *        $param["order"] = "order by 조건"<br>
     *        $param["group"] = "group by 조건"<br>
     *        $param["cache"] = "1" 캐쉬 생성<br>
     *        $param["limit"]["start"] = 리미트 시작값<br>
     *        $param["limit"]["end"] =  리미트 종료값<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */ 
    function selectData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        //주문배송, 회원, 주문 공통, 가상계좌, 견적
        if ($param["table"] == "member" || $param["table"] == "order_common" || 
                $param["table"] == "order_dlvr" || $param["table"] == "virt_ba_admin" ||
                $param["table"] == "esti") {
            echo "접근이 허용되지 않는 테이블 입니다.";
            return false;
        }

        $query = "\n SELECT " . $param["col"] . " FROM " . $param["table"];

        $i = 0;
        $col = "";
        $value = "";

        if ($param["where"]) {


            while (list($key, $val) = each($param["where"])) {

                $inchr = $conn->qstr($val,get_magic_quotes_gpc());

                if ($i == 0) {
                    $value  .= "\n WHERE " . $key . "=" . $inchr;
                } else {
                    $value  .= "\n   AND " . $key . "=" . $inchr;
                }
                $i++;
            }
        }


        //임시로 만듬
        if ($param["not"]) {

            while (list($key, $val) = each($param["not"])) {

                $inchr = $conn->qstr($val,get_magic_quotes_gpc());
                $value  .= "\n AND NOT " . $key . "=" . $inchr;
                $i++;
            }
        }

        //like search
        if ($param["like"]) {

            while (list($key, $val) = each($param["like"])) {

                $inchr = substr($conn->qstr($val,get_magic_quotes_gpc()),1, -1);

                if ($i == 0) {
                    $value  .= "\n WHERE " . $key . " LIKE '%" . $inchr . "%'";
                } else {
                    $value  .= "\n   AND " . $key . " LIKE '%" . $inchr . "%'";
                }
                $i++;
            }
        }


        $query .= $value;

        if ($param["group"]) {
            $query .= "\n GROUP BY " . $param["group"]; 
        }

        if ($param["order"]) {
            $query .= "\n ORDER BY " . $param["order"]; 
        }

        if ($param["limit"]) {
            $query .= "\n LIMIT " . $param["limit"]["start"] . ",";
            $query .= $param["limit"]["end"]; 
        }



        //Query Cache 
        if ($param["cache"] == 1) {
            $rs = $conn->CacheExecute(1800, $query);
        } else {
            $rs = $conn->Execute($query);
        }

        return $rs;
    } 

    /** 
     * @brief COUNT 데이터 검색 쿼리 함수 (공통)<br>
     *        param 배열 설명<br>
     *        $param : $param["table"] = "테이블명"<br>
     *        $param["where"]["컬럼명"] = "조건" (다중)<br>
     *        $param["cache"] = "1" 캐쉬 생성<br>
     *        $param["limit"]["start"] = 리미트 시작값<br>
     *        $param["limit"]["end"] =  리미트 종료값<br>
     * @param $conn DB Connection
     * @param $param 파라미터 인자 배열
     * @return boolean
     */ 
    function countData($conn, $param) {

        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        $query = "\n SELECT count(*) cnt  FROM " . $param["table"];

        $i = 0;
        $col = "";
        $value = "";

        if ($param["where"]) {

            while (list($key, $val) = each($param["where"])) {

                $inchr = $conn->qstr($val,get_magic_quotes_gpc());

                if ($i == 0) {
                    $value  .= "\n WHERE " . $key . "=" . $inchr;
                } else {
                    $value  .= "\n   AND " . $key . "=" . $inchr;
                }
                $i++;
            }
        }

        if ($param["like"]) {

            while (list($key, $val) = each($param["like"])) {

                $inchr = $conn->qstr($val,get_magic_quotes_gpc());

                if ($i == 0) {
                    $value  .= "\n WHERE " . $key . " LIKE " . $inchr;
                } else {
                    $value  .= "\n   AND " . $key . " LIKE " . $inchr;
                }
                $i++;
            }
        }

        if ($param["group"]) {
            $query .= "\n GROUP BY " . $param["group"]; 
        }

        if ($param["limit"]) {

            $query .= "\n LIMIT " . $param["limit"]["start"] . ",";
            $query .= $param["limit"]["end"]; 
        }

        $query .= $value;

        $rs = $conn->Execute($query);
        return $rs;

    } 

    /** 
     * @brief 커넥션 검사
     * @param $conn DB Connection
     * @return boolean
     */ 
    function connectionCheck($conn) {
        if (!$conn) {
            echo "master connection failed\n";
            return false;
        }

        return true;
    }

    /** 
     * @brief SQL 인젝션 방지용
     *
     * @param $conn  = DB Connection
     * @param $param = 검색조건
     *
     * @return 변환 된 인자 
     */ 
    function parameterEscape($conn, $param) {
        $param = @htmlspecialchars($param, ENT_QUOTES, "UTF-8", false);
        $ret = $conn->qstr($param, get_magic_quotes_gpc());
        return $ret;
    }

    /** 
     * @brief SQL 인젝션 방지용, 배열
     *
     * @detail $except_arr 배열은 $except["제외할 필드명"] = true
     * 형식으로 입력받는다.
     *
     * @param $conn       = DB Connection
     * @param $param      = 검색조건 배열
     * @param $except_arr = 이스케이프 제외할 필드명
     *
     * @return 변환 된 배열
     */ 
    function parameterArrayEscape($conn, $param, $except_arr = null) {
        if (!is_array($param)) return false;

        foreach ($param as $key => $val) {
            if ($except_arr[$key] === true) {
                continue;
            }

            if (is_array($val)) {
                $val = $this->parameterArrayEscape($conn, $val, $except_arr);
            } else {
                $val = $this->parameterEscape($conn, $val);
            }

            $param[$key] = $val;
        }

        return $param;
    }

    /** 
     * @brief 배열값을 IN 조건 등에 들어갈 수 있도록 문자열로 변경
     *
     * @param $conn  = DB Connection
     * @param $param = 배열값
     *
     * @return 변환 된 배열
     */ 
    function arr2paramStr($conn, $param) {
        if (empty($param) === true || count($param) === 0) {
            return '';
        }

        $ret = "";

        foreach ($param as $val) {
            if (empty($val) === true) {
                continue;
            }

            $ret .= $this->parameterEscape($conn, $val) . ','; 
        }

        return substr($ret, 0, -1);
    }

    /** 
     * @brief NULL 이거나 공백값('')이 아닌 파라미터만 체크
     * @param $param 임의의 배열 인자
     * @param $key 임의의 배열 인자의 키
     * @return boolean
     */ 
    function blankParameterCheck($param, $key) {
        // 파라미터가 빈 값이 아닐경우
        if ($param !== ""
                && empty($param[$key]) !== true
                && $param[$key] !== "''" 
                && $param[$key] !== "NULL" 
                && $param[$key] !== "null") {
            return true;
        } else {
            return false;
        }
    }

    /** 
     * @brief CUD 실패시 입력된 에러메시지 반환
     * @return 에러메시지
     */ 
    function getErrorMessage() {
        return $errorMessage;
    }

    /** 
     * @brief 캐쉬를 삭제하는 함수
     * @param $conn DB Connection
     */ 
    function cacheFlush($conn) {
        $conn->CacheFlush();
    }

    /**
     * @brief 카테고리 검색
     *
     * @param $conn         = connection identifier
     * @param $sel_sortcode = html 선택으로 표시할 분류코드
     * @param $sortcode     = 검색조건 분류코드
     *
     * @return 검색결과
     */
    function selectCateHtml($conn, $sel_sortcode, $sortcode = null) {
        $param = array();
        $param["col"]   = "sortcode, cate_name";
        $param["table"] = "cate";
        if ($sortcode === null) {
            $param["where"]["cate_level"] = "1";
        } else {
            $param["where"]["high_sortcode"] = $sortcode;
            $param["where"]["use_yn"] = 'Y';
        }

        $param["order"] = "seq, sortcode, cate_name";

        $rs = $this->selectData($conn, $param);

        $arr = array();
        $arr["val"] = "sortcode";
        $arr["dvs"] = "cate_name";
        $arr["sel"] = $sel_sortcode;

        return makeOptionHtml($rs, $arr);
    }

    /*
     * @brief 지번 주소 Select 
     * @param $conn : DB Connection
     * @param $param["val"] : 지번 검색어

     * @param $param["area"] : 지역
     * @return : resultSet 
     */ 
    function selectJibunZip($conn, $param) {

        if (!$this->connectionCheck($conn)) return false; 
        $param = $this->parameterArrayEscape($conn, $param);
        $area = substr($param["area"], 1, -1); 
        $val = substr($param["val"], 1, -1); 

        $query  = "\n    SELECT  zipcode";
        $query .= "\n           ,sido";
        $query .= "\n           ,gugun";
        $query .= "\n           ,eup";
        $query .= "\n           ,dong";
        $query .= "\n           ,bldg";
        $query .= "\n           ,jibun_bonbun";
        $query .= "\n           ,jibun_bubun";
        $query .= "\n           ,bldg";
        $query .= "\n           ,ri";
        $query .= "\n      FROM  " . $area . "_zipcode";
        $query .= "\n     WHERE  (dong LIKE '%" . $val . "%'";
        $query .= "\n        OR   eup LIKE '%" . $val . "%'";
        $query .= "\n        OR   ri LIKE '%" . $val . "%')";

        $result = $conn->Execute($query);

        return $result;
    }

    /*
     * @brief 도로명 주소 Select 
     * @param $conn : DB Connection
     * @param $param["val"] : 지번 검색어
     * @param $param["area"] : 지역
     * @return : resultSet 
     */ 
    function selectDoroZip($conn, $param) {

        if (!$this->connectionCheck($conn)) return false; 
        $param = $this->parameterArrayEscape($conn, $param);
        $area = substr($param["area"], 1, -1); 
        $val = substr($param["val"], 1, -1); 

        $query  = "\n    SELECT  zipcode";
        $query .= "\n           ,sido";
        $query .= "\n           ,gugun";
        $query .= "\n           ,doro";
        $query .= "\n           ,bldg";
        $query .= "\n           ,bldg_bonbun";
        $query .= "\n           ,bldg_bubun";
        $query .= "\n      FROM  " . $area . "_zipcode";
        $query .= "\n     WHERE  doro LIKE '%" . $val .  "%'";

        $result = $conn->Execute($query);

        return $result;
    }

    /**
     * @brief 아이디와 비밀번호로 회원 정보 검색
     *
     * @detail $param["id"] = 회원 아이디
     * $param["seqno"] = 회원 일련번호
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectMember($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n    SELECT  A.member_seqno ";      /* 회원일련번호 */
        $query .= "\n           ,A.member_name ";       /* 회원명 */
        $query .= "\n           ,A.member_id ";         /* 회원아이디 */
        $query .= "\n           ,A.group_id ";          /* 그룹회원일련번호 */
        $query .= "\n           ,A.group_name ";        /* 그룹명 */
        $query .= "\n           ,A.member_photo_path "; /* 회원사진경로 */
        $query .= "\n           ,A.grade ";             /* 등급 */
        $query .= "\n           ,B.bank_name ";         /* 은행명 */
        $query .= "\n           ,B.ba_num ";            /* 가상계좌번호 */
        $query .= "\n           ,A.member_dvs ";        /* 회원구분 */
        $query .= "\n           ,A.member_typ ";        /* 회원종류 */
        $query .= "\n           ,A.own_point ";         /* 보유포인트 */
        $query .= "\n           ,A.prepay_price ";      /* 선입금액 */
        $query .= "\n           ,A.order_lack_price ";  /* 주문부족금액 */
        $query .= "\n           ,A.passwd ";            /* 비밀번호 */
        $query .= "\n           ,A.cumul_sales_price "; /* 누적매출금액 */
        $query .= "\n           ,A.onefile_etprs_yn";   /* 원파일업체여부 */
        $query .= "\n           ,A.card_pay_yn";        /* 카드결제여부 */
        $query .= "\n           ,A.nc_release_resp";    /* 명함출고담당자 */
        $query .= "\n           ,A.bl_release_resp";    /* 전단출고담당자 */
        $query .= "\n           ,A.first_order_date";   /* 최초 주문일자 */
        $query .= "\n           ,A.final_order_date";   /* 최종 주문일자*/
        $query .= "\n           ,A.A_board_yn";         /* A판 업체여부 */

        $query .= "\n      FROM  member  AS A ";
        $query .= "\n LEFT JOIN  virt_ba_admin AS B ";
        $query .= "\n        ON  A.member_seqno = B.member_seqno ";
        $query .= "\n     WHERE  1 = 1";
        $query .= "\n       AND  withdraw_dvs = 1";
        if ($this->blankParameterCheck($param, "id")) {
            $query .= "\n      AND  A.member_id = " . $param["id"];
        }
        if ($this->blankParameterCheck($param, "seqno")) {
            $query .= "\n      AND  A.member_seqno = " . $param["seqno"];
        }

        return $conn->Execute($query);
    }

    /**
     * @brief 쿠폰 매수 검색
     *
     * @param $conn  = connection identifier
     * @param $Seqno = 회원 일련번호
     *
     * @return 쿠폰 매수
     */
    function selectMemberCpCount($conn, $seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $seqno = $this->parameterEscape($conn, $seqno);

        $query .= "\nSELECT  COUNT(*) AS cp_count ";
        $query .= "\n  FROM  cp_use_history AS A ";
        $query .= "\n WHERE  member_seqno = %s";
        $query .= "\n   AND  A.use_yn = 'N'";

        $query  = sprintf($query, $seqno);

        $rs = $conn->Execute($query);

        return $rs->fields["cp_count"];
    }

    /**
     * @brief 주문 요약 배열 생성
     *
     * @detail $param["seqno"] = 회원일련번호
     * $param["date"] = 요약범위
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건파라미터
     *
     * @return 쿠폰 매수
     */
    function selectOrderSummary($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  B.dvs";
        $query .= "\n        ,count(1) AS state_count";

        $query .= "\n   FROM  order_common AS A";
        $query .= "\n        ,state_admin  AS B";

        $query .= "\n  WHERE  A.member_seqno = %s";
        $query .= "\n    AND  A.order_state = B.state_code";
        $query .= "\n    AND  A.order_regi_date BETWEEN %s AND %s";

        $query .= "\n  GROUP BY B.dvs";

        $query  = sprintf($query, $param["seqno"]
                                , $param["start_date"]
                                , $param["end_date"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 최근 주문리스트
     *
     * @detail $param["seqno"] = 회원일련번호
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건파라미터
     *
     * @return 리스트
     */
    function selectRecentOrderList($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n   SELECT  order_detail";
        $query .= "\n          ,order_common_seqno";
        $query .= "\n     FROM  order_common";
        $query .= "\n    WHERE  member_seqno = %s";
        $query .= "\n      AND  order_state != 110";
        $query .= "\n      AND  order_state != 120";
        $query .= "\n ORDER BY  order_common_seqno DESC";
        $query .= "\n    LIMIT  5";
        
        $query  = sprintf($query, $param["seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 카테고리 종이 정보 검색
     *
     * @param $conn   = connection identifier
     * @param $mpcode = 카테고리 종이 맵핑코드
     * @param $col    = 상품종이에서 검색할 필드
     *
     * @return 종이 기준단위
     */
    function selectCatePaperInfo($conn, $mpcode) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["col"]   = "sort, name, dvs, color, basisweight";
        $temp["table"] = "cate_paper";
        $temp["where"]["mpcode"] = $mpcode;

        $rs = $this->selectData($conn, $temp);

        return $rs->fields;
    }

    /**
     * @brief 도수명과 인쇄용도로 카테고리 인쇄 맵핑코드 검색
     *
     * @detail $param["name"] = 카테고리 후공정 맵핑코드
     * $param["purp_dvs"] = 판매채널
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function selectCatePrintMpcode($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n SELECT  C.mpcode";

        $query .= "\n   FROM  prdt_print      AS A";
        $query .= "\n        ,prdt_print_info AS B";
        $query .= "\n        ,cate_print      AS C";

        $query .= "\n  WHERE  A.prdt_print_seqno = C.prdt_print_seqno";
        $query .= "\n    AND  A.print_name       = B.print_name";
        $query .= "\n    AND  A.purp_dvs         = B.purp_dvs";
        $query .= "\n    AND  A.name             = %s";
        $query .= "\n    AND  A.purp_dvs         = %s";
        $query .= "\n    AND  C.cate_sortcode    = %s";
        if ($this->blankParameterCheck($param, "affil")) {
            $query .= "\n    AND  B.affil            = ";
            $query .= $param["affil"];
        }
        if ($this->blankParameterCheck($param, "side_dvs")) {
            $query .= "\n    AND  A.side_dvs         = ";
            $query .= $param["side_dvs"];
        }

        $query  = sprintf($query, $param["name"]
                                , $param["purp_dvs"]
                                , $param["cate_sortcode"]);

        $rs = $conn->Execute($query);

        return $rs->fields["mpcode"];
    }

    /**
     * @brief 회원 마지막 로그인 시간 변경
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateMemberFinalLoginDate($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param["final_login_date"] = date("Y-m-d H:i:s");

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = "\n    UPDATE  member ";
        $query .= "\n       SET  final_login_date = %s ";
        $query .= "\n     WHERE  member_seqno = %s ";

        $query = sprintf($query, $param["final_login_date"],
                         $param["member_seqno"]);

        $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 회원정보 비밀번호 변경
     *
     * @param $conn  = connection identifier
     * @param $param = 검색조건 파라미터
     *
     * @return 검색결과
     */
    function updateMemberPw($conn, $param) {
        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = "\n    UPDATE  member ";
        $query .= "\n       SET  passwd = %s ";
        $query .= "\n           ,final_modi_date = now()";
        $query .= "\n     WHERE  member_seqno = %s ";

        $query = sprintf($query, $param["passwd"],
                         $param["member_seqno"]);

        $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 회사_관리에서 판매채널명 검색
     *
     * @param $conn   = connection identifier
     * @param $seqno  = 검색조건 파라미터
     * @param $detail = 상세정보 포함
     *
     * @return 검색결과
     */
    function selectCpnAdmin($conn, $seqno, $detail = false) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $seqno = $this->parameterEscape($conn, $seqno);

        $query  = "\n SELECT  sell_site";
        if ($detail) {
            $query .= "\n        ,licensee_num";
            $query .= "\n        ,repre_name";
            $query .= "\n        ,repre_num";
            $query .= "\n        ,addr";
            $query .= "\n        ,addr_detail";
            $query .= "\n        ,bc";
            $query .= "\n        ,tob";
        }
        $query .= "\n   FROM  cpn_admin";
        $query .= "\n  WHERE  cpn_admin_seqno = %s ";

        $query  = sprintf($query, $seqno);

        $rs = $conn->Execute($query);

        if ($detail) {
            return $rs->fields;
        }

        return $rs->fields["sell_site"];
    }

    /**
     * @brief 결제확인 팝업용 사용자 기본정보 검색
     *
     * @param $conn = connection identifier
     * @param $member_seqno = 사용자 일련번호
     *
     * @return 검색결과
     */
    function selectMemberInfo($conn, $member_seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $member_seqno = $this->parameterEscape($conn, $member_seqno);

        $query  = "\n SELECT  A.mail";
        $query .= "\n        ,A.tel_num";
        $query .= "\n        ,A.cell_num";
        $query .= "\n        ,A.zipcode";
        $query .= "\n        ,A.addr";
        $query .= "\n        ,A.addr_detail";
        $query .= "\n   FROM  member AS A";
        $query .= "\n  WHERE  A.member_seqno = %s";

        $query  = sprintf($query, $member_seqno);

        $rs = $conn->Execute($query);

        return $rs->fields;
    }

    /**
     * @brief 회원_결제_내역 정보 입력
     *
     * @param $conn = connection identifier
     * @param $seqno = 주문 공통 일련번호
     *
     * @return 쿼리 실행결과
     */
    function insertMemberPayHistory($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n INSERT INTO member_pay_history (";
        $query .= "\n      member_seqno";
        $query .= "\n     ,deal_date";
        $query .= "\n     ,order_num";
        $query .= "\n     ,dvs";
        $query .= "\n     ,sell_price";
        $query .= "\n     ,sale_price";
        $query .= "\n     ,pay_price";
        $query .= "\n     ,depo_price";
        $query .= "\n     ,depo_way";
        $query .= "\n     ,exist_prepay";
        $query .= "\n     ,prepay_bal";
        $query .= "\n     ,state";
        $query .= "\n     ,deal_num";
        $query .= "\n     ,order_cancel_yn";
        $query .= "\n     ,prepay_use_yn";
        $query .= "\n ) VALUES (";
        $query .= "\n      %s";
        $query .= "\n     ,now()";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n     ,%s";
        $query .= "\n )";

        $query  = sprintf($query, $param["member_seqno"]
                                , $param["order_num"]
                                , $param["dvs"]
                                , $param["sell_price"]
                                , $param["sale_price"]
                                , $param["pay_price"]
                                , $param["depo_price"]
                                , $param["depo_way"]
                                , $param["exist_prepay"]
                                , $param["prepay_bal"]
                                , $param["state"]
                                , $param["deal_num"]
                                , $param["order_cancel_yn"]
                                , $param["prepay_use_yn"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 회원 테이블 값 수정
     *
     * @param $conn  = connection identifier
     * @param $param = 입력값 파라미터
     *
     * @return 쿼리실행결과
     */
    function updateMember($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $param = $this->parameterArrayEscape($conn, $param);

        $query  = "\n UPDATE  member as A";
        $query .= "\n    SET  A.member_seqno = A.member_seqno";
        $query .= "\n        ,final_modi_date = now()";
        if ($this->blankParameterCheck($param, "final_order_date")) {
            $query .= "\n        ,A.final_order_date  = ";
            $query .= $param["final_order_date"];
        }
        if ($this->blankParameterCheck($param, "first_order_date")) {
            $query .= "\n        ,A.first_order_date = ";
            $query .= $param["first_order_date"];
        }
        if ($this->blankParameterCheck($param, "cumul_sales_price")) {
            $query .= "\n        ,A.cumul_sales_price = IFNULL(A.cumul_sales_price, 0) + ";
            $query .= $param["cumul_sales_price"];
        }
        if ($this->blankParameterCheck($param, "order_lack_price")) {
            $query .= "\n        ,A.order_lack_price = ";
            $query .= $param["order_lack_price"];
        }
        if ($this->blankParameterCheck($param, "prepay_price")) {
            $query .= "\n        ,A.prepay_price = ";
            $query .= $param["prepay_price"];
        }
        if ($this->blankParameterCheck($param, "own_point")) {
            $query .= "\n        ,A.own_point = ";
            $query .= $param["own_point"];
        }
        $query .= "\n  WHERE  A.member_seqno = %s";

        $query  = sprintf($query, $param["member_seqno"]);

        return $conn->Execute($query);
    }

    /**
     * @brief 사용자 장바구니 갯수
     *
     * @param $conn = connection identifier
     * @param $member_seqno = 사용자 일련번호
     *
     * @return 검색결과
     */
    function selectCartCount($conn, $member_seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $member_seqno = $this->parameterEscape($conn, $member_seqno);

        $query  = "\n SELECT  COUNT(*) AS cnt";
        $query .= "\n   FROM  order_common";
        $query .= "\n  WHERE  member_seqno = %s";
        $query .= "\n    AND  order_state = '110'";

        $query  = sprintf($query, $member_seqno);

        return $conn->Execute($query);
    }

    /**
     * @brief 가상계좌 부여
     *
     * @param $conn  = connection identifier
     *
     * @return 검색결과
     */
    function insertVirtBaAdmin($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = "\nINSERT INTO virt_ba_admin ";
        $query .= "\n( ba_num";
        $query .= "\n, state";
        $query .= "\n, bank_name";
        $query .= "\n, cpn_admin_seqno";
        $query .= "\n, member_seqno) ";
        $query .= "\nVALUES ";
        $query .= "\n(%s";
        $query .= "\n,'%s'";
        $query .= "\n,%s";
        $query .= "\n,%s";
        $query .= "\n,%s)";

        $query = sprintf($query,
                         $param["ba_num"],
                         "Y",
                         $param["bank_name"],
                         $param["cpn_admin_seqno"],
                         $param["member_seqno"]);

        $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 가상계좌 반환
     *
     * @param $conn  = connection identifier
     *
     * @return 검색결과
     */
    function deleteVirtBaAdmin($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
 
        $query  ="\nDELETE FROM virt_ba_admin ";
        $query .="\n WHERE member_seqno =%s";

        $query = sprintf($query,
                         $param["member_seqno"]);

        $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 선입금 충전 할 회원 일련번호 검색
     *
     * @param $conn  = connection identifier
     *
     * @return 검색결과
     */
    function selectDepositMember($conn, $r_account_no) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $r_account_no = $this->parameterEscape($conn, $r_account_no);
 
        $query  = "\nSELECT member_seqno ";
        $query .= "\n  FROM virt_ba_admin ";
        $query .= "\n WHERE ba_num = %s";

        $query = sprintf($query,
                         $r_account_no);

        return $conn->Execute($query);
    }

    /**
     * @brief 회원 선입금, 부족금액 정보 가져오기
     *
     * @param $conn  = connection identifier
     *
     * @return 검색결과
     */
    function selectPrepayPrice($conn, $member_seqno) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $member_seqno = $this->parameterEscape($conn, $member_seqno);
 
        $query  = "\nSELECT prepay_price ";
        $query .= "\n      ,order_lack_price ";
        $query .= "\n  FROM member ";
        $query .= "\n WHERE member_seqno = %s";

        $query = sprintf($query,
                         $member_seqno);

        return $conn->Execute($query);
    }

    /**
     * @brief 선입금 충전
     *
     * @param $conn  = connection identifier
     *
     * @return 검색결과
     */
    function updateMemberPrepay($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = "\n    UPDATE  member ";
        $query .= "\n       SET  prepay_price = %s";
        $query .= "\n           ,final_modi_date = now()";
        $query .= "\n     WHERE  member_seqno = %s";

        $query = sprintf($query, $param["prepay_price"],
                                 $param["member_seqno"]);

         $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief sms 보내기
     *
     * @param $conn  = connection identifier
     *
     * @return 검색결과
     */
    function sendSms($conn, $param) {

        //커넥션 체크
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        //인젝션 어택 방지
        $param = $this->parameterArrayEscape($conn, $param);
  
        $query  = "\nINSERT INTO SC_TRAN (";
        $query .= "\n  TR_SENDDATE";
        $query .= "\n, TR_SENDSTAT";
        $query .= "\n, TR_MSGTYPE";
        $query .= "\n, TR_PHONE";
        $query .= "\n, TR_CALLBACK";
        $query .= "\n, TR_MSG) ";
        $query .= "\nVALUES (";
        $query .= "\n  NOW()";
        $query .= "\n, '0'";
        $query .= "\n, '0'";
        $query .= "\n, %s";
        $query .= "\n, %s";
        $query .= "\n, %s)";

        $query = sprintf($query,
                         $param["tr_phone"],
                         $param["tr_callback"],
                         $param["tr_msg"]);

        $resultSet = $conn->Execute($query);
 
        if ($resultSet === FALSE) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @brief 카테고리명 검색
     *
     * @param $conn = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return 카테고리명
     */
    function selectCateName($conn, $cate_sortcode) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["col"] = "cate_name";
        $temp["table"] = "cate";
        $temp["where"]["sortcode"] = $cate_sortcode;

        $rs = $this->selectData($conn, $temp);

        return $rs->fields["cate_name"];
    }

    /**
     * @brief 발행_대상_금액 있는지 검색
     *
     * @param $conn = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return 카테고리명
     */
    function selectPublicObjectPrice($conn, $member_seqno) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["col"]  = " public_object_price";
        $temp["col"] .= ",unissued_object_price";
        $temp["table"] = "public_object_price";
        $temp["where"]["member_seqno"] = $member_seqno;

        $rs = $this->selectData($conn, $temp);

        return $rs;
    }

    /**
     * @brief 발행_대상_금액 입력
     *
     * @param $conn = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return 카테고리명
     */
    function insertPublicObjectPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["col"]["public_object_price"]   = $param["public_object_price"];
        $temp["col"]["unissued_object_price"] = $param["unissued_object_price"];
        $temp["col"]["member_seqno"]          = $param["member_seqno"];
        $temp["table"] = "public_object_price";

        $rs = $this->insertData($conn, $temp);

        return $rs;
    }

    /**
     * @brief 발행_대상_금액 수정
     *
     * @param $conn = connection identifier
     * @param $cate_sortcode = 카테고리 분류코드
     *
     * @return 카테고리명
     */
    function updatePublicObjectPrice($conn, $param) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $temp = array();
        $temp["col"]["public_object_price"]   = $param["public_object_price"];
        $temp["col"]["unissued_object_price"] = $param["unissued_object_price"];
        $temp["table"]  = "public_object_price";
        $temp["prk"]    = "member_seqno";
        $temp["prkVal"] = $param["member_seqno"];

        $rs = $this->updateData($conn, $temp);

        return $rs;
    }

    /**
     * @brief 주문상태값 검색
     *
     * @param $conn = connection identifier
     *
     * @return 카테고리명
     */
    function selectStateAdmin($conn) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $query  = "\n   SELECT  A.state_code";
        $query .= "\n          ,A.front_state_name";
        $query .= "\n     FROM  state_admin AS A";
        $query .= "\n ORDER BY  A.state_code";

        $rs = $conn->Execute($query);

        return $rs;
    }

    /**
     * @brief 주문상태 구분값 검색
     *
     * @param $conn = connection identifier
     *
     * @return 카테고리명
     */
    function selectStateAdminDvs($conn) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $query  = "\n   SELECT  DISTINCT A.front_dvs AS dvs";
        $query .= "\n     FROM  state_admin AS A";
        $query .= "\n ORDER BY  A.state_code";

        $rs = $conn->Execute($query);

        return $rs;
    }

    /**
     * @breif 상태_관리 테이블에서 해당 상태에 해당하는
     * 범위 최소/최대값 검색
     *
     * @param $conn = db connection
     * @param $dvs  = 상태구분
     *
     * @return 최소/최대값
     */
    function selectStateAdminRange($conn, $dvs) {
        if ($this->connectionCheck($conn) === false) {
            return false;
        }

        $dvs = $this->parameterEscape($conn, $dvs);

        $query  = "\n SELECT  MIN(A.state_code + 0) AS min";
        $query .= "\n        ,MAX(A.state_code + 0) AS max";
        $query .= "\n   FROM  state_admin AS A";
        $query .= "\n  WHERE  A.front_dvs IN (%s)";
        $query .= "\n    AND  A.front_state_name != '주문취소'";

        $query  = sprintf($query, $dvs);

        $rs = $conn->Execute($query);

        return $rs->fields;
    }

    /**
     * @brief 리스트 쿼리 row 수 반환
     *
     * @detail https://blog.asamaru.net/2015/09/11/using-sql-calc-found-rows-and-found-rows-with-mysql/
     *
     * @param $conn = connection identifier
     *
     * @return 카테고리명
     */
    function selectFoundRows($conn) {
        if ($this->connectionCheck($conn) === false) {
            return false; 
        }

        $query = "SELECT FOUND_ROWS() AS count";

        $rs = $conn->Execute($query);

        return $rs->fields["count"];
    }
}
?>
