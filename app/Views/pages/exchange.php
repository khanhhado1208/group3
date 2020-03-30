<div class="container">
<br>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.js"></script>
<div class='row'>
<?php 

$date = new DateTime();
$hourly = [];
$daily = [];
$weekly = [];
$hourly_now = $date->format('i');
$daily_now = $date->format('H');
$weekly_now = $date->format('d');
for ($i = 0; $i < 60; $i++){
    array_push($hourly, $hourly_now);
    if($hourly_now > 0) {
        $hourly_now = $hourly_now - 1;
    } else {
        $hourly_now = 59;
    }
}
for ($i = 0; $i < 24; $i++){
    array_push($daily, $daily_now.':00');
    if($daily_now > 0) {
        $daily_now = $daily_now - 1;
    } else {
        $daily_now = 23;
    }
}
for ($i = 0; $i < 7; $i++){
    array_push($weekly, $weekly_now);
    if($weekly_now > 0) {
        $weekly_now = $weekly_now - 1;
    } else {
        $weekly_now = 6;
    }
}
$hourly = array_reverse($hourly);
$daily = array_reverse($daily);
$weekly = array_reverse($weekly);

echo '<div class="nav flex-column nav-pills col" id="v-pills-tab" role="tablist" aria-orientation="vertical">';
echo '<a class="nav-link active" id="v-pills-'.$stonkproperties[0]->stonk_id.'-tab" data-toggle="pill" href="#v-pills-'.$stonkproperties[0]->stonk_id.'" role="tab" aria-controls="v-pills-'.$stonkproperties[0]->stonk_id.'" aria-selected="true">';
echo $stonkproperties[0]->stonk_name.'</a>';
for ($i = 1; $i < count($stonkproperties); $i++) {
    echo '<a class="nav-link" id="v-pills-'.$stonkproperties[$i]->stonk_id.'-tab" data-toggle="pill" href="#v-pills-'.$stonkproperties[$i]->stonk_id.'" role="tab" aria-controls="v-pills-'.$stonkproperties[$i]->stonk_id.'" aria-selected="false">';
    echo $stonkproperties[$i]->stonk_name.'</a>';
}
echo '<div class="tab-content col-md-11" id="v-pills-tabContent">';
echo '<div class="tab-pane fade show active" id="v-pills-'.$stonkproperties[0]->stonk_id.'" role="tabpanel" aria-labelledby="v-pills-'.$stonkproperties[0]->stonk_id.'-tab">
<p class=h3>'.$stonkproperties[0]->stonk_name.'</p>
<p class=h6><a href="#">'.$stonkproperties[0]->issuer_name.'</a></p>
<p>Information about this stonk: '.$stonkproperties[0]->stonk_desc.'</p>
<p>Current value: '.$pricenow[$stonkproperties[0]->stonk_id].'stonk$</p>
<a href="'.base_url('/quicktrade').'"><button class="btn btn-success">Buy/Sell</button></a>
<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
    <a class="nav-link disabled" href="#">Price graph</a>
    </li>
    <li class="nav-item">
    <a class="nav-link active" id="hourly-tab" data-toggle="tab" href="#hourly" role="tab" aria-controls="hourly" aria-selected="true">1h</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" id="daily-tab" data-toggle="tab" href="#daily" role="tab" aria-controls="daily" aria-selected="false">1d</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" id="weekly-tab" data-toggle="tab" href="#weekly" role="tab" aria-controls="weekly" aria-selected="false">1w</a>
    </li>
    </ul>
    <div class="tab-content" id="myTabContent">
    <div class="tab-pane show active" id="hourly" role="tabpanel" aria-labelledby="hourly-tab">
    <canvas id="hourlyChart0"></canvas>
    </div>
    <div class="tab-pane" id="daily" role="tabpanel" aria-labelledby="daily-tab">
    <canvas id="dailyChart0"></canvas>
    </div>
    <div class="tab-pane" id="weekly" role="tabpanel" aria-labelledby="weekly-tab">
    <canvas id="weeklyChart0"></canvas>
    </div> 
    </div>
    <script>
    var ctx = document.getElementById("hourlyChart0");
    var ctx1 = document.getElementById("dailyChart0");
    var ctx2 = document.getElementById("weeklyChart0");
    var datahourly = {
            labels: ["'.implode('", "', $hourly).'"],
            datasets: [{
                label: "Value in stonk$",
                data: ["'.implode('", "', $hourlydata[$stonkproperties[0]->stonk_id]).'"],
                backgroundColor: "rgba(0, 0, 255, 0.6)",
                borderColor: "rgba(0, 0, 0, 0.3)",
                borderWidth: 2 }] }
    var datadaily = {
        labels: ["'.implode('", "', $daily).'"],
        datasets: [{
            label: "Value in stonk$",
            data: ["'.implode('", "', $dailydata[$stonkproperties[0]->stonk_id]).'"],
            backgroundColor: "rgba(0, 0, 255, 0.6)",
            borderColor: "rgba(0, 0, 0, 0.3)",
            borderWidth: 2 }] }
    var dataweekly = {
        labels: ["'.implode('", "', $weekly).'"],
        datasets: [{
            label: "Value in stonk$",
            data: ["'.implode('", "', $weeklydata[$stonkproperties[0]->stonk_id]).'"],
            backgroundColor: "rgba(0, 0, 255, 0.6)",
            borderColor: "rgba(0, 0, 0, 0.3)",
            borderWidth: 2 }] }
    var option = { scales: { yAxes: [ { stacked: true, ticks: { suggestedMin: 0, stepSize: 1 } } ] } }
    var hourlyChart0 = new Chart(ctx, {
        type: "line",
        data:datahourly,
        options:option });
    var dailyChart0 = new Chart(ctx1, {
        type: "line",
        data:datadaily,
        options:option });
    var weeklyChart0 = new Chart(ctx2, {
        type: "line",
        data:dataweekly,
        options:option });
    </script>
</div>';

for ($i = 1; $i < count($stonkproperties); $i++) {
    echo '<div class="tab-pane fade" id="v-pills-'.$stonkproperties[$i]->stonk_id.'" role="tabpanel" aria-labelledby="v-pills-'.$stonkproperties[$i]->stonk_id.'-tab">
    <p class=h3>'.$stonkproperties[$i]->stonk_name.'</p>
    <p class=h6><a href="#">'.$stonkproperties[$i]->issuer_name.'</a></p>
    <p>Information about this stonk: '.$stonkproperties[$i]->stonk_desc.'</p>
    <p>Current value '.$pricenow[$stonkproperties[$i]->stonk_id].'stonk$</p>
    <a href="'.base_url('/quicktrade').'"><button class="btn btn-success">Buy/Sell</button></a>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item">
    <a class="nav-link disabled" href="#">Price graph</a>
    </li>
    <li class="nav-item">
    <a class="nav-link active" id="hourly'.$i.'-tab" data-toggle="tab" href="#hourly'.$i.'" role="tab" aria-controls="hourly'.$i.'" aria-selected="true">1h</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" id="daily'.$i.'-tab" data-toggle="tab" href="#daily'.$i.'" role="tab" aria-controls="daily'.$i.'" aria-selected="false">1d</a>
    </li>
    <li class="nav-item">
    <a class="nav-link" id="weekly'.$i.'-tab" data-toggle="tab" href="#weekly'.$i.'" role="tab" aria-controls="weekly'.$i.'" aria-selected="false">1w</a>
    </li>
    </ul>
    <div class="tab-content" id="myTabContent'.$i.'">
    <div class="tab-pane show active" id="hourly'.$i.'" role="tabpanel" aria-labelledby="hourly'.$i.'-tab">
    <canvas id="hourlyChart'.$i.'"></canvas>
    </div>
    <div class="tab-pane" id="daily'.$i.'" role="tabpanel" aria-labelledby="daily'.$i.'-tab">
    <canvas id="dailyChart'.$i.'"></canvas>
    </div>
    <div class="tab-pane" id="weekly'.$i.'" role="tabpanel" aria-labelledby="weekly'.$i.'-tab">
    <canvas id="weeklyChart'.$i.'"></canvas>
    </div> 
    </div>
    <script>
    var ctx = document.getElementById("hourlyChart'.$i.'");
    var ctx1 = document.getElementById("dailyChart'.$i.'");
    var ctx2 = document.getElementById("weeklyChart'.$i.'");
    var datahourly = {
            labels: ["'.implode('", "', $hourly).'"],
            datasets: [{
                label: "Value in stonk$",
                data: ["'.implode('", "', $hourlydata[$stonkproperties[$i]->stonk_id]).'"],
                backgroundColor: "rgba(0, 0, 255, 0.6)",
                borderColor: "rgba(0, 0, 0, 0.3)",
                borderWidth: 2 }] }
    var datadaily = {
        labels: ["'.implode('", "', $daily).'"],
        datasets: [{
            label: "Value in stonk$",
            data: ["'.implode('", "', $dailydata[$stonkproperties[$i]->stonk_id]).'"],
            backgroundColor: "rgba(0, 0, 255, 0.6)",
            borderColor: "rgba(0, 0, 0, 0.3)",
            borderWidth: 2 }] }
    var dataweekly = {
        labels: ["'.implode('", "', $weekly).'"],
        datasets: [{
            label: "Value in stonk$",
            data: ["'.implode('", "', $weeklydata[$stonkproperties[$i]->stonk_id]).'"],
            backgroundColor: "rgba(0, 0, 255, 0.6)",
            borderColor: "rgba(0, 0, 0, 0.3)",
            borderWidth: 2 }] }
    var option = { scales: { yAxes: [ { stacked: true, ticks: { suggestedMin: 0, stepSize: 1 } } ] } }
    var hourlyChart'.$i.' = new Chart(ctx, {
        type: "line",
        data:datahourly,
        options:option });
    var dailyChart'.$i.' = new Chart(ctx1, {
        type: "line",
        data:datadaily,
        options:option });
    var weeklyChart'.$i.' = new Chart(ctx2, {
        type: "line",
        data:dataweekly,
        options:option });
    </script>
    </div>';
}
echo '</div></div>';
?>

</div>
</div>