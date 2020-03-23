<div class="container">
    <br>
    <?= \Config\Services::validation()->listErrors(); ?>
    <div class="container">
        <div class="row">
            <div class="col card">
            <h3>Welcome, <?= $username ?>! </h3>
            <h2>Balance: <?= $balance ?> STONK$ </h2>
                <div class="row">
                    <div class="col">
                        <a href='/topup'><button class="btn btn-light">Deposit</button></a>
                    </div>
                    <div class="col">
                        <a href='/withdraw'><button class="btn btn-light">Withdraw</button></a>
                    </div>
                    <div class="col">
                        <a href='/history'><button class="btn btn-light">History</button></a>
                    </div>
                </div>
            </div>
            <div class="col card">
            <?php
                if (count($history) == 0){
                    echo 'You don\'t seem to have any recent activity.<br>
                        <a href="/exchange">Start trading today!</a>';
                } else {
                    echo "<table class=\"table\">";
                    echo "<thead><th colspan=\"3\">Recent Activity<th></thead>";
                    $max_displayed_rows = 3;
                    foreach (array_reverse($history) as $row) {
                        echo "</td><td>";
                        echo $row->tx_value;
                        echo "</td><td>";
                        echo $row->tx_type;
                        echo "</td><td>";
                        echo $row->tx_date;
                        echo "</td><tr>";
                        $max_displayed_rows -= 1;
                        if ($max_displayed_rows == 0) break;
                    }
                    echo "</table>";
                }
            ?>
            </div>
            <div class="col card">
            Activity from the past week
            <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.js"></script>

            <canvas id="activityChart" width="400" height="400"></canvas>
            <script>
            var ctx = document.getElementById('activityChart');
            var activityChart = new Chart(ctx, {
                type: 'line',
                data: {
                    <?php
                        echo "labels: ['".(date("d") - 6)."', '".(date("d") - 5)."', '".(date("d") - 4)."', '".(date("d") - 3)."', '".(date("d") - 2)."', '".(date("d") - 1)."', '".date("d")."'],";
                    ?>
                    datasets: [{
                        label: '# of transactions',
                        <?php
                                $txvol = [0, 0, 0, 0, 0, 0, 0];
                            foreach ($history as $row)
                            {
                                $row = var_export($row->tx_date, true);
                                $date = new DateTime();
                                for ($x = 0; $x <= 5; $x++) {
                                    $check = $date->format('Y-m-d');
                                    if(strpos($row, $check) !== false){
                                        $txvol[$x] += 1;
                                    }
                                    $date->sub(new DateInterval('P1D'));
                                }

                            }
                            $array = array_reverse($txvol);
                            echo "data: ['".$array[0]."', '".$array[1]."','".$array[2]."','".$array[3]."','".$array[4]."','".$array[5]."','".$array[6]."',],";
                        ?>
                        backgroundColor: 'rgba(0, 255, 0, 0.6)',
                        borderColor: 'rgba(0, 0, 0, 0.3)',
                        borderWidth: 2
                    }]
                },
            });
            </script>
            </div>
        </div>
        <div class="row">
            <div class="col card">
            Announcements
            </div>
            <div class="col card">
            Live site activity
            </div>
        </div>
    </div>