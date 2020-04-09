<div class="container">
    <br>
    <?= \Config\Services::validation()->listErrors(); ?>

        <div class="row">
            <div class="col-md-9">
                <form action="<?php echo base_url('/account/authenticate') ?>" method="post" accept-charset="utf-8">

                    <div class="form-group">
                        <label for="formGroupExampleInput">Username</label>
                        <input type="text" name="username" class="form-control" id="formGroupExampleInput" placeholder="Please enter your username">

                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" class="form-control" id="password" placeholder="Please enter your password">

                    </div>

                    <div class="form-group">
                        <button type="submit" id="send_form" class="btn btn-success">Log in</button>
                    </div>
                </form>
                <form action="<?php echo base_url('/account/demoAutoLogin') ?>" method="post" accept-charset="utf-8">
                    <div class="form-group">
                        <a href="<?php echo base_url('/demoAutoLogin') ?>">
                            <button class="btn btn-outline-success" type="submit" id="send_form">Demo login</button>
                        </a>
                    </div>
                </form>
                <a href="<?php echo base_url('/admin') ?>">
                    <button class="btn btn-outline-danger">Admin login</button>
                </a>
            </div>

        </div>

</div>