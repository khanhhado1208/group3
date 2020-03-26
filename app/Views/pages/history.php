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
    if ($row->stonk_id > 1) {
      if ($row->stonk_amount < 0) {
        echo 'Sold '.(-$row->stonk_amount).' '.$row->stonk_name;
      } else {
        echo 'Purchased '.($row->stonk_amount).' '.$row->stonk_name;
      }
    } else {
      echo $row->tx_type;
    }
    echo "</td><td>";
    echo $row->tx_date;
    echo "</td><tr>";
}
?>
  </tbody>
</table>
</div>