<?php
include "conn.php";

?>

<?php
$x_date  = mysqli_query($konek, 'SELECT date  FROM ( SELECT * FROM curve ORDER BY id DESC LIMIT 10) Var1 ORDER BY ID ASC');
$y_voltage = mysqli_query($konek, 'SELECT voltage FROM ( SELECT * FROM curve ORDER BY id DESC LIMIT 10) Var1 ORDER BY ID ASC');
$y_current = mysqli_query($konek, 'SELECT current FROM ( SELECT * FROM curve ORDER BY id DESC LIMIT 10) Var1 ORDER BY ID ASC');

$voltage = mysqli_query($konek, 'SELECT voltage FROM curve  ORDER BY id DESC LIMIT 3');
$max_power = mysqli_query($konek, 'SELECT  MAX(power) FROM curve ORDER BY id DESC LIMIT 1');
$cur_power = mysqli_query($konek, 'SELECT power FROM curve ORDER BY id DESC LIMIT 1');
$luminance = mysqli_query($konek, 'SELECT luminance FROM curve ORDER BY id DESC LIMIT 2');


if (mysqli_num_rows($max_power) > 0) {
  $row1 = mysqli_fetch_all($max_power);
  $p1 =  $row1[0][0];
}

if (mysqli_num_rows($cur_power) > 0) {
  $row2 = mysqli_fetch_all($cur_power);
  $p2 =  $row2[0][0];
}

if (mysqli_num_rows($luminance) > 0) {
  $row3 = mysqli_fetch_all($luminance);
  $l1 =  $row3[1][0];
  $l2 =  $row3[0][0];
}


if (mysqli_num_rows($voltage) > 1) {
  // output data of row


  $row = mysqli_fetch_all($voltage);

  $beforePrev_state = $row[2][0];

  $previous_state =  $row[1][0];

  $current_state =  $row[0][0];


  $currenterror = (($previous_state - $current_state) * 100 / $previous_state);
  $previouserror = (($beforePrev_state - $previous_state) * 100 / $beforePrev_state);
  $Effectpower = (($p1 - $p2) * 100 / $p1);
  $lumi_error = (($l1 - $l2) * 100 / $l1);


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
    if ($lumi_error > 60) {
      $message = "Normal Condition countining";
    } else {
      $message = "Shading occured";
    }
  } elseif ($currenterror < -30) {
    $message = "Normal Condition countining";
  } else {
    if ($Effectpower > 50) {
      $message = "Shading effect Countining";
    } else {
      $message = "Normal Condition Countining";
    }
  }
}

?>


<div class="panel panel-primary" style="width: auto;">
  <div class="panel-heading">
    <h3 class="panel-title">
      <center>Graph of Voltage and Current</center>
    </h3>
  </div>

  <div class="row">
    <div class="col-md-8">
      <div class="panel-body">
        <canvas id="myChart"></canvas>
        <script>
          var canvas = document.getElementById('myChart');
          var data = {
            labels: [<?php while ($b = mysqli_fetch_array($x_date)) {
                        echo '"' . $b['date'] . '",';
                      } ?>],
            datasets: [{
                label: "voltage",
                fill: false,
                lineTension: 0.1,
                backgroundColor: "rgba(105, 0, 132, .2)",
                borderColor: "rgba(200, 99, 132, .7)",
                borderCapStyle: 'butt',
                borderDash: [],
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                pointBorderColor: "rgba(200, 99, 132, .7)",
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(200, 99, 132, .7)",
                pointHoverBorderColor: "rgba(200, 99, 132, .7)",
                pointHoverBorderWidth: 2,
                pointRadius: 5,
                pointHitRadius: 10,
                data: [<?php while ($b = mysqli_fetch_array($y_voltage)) {
                          echo  $b['voltage'] . ',';
                        } ?>],
              },
              {
                label: "current",
                fill: false,
                lineTension: 0.1,
                backgroundColor: "rgba(0, 137, 132, .2)",
                borderColor: "rgba(0, 10, 130, .7)",
                borderCapStyle: 'butt',
                borderDash: [],
                borderDashOffset: 0.0,
                borderJoinStyle: 'miter',
                pointBorderColor: "rgba(0, 10, 130, .7)",
                pointBackgroundColor: "#fff",
                pointBorderWidth: 1,
                pointHoverRadius: 5,
                pointHoverBackgroundColor: "rgba(0, 10, 130, .7)",
                pointHoverBorderColor: "rgba(0, 10, 130, .7)",
                pointHoverBorderWidth: 2,
                pointRadius: 5,
                pointHitRadius: 10,
                data: [<?php while ($b = mysqli_fetch_array($y_current)) {
                          echo  $b['current'] . ',';
                        } ?>],
              }
            ]
          };

          var option = {
            showLines: true,
            animation: {
              duration: 0
            }
          };

          var myLineChart = Chart.Line(canvas, {
            data: data,
            options: option
          });
        </script>
      </div>
    </div>
    <div class="col-md-4">
      <h1><b>Condition</b></h1>

      <div class="col align-self-center">
        <h3>Power (W) </h3>
        <span class="badge badge-secondary"><?php echo $p2 ?></span>
        </br>
        <h3>Temperature (Celcius)</h3>
        <span class="badge badge-secondary"><?php echo $p1 ?></span>
        </br>
        <h3>Irradiance (W/m^2)</h3>
        <span class="badge badge-secondary"><?php echo $p2 ?></span>
        </br>
        <h3>Status</h3>
        <span class="badge badge-secondary"><?php echo $message ?></span>
      </div>
    </div>
  </div>
</div>

<div class="panel panel-primary">
  <div class="panel-heading">
    <h3 class="panel-title">
      <center>Data Logger
    </h3>
  </div>
  <div class="panel-body">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th class='text-center'>Date</th>
          <th class='text-center'>Voltage</th>
          <th class='text-center'>Current</th>
        </tr>
      </thead>

      <tbody>
        <?php

        $sqlAdmin = mysqli_query($konek, "SELECT date,voltage,current FROM curve ORDER BY ID DESC LIMIT 0,20");
        while ($data = mysqli_fetch_array($sqlAdmin)) {
          echo "<tr >
                <td><center>$data[date]</center></td> 
                <td><center>$data[voltage]</td>
                <td><center>$data[current]</td>
              </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>