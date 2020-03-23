<div class="container">
    <br>
    <?= \Config\Services::validation()->listErrors(); ?>
    <div class="container">
        <div class="row">
            <div class="col card">
            <h3>Welcome, <?= $username ?>! </h3>
            <h2>Balance: <?= $balance ?> STONK$ </h2>
                <div class="row">
                    <div class="col">
                        <a href='/topup'><button class="btn btn-light">Deposit</button></a>
                    </div>
                    <div class="col">
                        <a href='/withdraw'><button class="btn btn-light">Withdraw</button></a>
                    </div>
                    <div class="col">
                        <a href='/history'><button class="btn btn-light">History</button></a>
                    </div>
                </div>
            </div>
            <div class="col card">
            Recent activity
            </div>
            <div class="col card">
            Activity from the past week
            

            </div>
        </div>
        <div class="row">
            <div class="col card">
            Announcements
            </div>
            <div class="col card">
            Live site activity
            </div>
        </div>
    </div>