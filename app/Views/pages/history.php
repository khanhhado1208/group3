<div class="container">
<table class="table">
  <thead>
    <tr>
      <th scope="col">Value</th>
      <th scope="col">Type</th>
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
    if ($row->stonk_id > 1) {
      echo ' - '.$row->stonk_name.' - ';
      if ($row->stonk_amount < 0) {
        echo (-$row->stonk_amount).' Sold';
      } else {
        echo $row->stonk_amount.' Purchased';
      }
    }
    echo "</td><td>";
    echo $row->tx_date;
    echo "</td><tr>";
}
?>
  </tbody>
</table>
</div>