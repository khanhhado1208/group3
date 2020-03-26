<div class="container">
<br>
<div class='row'>

<?php 
echo '<div class="nav flex-column nav-pills col-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">';
echo '<a class="nav-link active" id="v-pills-'.$stonkids[0].'-tab" data-toggle="pill" href="#v-pills-'.$stonkids[0].'" role="tab" aria-controls="v-pills-'.$stonkids[0].'" aria-selected="true">';
echo $stonknames[2].'</a>';
for($x = 1; $x <= sizeof($stonkids) - 1; $x++){
    echo '<a class="nav-link" id="v-pills-'.$stonkids[$x].'-tab" data-toggle="pill" href="#v-pills-'.$stonkids[$x].'" role="tab" aria-controls="v-pills-'.$stonkids[$x].'" aria-selected="false">';
    echo $stonknames[$x+2].'</a>';
}
echo '<div class="tab-content col-9" id="v-pills-tabContent">';
echo '<div class="tab-pane fade show active" id="v-pills-'.$stonkids[0].'" role="tabpanel" aria-labelledby="v-pills-'.$stonkids[0].'-tab">
<p class=h3>'.$stonknames[2].'</p>
<p class=h6><a href="#">Issuername</a></p>
<a href="/quicktrade"><button class="btn btn-success">Buy/Sell</button></a>
</div>';
for($x = 1; $x <= sizeof($stonkids) - 1; $x++){
    echo '<div class="tab-pane fade" id="v-pills-'.$stonkids[$x].'" role="tabpanel" aria-labelledby="v-pills-'.$stonkids[$x].'-tab">
    <p class=h3>'.$stonknames[$x+2].'</p>
    <p class=h6><a href="#">Issuername</a></p>
    <a href="/quicktrade"><button class="btn btn-success">Buy/Sell</button></a>
    </div>';
}
echo '</div></div>';
?>

</div></div>