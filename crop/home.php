<?php
session_start();
// error_reporting(0);
include('dbconnection.php');
if (strlen($_SESSION['login_id']==0)) {
  header('location:logout.php');
} else {
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" http-equiv="refresh" content="5">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>AI & IoT Based Crop Recommendation</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <!-- <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon"> -->

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <link href="assets/css/customize.css" rel="stylesheet">

     <!--script for Today's date in attendence-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<style>
    #chartContainer {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh; /* Full viewport height */
    }

    #barChart {
        width: 80%;   /* You can adjust the width percentage as needed */
        height: 300px; /* Set a fixed height for the chart */
    }
</style>

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex justify-content-center">
      <a href="home.php" class="logo d-flex align-items-center">
        <span>Crop Recommendation</span>
      </a>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="logout.php">
           <span>Logout</span>
          </a><!-- End Profile Iamge Icon -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <main id="main" class="main">

    <div class="pagetitle">
      
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-md-12">

          <div class="card">
            <h5 class="card-title">IoT Based Soil Data Monitor</h5>
            <div class="card-body txt-green">
              <div class="row">
                <div class="col-6">
                  <span>Date: </span>
                  <span id="date"></span>
                </div>
                <div class="col-6 d-flex justify-content-end">
                <span>Time: </span>
                <span id="time">
                </span>
                </div>
              </div>
              <?php
                $query0 = mysqli_query($con,"select * from tbl_data order by id DESC LIMIT 1");
                while($row0 = mysqli_fetch_array($query0)){
              ?>
              <div class="row">
                <div class="col-md-4 mt-2">
                  <div class="card card-green">
                    <div class="card-body">
                      <h1 class="mt-4" style="font-weight:800">Nitrogen (N)</h1>
                      <h1><?php echo $row0['n']; ?></h1>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card card-green">
                    <div class="card-body">
                      <h1 class="mt-4" style="font-weight:800">Phosphorus (P)</h1>
                      <h1><?php echo $row0['p']; ?></h1>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card card-green">
                    <div class="card-body">
                      <h1 class="mt-4" style="font-weight:800">Potassium (K)</h1>
                      <h1><?php echo $row0['k']; ?></h1>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="card card-green">
                    <div class="card-body">
                      <h1 class="mt-4" style="font-weight:800">Soil Moisture</h1>
                      <h1><?php echo $row0['soil']; ?></h1>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card card-green">
                    <div class="card-body">
                      <h1 class="mt-4" style="font-weight:800">Ph</h1>
                      <h1><?php echo $row0['ph']; ?></h1>
                    </div>
                  </div>
                </div>
              </div>
              <?php } ?>
            </div>
          </div>

        </div>

        <div class="col-md-12">

          <div class="card">
            <h5 class="card-title">AI Based Crop Recommendation</h5>
            <div class="card-body">
			<div class="row">
			 
				<div class="chart-container" style="width: 60%; margin: auto;">
        <canvas id="barChart"></canvas>
    </div>

			
			</div>
              <div class="row">
                <div class="col-md-12 mb-3">
                <div style="overflow: auto;">
                <table class="table table-bordered text-center">
                  <thead>
                    <tr>
                        <th scope="col">Date</th>
                        <th scope="col">Time</th>   
                        <th scope="col">Nitrogen (N)</th>
                        <th scope="col">Phosphorus (P)</th>
                        <th scope="col">Potassium (K)</th>
                        <th scope="col">Soil Moisture</th>
                        <th scope="col">Ph</th>
                    </tr>
                </thead>
                <tbody id="myTable">
                <?php 
                    $query1=mysqli_query($con, "select * from tbl_data");
                    while($row1=mysqli_fetch_array($query1)){
                  ?>
                    <tr>
                        <td><?php echo $row1['date']; ?></td>
                        <td><?php echo $row1['time']; ?></td>
                        <td><?php echo $row1['n']; ?></td>
                        <td><?php echo $row1['p']; ?></td>
                        <td><?php echo $row1['k']; ?></td>
                        <td><?php echo $row1['soil']; ?></td>
                        <td><?php echo $row1['ph']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
                    </div>
                </div>
                <div class="col-6 d-flex justify-content-center">
				 
                <form action="generate_pdf.php" method="post">
                    <input type="submit" class="btn btn-success" name="generate_pdf" value="Generate PDF Report">
                </form>
           </div>
			 <div class="col-6 d-flex justify-content-center">
                 <form action="ai_recommendation.php" method="post">
        <input type="submit" class="btn btn-success" name="recommendation" value="AI Recommendation">
    </form>
                </div>
            
            </div>
          </div>
        </div>
        <div class="col-12">
        <div class="card">
            <h5 class="card-title">Recommended Crop</h5>
            <div class="card-body">
			<div class="chart-container" style="width: 40%; margin: auto;">
			 <canvas id="pieChart" width="100" height="100"></canvas>
			 </div>
              <div class="row">
                <div class="col-md-12 mb-3">
                <table class="table table-bordered text-center">
                  <thead>
                    <tr>
                        <th scope="col">Crop</th>
                        <th scope="col">Percentage</th>
                    </tr>
                </thead>
                <tbody id="myTable">
                <?php 
                    $query2=mysqli_query($con, "select * from tbl_crop");
                    while($row2=mysqli_fetch_array($query2)){
                  ?>
                    <tr>
                        <td><?php echo $row2['crop']; ?></td>
                        <td><?php echo $row2['percentage']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
                </div>
            </div>
            </div>
          </div>
		 
        </div>
      </div>
    </section>

  </main><!-- End #main -->
	
  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>AI & IoT Based Crop Recommendation</span></strong>. All Rights Reserved
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    n =  new Date();
    y = n.getFullYear();
    m = n.getMonth() + 1;
    d = n.getDate();
    document.getElementById("date").innerHTML = m + "-" + d + "-" + y;
  </script>

  <script>
    function updateTime() {
        const currentTime = new Date();
        const options = { hour: '2-digit', minute: '2-digit', hour12: true };
        document.getElementById('time').innerText = currentTime.toLocaleString('en-US', options);
    }

    // Update the time immediately and then every minute
    updateTime();
    setInterval(updateTime, 60000);
  </script>
 <script>
        // Define chart data structure
        const chartData = {
            labels: ['Nitrogen (N)', 'Phosphorus (P)', 'Potassium (K)', 'Soil Moisture', 'pH'], // Labels for data
            values: [] // Values fetched from the database
        };

        <?php
        // Fetch the most recent row of data from the database
        $query1 = mysqli_query($con, "SELECT * FROM tbl_data ORDER BY id DESC LIMIT 1");
        $row1 = mysqli_fetch_array($query1);

        // Pass the values from PHP to JavaScript
        echo "chartData.values.push({$row1['n']}, {$row1['p']}, {$row1['k']}, {$row1['soil']}, {$row1['ph']});";
        ?>

        // Select the canvas element
        const ctx = document.getElementById('barChart').getContext('2d');

        // Create the bar chart
        const barChart = new Chart(ctx, {
            type: 'bar', // Chart type
            data: {
                labels: chartData.labels, // Use labels defined above
                datasets: [{
                    label: 'Soil Analysis Data', // Chart legend label
                    data: chartData.values, // Values from database
                    backgroundColor: [
                        'rgba(244, 67, 54, 0.8)',  // Nitrogen
                        'rgba(33, 150, 243, 0.8)', // Phosphorus
                        'rgba(76, 175, 80, 0.8)',  // Potassium
                        'rgba(255, 193, 7, 0.8)',  // Soil Moisture
                        'rgba(156, 39, 176, 0.8)'  // pH
                    ],
                    borderColor: [
                        'rgba(244, 67, 54, 1)',
                        'rgba(33, 150, 243, 1)',
                        'rgba(76, 175, 80, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(156, 39, 176, 1)'
                    ],
                    borderWidth: 5 // Border thickness
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true // Ensure Y-axis starts at zero
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top' // Legend position
                    }
                }
            }
        });
    </script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var cropData = <?php 
        $crops = [];
        $percentages = [];
        $query = mysqli_query($con, "SELECT * FROM tbl_crop");
        while ($row = mysqli_fetch_array($query)) {
            $crops[] = $row['crop'];
            $percentages[] = $row['percentage'];
        }
        echo json_encode(['crops' => $crops, 'percentages' => $percentages]);
    ?>;

    var ctx = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: cropData.crops, // Crop Names
            datasets: [{
                label: 'Crop Percentages',
                data: cropData.percentages, // Percentages
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'], // Colors
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                        }
                    }
                }
            }
        }
    });
});
</script>
</body>

</html>
<?php } ?>