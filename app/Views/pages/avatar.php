<style>
.carousel-control-next-icon,
.carousel-control-prev-icon {
    filter: invert(1);
}
</style>

<div class="container">

<div class="container">

	<div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="false">
		<div class="carousel-inner">

			<?php
                $files = glob('img/*.{jpg,png,gif}', GLOB_BRACE);
                $active = false;
                foreach ($files as $file) {
                    if (!$active) {
                        echo '<div class="carousel-item active">';
                        $active = true;
                    } else {
                        echo '<div class="carousel-item">';
                    }
                    echo '<img class="d-block w-100" src="'.$file.'" onclick=selectAvatar("'.$file.'")>';
                    echo '</div>';
                }
          ?>
		</div>
		<a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
			<span class="carousel-control-prev-icon" aria-hidden="true"></span>
			<span class="sr-only">Previous</span>
		</a>
		<a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
			<span class="carousel-control-next-icon" aria-hidden="true"></span>
			<span class="sr-only">Next</span>
		</a>
	</div>
</div>

<form action="<?php echo base_url('/account/avatar') ?>" method="post" accept-charset="utf-8" id="form">
    <input type="hidden" name="file" id="file">
</form>

<script>

function selectAvatar(avatar) {
    document.getElementById("file").value = avatar;
    document.getElementById("form").submit();
}

</script>