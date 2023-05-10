<?php
    require_once 'dbh.inc.php';

    $sql = "SELECT *
            FROM contratto"; 

    $stmt = mysqli_stmt_init($conn);

    if(!mysqli_stmt_prepare($stmt, $sql)) { 
        header("location: ../../index.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    while($row = mysqli_fetch_assoc($resultData)) {

        $SmartContractArray = array(
            'ABI'                   => $row["ABI"],
            'AddressSmartContract'   => $row["AddressSmartContract"],
            'AddressOwner'           => $row["AddressOwner"],
        );
    }

    mysqli_stmt_close($stmt);
    
    echo json_encode($SmartContractArray);
