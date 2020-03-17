<div class="container">
    <br>
    <?= \Config\Services::validation()->listErrors(); ?>
 
    <div class="row">
      <div class="col-md-9">
        <form action="<?php echo base_url('index.php/account/setupdb') ?>" method="post" accept-charset="utf-8">
          <div class="form-group">
           <button type="submit" id="send_form" class="btn btn-success">Create Table</button>
          </div>
        </form>
      </div>
 
    </div>
  
</div>