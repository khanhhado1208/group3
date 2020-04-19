<style>
.avatar {
    width: 200px;
    height: 200px;
    margin: 10px;
    float: left;
    object-fit: cover;
}
.avatar:hover {
    cursor: pointer;
}
.clearboth {
    clear: both;
}
</style>

<div class="container">
<p class=h3>Click to choose your avatar</p>
<?php
    $files = glob('img/*.{jpg,png,gif}', GLOB_BRACE);
    foreach ($files as $file) {
        echo '<img class="d-block avatar" src="'.$file.'" onclick=selectAvatar("'.$file.'")>';
    }
?>
<div class="clearboth"></div>

<form action="<?php echo base_url('/account/avatar') ?>" method="post" accept-charset="utf-8" id="form">
    <input type="hidden" name="file" id="file">
</form>
</div>
<script>

function selectAvatar(avatar) {
    document.getElementById("file").value = avatar;
    document.getElementById("form").submit();
}

</script>