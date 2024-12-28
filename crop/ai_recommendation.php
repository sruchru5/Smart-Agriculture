<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recommendation'])) {
    // Path to your Python script
    $output = shell_exec("python test.py");
		echo "<script>window.location.href='home.php';</script>";

    // Display the output
    echo "<pre>$output</pre>";
}
?>
