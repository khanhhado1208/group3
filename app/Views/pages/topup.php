<div class="container">
    <br>
    <?= \Config\Services::validation()->listErrors(); ?>
 
    <div class="row">
      <div class="col-md-9">
        <form action="<?php echo base_url('index.php/account/topup') ?>" method="post" accept-charset="utf-8">
          <div class="form-group">
            <input type="number" min="1" name="amount" class="form-control" id="formGroupExampleInput" placeholder="Please enter the top up amount">
           <button type="submit" id="send_form" class="btn btn-success">Top Up</button>
          </div>
        </form>
      </div>
 
    </div>
  
</div>