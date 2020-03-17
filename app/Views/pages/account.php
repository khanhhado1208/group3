<div class="container">
    <br>
    <?= \Config\Services::validation()->listErrors(); ?>
    <div class="container mt-5">
        <h1 class="text-center"> Welcome <?= $username; ?>!</h1>
    </div>

    <div class="row">
      <div class="col-md-9">
        <form action="<?php echo base_url('index.php/account/logout') ?>" method="post" accept-charset="utf-8">
          <div class="form-group">
           <button type="submit" id="send_form" class="btn btn-success">Logout</button>
          </div>
        </form>
      </div>
 
    </div>
  
</div>