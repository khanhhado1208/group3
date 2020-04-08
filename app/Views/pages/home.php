<div class="container">
    <p class="h2">Start trading stonks now, 10 stonk$ sign up bonus for new users!</p>
    <img src="https://cdn.archi.fi/home.gif"></img>
    <br>
    <p class="h3">Demo tools</p>
    <a href="<?php echo base_url('/createdb') ?>">
        <button class="btn btn-success">Database Tools</button>
    </a>
    <form action="<?php echo base_url('/account/demoAutoLogin') ?>" method="post" accept-charset="utf-8">
        <div class="form-group">
            <a href="<?php echo base_url('/demoAutoLogin') ?>">
                <button class="btn btn-success" type="submit" id="send_form">Log in with demouser</button>
            </a>
        </div>
    </form>
</div>