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