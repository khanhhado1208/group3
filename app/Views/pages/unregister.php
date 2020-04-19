<style>
.item {
    opacity: 0.6;
}

.item:hover {
    opacity: 1.0;
    cursor: pointer;
}
</style>

<div class="container">
    <br>
    <?= \Config\Services::validation()->listErrors(); ?>
        <script>
            function showConfirmation(selected) {
                document.getElementById("select").style.display = "none";
                document.getElementById("confirm" + selected).style.display = "block";
            }
        </script>
        <div class="row">
            <div class="col-md-12" style="align-content: center;">
            <div id="confirmdat" style="display: none;">
                <p class="h1">Are you sure?</p>
                <p class="text-danger">This will permanently wipe your account history, INCLUDING balances not withdrawn</p>
                <a href="<?php echo base_url('/profile') ?>"><button class="btn btn-success">Back to safety</button></a>
                <form action="<?php echo base_url('/account/deleteuserinfo') ?>" method="post" accept-charset="utf-8">
                <button class="btn btn-danger">Yes, delete my data</button>
                </form>
            </div>
            <div id="confirmdis" style="display: none;">
                <p class="h1">Are you sure?</p>
                <p class="text-danger">Only admins can restore disabled accounts.</p>
                <a href="<?php echo base_url('/profile') ?>"><button class="btn btn-success">Back to safety</button></a>
                <form action="<?php echo base_url('/account/disableaccount') ?>" method="post" accept-charset="utf-8">
                <button class="btn btn-danger">Yes, disable my account</button>
                </form>
            </div>
            <div id="confirmdel" style="display: none;">
                <p class="h1">Are you sure?</p>
                <p class="text-danger">This action is permanent. Your account and ALL data INCLUDING balances not withdrawn will vanish into the void</p>
                <a href="<?php echo base_url('/profile') ?>"><button class="btn btn-success">Back to safety</button></a>
                <form action="<?php echo base_url('/account/deleteuser') ?>" method="post" accept-charset="utf-8">
                <button class="btn btn-danger">Yes, delete my account</button>
                </form>
            </div>
            <div id="select">
            <h2 style="text-align: center;">I thought we were friends :(</h1>
            <h4 style="text-align: center;">We're sad to see you go, <?= $username ?>. If you change your mind you can always go <a href="<?php echo base_url('/profile') ?>">back</a></h2>
            <div class="row">
                <div class="col card item" id="dat" onclick="showConfirmation('dat')">
                <div class="card-body">
                    <p class="h4 card-title">Delete activity</p>
                    <p class="card-text text-success">Your activity will be wiped</p>
                    <p class="card-text text-success">Balances not withdrawn will be wiped</p>
                    <p class="card-text text-danger">Your account will not be deleted</p>
                    <p class="card-text text-danger">You will not be eligible for another sign up bonus</p>
                </div>
                </div>
                <div class="col card item" id="dis" onclick="showConfirmation('dis')">
                <div class="card-body">
                    <p class="h4 card-title">Disable account</p>
                    <p class="card-text text-success">Your account will be disabled</p>
                    <p class="card-text text-danger">Your account data will not be deleted</p>
                    <p class="card-text text-danger">Only admins can restore or delete disabled accounts</p>
                </div>
                </div>
                <div class="col card item" id="del" onclick="showConfirmation('del')">
                <div class="card-body">
                    <p class="h4 card-title">Delete account</p>
                    <p class="card-text text-success">Your account will be permanently deleted</p>
                    <p class="card-text text-success">Your account data will be deleted</p>
                    <p class="card-text text-danger">You will need to register again</p>
                </div>
                </div>
            </div>
            </div>
            </div>
        </div>
</div>

