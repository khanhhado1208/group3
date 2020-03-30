<div class="container">
    <br>
    <?= \Config\Services::validation()->listErrors(); ?>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Please enter your new password">

        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Enter your new password again">

        </div>
        <br>
        <div class="form-group">
            <button type="submit" id="send_form" class="btn btn-success">Confirm</button>
        </div>