<div class="container">
    <br>
 
    <div class="row">
      <div class="col-md-9">
        <form action="<?php echo base_url('/account/quicktrade') ?>" method="post" accept-charset="utf-8">
            <div class="form-group">
                    <p class="h6">I want to..</p>
                    <div class="radio">
                    <label><input type="radio" name="operation" value="buy" checked>Buy</label>
                    </div>
                    <div class="radio">
                    <label><input type="radio" name="operation" value="sell">Sell</label>
                    </div>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="stonk" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Choose stonk
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <?php
                    
                    for ($i = 0; $i < count($stonkids); $i++) {
                      echo '<button class="dropdown-item" onclick="selectStonk('.$stonkids[$i].',\''.$stonknames[$i].'\')" type="button">'.$stonknames[$i].'</button>';
                    }

                    ?>
                </div>
            </div>
            <input type="number" min="1" name="amount" class="form-control" disabled id="amount" placeholder="Stonk amount" oninput="calculatePrice()">
            <input type="number" min="1" name="value" class="form-control" readonly id="value" placeholder="Stonk value">
            <input type="hidden" name="stonkid" id="stonkid">
            
           <button type="submit" id="send_form" class="btn btn-success">Authorize transaction</button>
          </div>
        </form>
      </div>
 
    </div>
  
</div>

<script>

let stonkId;
let stonkName;
let stonkPrice;
let stonkAmount;

function selectStonk(index, name) {
  let element = document.getElementById("stonk");
  element.innerHTML = name;

  element = document.getElementById("amount");
  element.disabled = false;
  element.value = 1;

  stonkPrice = Math.floor(Math.random() * 100) + 1;

  element = document.getElementById("value");
  element.value = stonkPrice;

  element = document.getElementById("stonkid");
  element.value = index;
}

function calculatePrice() {
  let element = document.getElementById("amount");
  stonkAmount = element.value;

  element = document.getElementById("value");
  element.value = stonkPrice * stonkAmount;
}

</script>