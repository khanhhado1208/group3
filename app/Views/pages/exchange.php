<div class="container">
<br>
<div class='row'>

<?php 
echo '<div class="nav flex-column nav-pills col-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">';
echo '<a class="nav-link active" id="v-pills-'.$stonkproperties[0]->stonk_id.'-tab" data-toggle="pill" href="#v-pills-'.$stonkproperties[0]->stonk_id.'" role="tab" aria-controls="v-pills-'.$stonkproperties[0]->stonk_id.'" aria-selected="true">';
echo $stonkproperties[0]->stonk_name.'</a>';
for ($i = 1; $i < count($stonkproperties); $i++) {
    echo '<a class="nav-link" id="v-pills-'.$stonkproperties[$i]->stonk_id.'-tab" data-toggle="pill" href="#v-pills-'.$stonkproperties[$i]->stonk_id.'" role="tab" aria-controls="v-pills-'.$stonkproperties[$i]->stonk_id.'" aria-selected="false">';
    echo $stonkproperties[$i]->stonk_name.'</a>';
}
echo '<div class="tab-content col-9" id="v-pills-tabContent">';
echo '<div class="tab-pane fade show active" id="v-pills-'.$stonkproperties[0]->stonk_id.'" role="tabpanel" aria-labelledby="v-pills-'.$stonkproperties[0]->stonk_id.'-tab">
<p class=h3>'.$stonkproperties[0]->stonk_name.'</p>
<p class=h6><a href="#">'.$stonkproperties[0]->issuer_name.'</a></p>
<a href="/quicktrade"><button class="btn btn-success">Buy/Sell</button></a>
</div>';
for ($i = 1; $i < count($stonkproperties); $i++) {
    echo '<div class="tab-pane fade" id="v-pills-'.$stonkproperties[$i]->stonk_id.'" role="tabpanel" aria-labelledby="v-pills-'.$stonkproperties[$i]->stonk_id.'-tab">
    <p class=h3>'.$stonkproperties[$i]->stonk_name.'</p>
    <p class=h6><a href="#">'.$stonkproperties[$i]->issuer_name.'</a></p>
    <a href="/quicktrade"><button class="btn btn-success">Buy/Sell</button></a>
    </div>';
}
echo '</div></div>';
?>

</div></div>