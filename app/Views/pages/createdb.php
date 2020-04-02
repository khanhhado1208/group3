<div class="container">
    <br>
    <?= \Config\Services::validation()->listErrors(); ?>

        <div class="row">
            <div class="col-md-9">
                <form action="<?php echo base_url('/admin/setupdb') ?>" method="post" accept-charset="utf-8">
                    <div class="form-group">
                        <button type="submit" id="send_form" class="btn btn-success">Create Tables</button>
                    </div>
                </form>
                <form action="<?php echo base_url('/admin/dropdb') ?>" method="post" accept-charset="utf-8">
                    <div class="form-group">
                        <button type="submit" id="send_form" class="btn btn-success">Drop Tables</button>
                    </div>
                </form>
            </div>

        </div>

</div>