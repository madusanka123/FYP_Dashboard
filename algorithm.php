 <?php
    include "conn.php";
    ?>

<?php


$y_voltage = mysqli_query($konek, 'SELECT voltage FROM curve  ORDER BY id DESC LIMIT 3');
$max_power = mysqli_query($konek, 'SELECT  MAX(power) FROM curve');
$cur_power = mysqli_query($konek, 'SELECT power FROM curve ORDER BY id DESC LIMIT 1');

if (mysqli_num_rows($max_power) > 0) {
    $row1 = mysqli_fetch_all($max_power);
    $p1 =  $row1[0][0];
}

if (mysqli_num_rows($cur_power) > 0) {
    $row2 = mysqli_fetch_all($cur_power);
    $p2 =  $row2[0][0];
}

if (mysqli_num_rows($y_voltage) > 1) {
    // output data of row


    $row = mysqli_fetch_all($y_voltage);

    $beforePrev_state = $row[2][0];

    $previous_state =  $row[1][0];

    $current_state =  $row[0][0];


    $currenterror = (($previous_state - $current_state) * 100 / $previous_state);
    $previouserror = (($beforePrev_state - $previous_state) * 100 / $beforePrev_state);
    $Effectpower = (($p1 - $p2) * 100 / $p1);

    $state1 = 2;
    $message = null;

    if ($currenterror < 30 and $previouserror < -30) {
        $state1 = 1;
    } elseif ($previouserror > 30 and $currenterror > -30) {
        $state1 = 0;
    }

    $state = $state1;

    if ($state == 1) {
        $message =  "Normal Condition countining";
    } elseif ($state == 0) {
        $message = "Shading effect Countining";
    } elseif ($currenterror > 30) {
        $message = "Shading occured";
    } elseif ($currenterror < -30) {
        $message = "Shading just removed";
    } else {
        if ($Effectpower > 50) {
            $message = "Still Shading Countining";
        } else {
            echo "Normal Condition Countining";
        }
    }
}

?>