<?php
$srv = '203.248.116.111';
$sid = 'CGISDEV';
$port = 1521;

$oc_query = "DECLARE";
$oc_query .= "  P_CLNTNUM             VARCHAR2(400) := '30139938';";
$oc_query .= "  P_CLNTMGMCUSTCD       VARCHAR2(400) := '30139938';";
$oc_query .= "  P_PRNGDIVCD           VARCHAR2(400) := '01';";
$oc_query .= "  P_CGOSTS              VARCHAR2(400) := '91';";
$oc_query .= "  P_ADDRESS             VARCHAR2(400) := '서울 관악구 봉천동 180-96 666666666666';";
$oc_query .= "  P_ZIPNUM              VARCHAR2(400);";
$oc_query .= "  P_ZIPID               VARCHAR2(400);";
$oc_query .= "  P_OLDADDRESS          VARCHAR2(400);";
$oc_query .= "  P_OLDADDRESSDTL       VARCHAR2(400);";
$oc_query .= "  P_NEWADDRESS          VARCHAR2(400);";
$oc_query .= "  P_NESADDRESSDTL       VARCHAR2(400);";
$oc_query .= "  P_ETCADDR             VARCHAR2(400);";
$oc_query .= "  P_SHORTADDR           VARCHAR2(400);";
$oc_query .= "  P_CLSFADDR            VARCHAR2(400);";
$oc_query .= "  P_CLLDLVBRANCD        VARCHAR2(400);";
$oc_query .= "  P_CLLDLVBRANNM        VARCHAR2(400);";
$oc_query .= "  P_CLLDLCBRANSHORTNM   VARCHAR2(400);";
$oc_query .= "  P_CLLDLVEMPNUM        VARCHAR2(400);";
$oc_query .= "  P_CLLDLVEMPNM         VARCHAR2(400);";
$oc_query .= "  P_CLLDLVEMPNICKNM     VARCHAR2(400);";
$oc_query .= "  P_CLSFCD              VARCHAR2(400);";
$oc_query .= "  P_CLSFNM              VARCHAR2(400);";
$oc_query .= "  P_SUBCLSFCD           VARCHAR2(400);";
$oc_query .= "  P_RSPSDIV             VARCHAR2(400);";
$oc_query .= "  P_NEWADDRYN           VARCHAR2(400);";
$oc_query .= "  P_ERRORCD             VARCHAR2(400);";
$oc_query .= "  P_ERRORMSG            VARCHAR2(400);";

$oc_query .= "BEGIN";
$oc_query .= "  PKG_RVAP_ADDRSEARCH.PR_RVAP_SEARCHADDRESS";
$oc_query .= "  (";
$oc_query .= "      P_CLNTNUM";
$oc_query .= "      , P_CLNTMGMCUSTCD";
$oc_query .= "      , P_PRNGDIVCD";
$oc_query .= "      , P_CGOSTS";
$oc_query .= "      , P_ADDRESS";
$oc_query .= "      , P_ZIPNUM";
$oc_query .= "      , P_ZIPID";
$oc_query .= "      , P_OLDADDRESS";
$oc_query .= "      , P_OLDADDRESSDTL";
$oc_query .= "      , P_NEWADDRESS";
$oc_query .= "      , P_NESADDRESSDTL";
$oc_query .= "      , P_ETCADDR";
$oc_query .= "      , P_SHORTADDR";
$oc_query .= "      , P_CLSFADDR";
$oc_query .= "      , P_CLLDLVBRANCD";
$oc_query .= "      , P_CLLDLVBRANNM";
$oc_query .= "      , P_CLLDLCBRANSHORTNM";
$oc_query .= "      , P_CLLDLVEMPNUM";
$oc_query .= "      , P_CLLDLVEMPNM";
$oc_query .= "      , P_CLLDLVEMPNICKNM";
$oc_query .= "      , P_CLSFCD";
$oc_query .= "      , P_CLSFNM";
$oc_query .= "      , P_SUBCLSFCD";
$oc_query .= "      , P_RSPSDIV";
$oc_query .= "      , P_NEWADDRYN";
$oc_query .= "      , P_ERRORCD";
$oc_query .= "      , P_ERRORMSG";
$oc_query .= "  );";

$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CLNTNUM           : ' || NVL(P_CLNTNUM           , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CLNTMGMCUSTCD     : ' || NVL(P_CLNTMGMCUSTCD     , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_PRNGDIVCD         : ' || NVL(P_PRNGDIVCD         , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CGOSTS            : ' || NVL(P_CGOSTS            , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_ADDRESS           : ' || NVL(P_ADDRESS           , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_ZIPNUM            : ' || NVL(P_ZIPNUM            , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_ZIPID             : ' || NVL(P_ZIPID             , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_OLDADDRESS        : ' || NVL(P_OLDADDRESS        , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_OLDADDRESSDTL     : ' || NVL(P_OLDADDRESSDTL     , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_NEWADDRESS        : ' || NVL(P_NEWADDRESS        , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_NESADDRESSDTL     : ' || NVL(P_NESADDRESSDTL     , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_ETCADDR           : ' || NVL(P_ETCADDR           , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_SHORTADDR         : ' || NVL(P_SHORTADDR         , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CLSFADDR          : ' || NVL(P_CLSFADDR          , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CLLDLVBRANCD      : ' || NVL(P_CLLDLVBRANCD      , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CLLDLVBRANNM      : ' || NVL(P_CLLDLVBRANNM      , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CLLDLCBRANSHORTNM : ' || NVL(P_CLLDLCBRANSHORTNM , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CLLDLVEMPNUM      : ' || NVL(P_CLLDLVEMPNUM      , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CLLDLVEMPNM       : ' || NVL(P_CLLDLVEMPNM       , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CLLDLVEMPNICKNM   : ' || NVL(P_CLLDLVEMPNICKNM   , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CLSFCD            : ' || NVL(P_CLSFCD            , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_CLSFNM            : ' || NVL(P_CLSFNM            , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_SUBCLSFCD         : ' || NVL(P_SUBCLSFCD         , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_RSPSDIV           : ' || NVL(P_RSPSDIV           , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_NEWADDRYN         : ' || NVL(P_NEWADDRYN         , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_ERRORCD           : ' || NVL(P_ERRORCD           , 'NULL'));";
$oc_query .= "  DBMS_OUTPUT.PUT_LINE('P_ERRORMSG          : ' || NVL(P_ERRORMSG          , 'NULL'));";

$oc_query .= "EXCEPTION WHEN OTHERS THEN";
$oc_query .= "   DBMS_OUTPUT.PUT_LINE('CODE : ' || SQLCODE);";
$oc_query .= "   DBMS_OUTPUT.PUT_LINE('MSG  : ' || SQLERRM);";
$oc_query .= "END;";

//$oc_query = addslashes($oc_query);

$conn = oci_connect('gdprinting', 'gdprintingdev$#!1', "$srv:$port/$sid", 'UTF8');
if (!$conn) {
        $e = oci_error();
            echo "An Error occured! " .  $e['message'] . "\n";
                exit(1);
}


$stid = oci_parse($conn, $oc_query);


if (!oci_execute($stid)) {
        $e = oci_error($stid);
		echo var_dump($e);
            echo "An Error occured! " .  $e['message'] . "\n";
                exit(1);
}


while ( $row = oci_fetch_assoc($stid) ) {
 print "ID: {$row['P_CLNTNUM']}, ";

 // Call the load() method to get the contents of the LOB
 print $row['MYLOB']->load()."\n";
}





oci_free_statement($stid);
oci_close($conn);
//phpinfo()
?>