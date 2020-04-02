<div class="container">
    <div class="row">
        <div class="col card">
            <h4><?= $username ?>'s Wallet </h4>
            <h5>Balance: <?= $balance ?> STONK$ </h5>
            <a href='<?php echo base_url('/topup') ?>'>
                <button class="btn btn-light">Deposit</button>
            </a>
            <a href='<?php echo base_url('/withdraw') ?>'>
                <button class="btn btn-light">Withdraw</button>
            </a>
            <a href='<?php echo base_url('/history') ?>'>
                <button class="btn btn-light">History</button>
            </a>
        </div>
        <div class="col card">
            <h2>Owned stonks:</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Stonk</th>
                        <th scope="col">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
foreach ($userstonks as $row) {
    if ($row->stonk_amount > 0) {
        echo "<tr><td>";
        echo $row->stonk_name;
        echo "</td><td>";
        echo $row->stonk_amount;
        echo "</td></tr>";
    }
}
?>
                </tbody>
            </table>

        </div>
    </div>
</div>