<table class="table">
  <thead>
    <tr>
      <th scope="col">Value</th>
      <th scope="col">Type</th>
      <th scope="col">Stonk Name</th>
      <th scope="col">Date</th>
    </tr>
  </thead>
  <tbody>
  
<?php
foreach (array_reverse($history) as $row) {
    echo "</tr><td>";
    echo $row->tx_value;
    echo "</td><td>";
    echo $row->tx_type;
    echo "</td><td>";
    echo $row->stonk_name;
    echo "</td><td>";
    echo $row->tx_date;
    echo "</td><tr>";
}
?>
  </tbody>
</table>

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