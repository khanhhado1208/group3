<table class="table">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Value</th>
      <th scope="col">Type</th>
      <th scope="col">Date</th>
    </tr>
  </thead>
  <tbody>
  
<?php 
foreach ($history as $row)
    {
        echo "<tr><td>";
        echo $row->tx_id;
        echo "</td><td>";
        echo $row->tx_value;
        echo "</td><td>";
        echo $row->tx_type;
        echo "</td><td>";
        echo $row->tx_date;
        echo "</td><tr>";
    }
?>
  </tbody>
</table>