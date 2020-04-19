<style>
    .p {
        opacity: 1;
    }
    
    .animated {
        opacity: 0;
        transition: all 0.5s;
    }
</style>

<div class="container">
    <br>
    <?= \Config\Services::validation()->listErrors(); ?>
        <div class="container">
            <div class="row">
                <div class="col card">
                <img class="card-img-top" height="200px" width="100px" src="<?php echo base_url($avatar) ?>" alt="User avatar">
                <div class="card-body">
                    <h4 class="card-title"><?= $username ?>'s dashboard</h4>
                    <p class="card-text h5">Balance: <?= $balance ?> Stonk$ </p>
                    <a class="card-text" href='<?php echo base_url('/wallet') ?>'>
                    <small class="text-muted">Go to my wallet</small>
                    </a>
                </div>
                </div>
                <div class="col card">
                <div class="card-body">
                    <?php
                if (count($history) == 0) {
                    echo '<h4 class="card-title">No recent activity.</h4><br>
                        <a href="'.base_url('/exchange').'">Start trading today!</a>';
                } else {
                    echo '<h4 class="card-title">Recent activity</h4><br>';
                    echo "<table class=\"table\">";
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
                        if ($max_displayed_rows == 0) {
                            break;
                        }
                    }
                    echo "</table>";
                    echo '<a class="card-text" href="'.base_url('/history').'">';
                    echo '<small class="text-muted">Show full transaction history</small>';
                    echo '</a>';
                }
            ?>
                </div></div>
                <div class="col card">
                <div class="card-body">
                    <h4 class="card-title">Activity from the past week</h4>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.js"></script>

                    <canvas id="activityChart" width="400" height="400"></canvas>
                    <script>
                        var ctx = document.getElementById('activityChart');
                        var data = {
                            <?php
                        echo "labels: ['".(date("d") - 6)."', '".(date("d") - 5)."', '".(date("d") - 4)."', '".(date("d") - 3)."', '".(date("d") - 2)."', '".(date("d") - 1)."', '".(date("d") - 0)."'],";
                    ?>
                            datasets: [{
                                label: '# of transactions',
                                <?php
                                $txvol = [0, 0, 0, 0, 0, 0, 0];
                            foreach ($history as $row) {
                                $row = var_export($row->tx_date, true);
                                $date = new DateTime();
                                for ($x = 0; $x <= 5; $x++) {
                                    $check = $date->format('Y-m-d');
                                    if (strpos($row, $check) !== false) {
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
                        }
                        var option = {
                            scales: {
                                yAxes: [{
                                    stacked: true,
                                    ticks: {
                                        suggestedMin: 0,
                                        stepSize: 1
                                    }
                                }]
                            }
                        }
                        var activityChart = new Chart(ctx, {
                            type: 'line',
                            data: data,
                            options: option
                        });
                    </script>
                </div>
            </div></div>
            <div class="row">
                <div class="col card">
                <div class="card-body">
                    <h4 class="card-title" id="headline">Announcements</h4>
                    <p class="card-text" id="story">Check out the site announcements here!</p>
                </div>
                </div>
            </div>
        </div>
</div>

<script>
    let announcements = [];

    fetch("<?php echo base_url('/json/announcements.json') ?>")
        .then(res => res.json())
        .then(
            (result) => {
                announcements = result.announcements;
            }, (error) => {}
        );

    let index = 0;
    let headline = document.getElementById("headline");
    let story = document.getElementById("story");

    setInterval(() => {
        headline.classList.add("animated");
        story.classList.add("animated");

        index = index + 1;
        if (index >= announcements.length) {
            index = 0;
        }
        setTimeout(function() {
            headline.innerHTML = announcements[index].headline;
            story.innerHTML = announcements[index].story;
            headline.classList.remove("animated");
            story.classList.remove("animated");
        }, 500);
    }, 5000);
</script>