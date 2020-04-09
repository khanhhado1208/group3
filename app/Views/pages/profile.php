<div class="container">
    <br>
    <?= \Config\Services::validation()->listErrors(); ?>

        <div class="row">
            <div class="col-md-12">
            <div class="row">
                <div class="col card">
                    <p class="h4">Security</p>
                    <a href="<?php echo base_url('changepassword') ?>">Change password</a>
                </div>
                <div class="col card">
                    <p class="h4">Activity</p>
                    <a href="<?php echo base_url('history') ?>">Transaction history</a>
                    <a href="<?php echo base_url('wallet') ?>">Wallet</a>
                    <a href="<?php echo base_url('support') ?>">Submitted tickets</a>
                </div>
                <div class="col card">
                    <p class="h4">Privacy</p>
                    <a href="<?php echo base_url('privacypolicy') ?>">Privacy policy</a>
                    <a href="<?php echo base_url('unregister') ?>">Manage my data</a>
                </div>
                <div class="col card">
                    <p class="h4">Preferences</p>
                    <a href="<?php echo base_url('unregister') ?>">Disable account</a>
                    <a href="<?php echo base_url('unregister') ?>">Delete account</a>
                </div>
            </div>
            </div>
        </div>
</div>