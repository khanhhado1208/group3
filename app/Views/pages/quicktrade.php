<div class="container">
    <br>
 
    <div class="row">
      <div class="col-md-9">
        <form action="<?php echo base_url('/account/quicktrade') ?>" method="post" accept-charset="utf-8">
            <div class="form-group">
                    <p class="h6">I want to..</p>
                    <div class="radio">
                    <label><input type="radio" name="operation" disabled id="buy" value="buy" checked onclick="selectOperation()">Buy</label>
                    </div>
                    <div class="radio">
                    <label><input type="radio" name="operation" disabled id="sell" value="sell" onclick="selectOperation()">Sell</label>
                    </div>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="stonk" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Choose stonk
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                    <?php
                    
                    for ($i = 0; $i < count($stonkids); $i++) {
                      echo '<button class="dropdown-item" onclick="selectStonk('.$stonkids[$i].',\''.$stonknames[$stonkids[$i]].'\')" type="button">'.$stonknames[$stonkids[$i]].'</button>';
                    }

                    ?>
                </div>
            </div>
            <input type="number" min="1" name="amount" class="form-control" disabled id="amount" placeholder="Stonk amount" oninput="updateValues('amount')" onkeyup="validateAmount()">
            <input type="number" min="1" name="value" class="form-control" disabled id="value" placeholder="Stonk value" oninput="updateValues('value')" onkeyup="validateValue()">
            <button type="button" class="btn btn-yellow" disabled onclick="setAmountFactor(0.5)">0.5x</button>
            <button type="button" class="btn btn-yellow" disabled onclick="setAmountFactor(2)">2x</button>
            <button type="button" class="btn btn-yellow" disabled onclick="setAmountFactor(10)">10x</button>
            <button type="button" class="btn btn-yellow" disabled onclick="setAmountFactor('max')">MAX</button><br>
            <input type="hidden" name="stonkid" id="stonkid">
            
           <button type="submit" id="send_form" class="btn btn-success">Authorize transaction</button>
          </div>
        </form>
      </div>
 
    </div>
  
</div>

<script>

//INITIALIZE AND PARSE DATA FROM PHP
let userBalance = <?php echo $balance ?>;
let userStonksJSON = <?php echo json_encode($userstonks) ?>;
let userStonks = [];
for (let i = 0; i < userStonksJSON.length; i++) {
  userStonks[userStonksJSON[i].stonk_id] = parseInt(userStonksJSON[i].stonk_amount);
}
//ELEMENT VARIABLES
let stonkidElement = document.getElementById("stonkid");
let stonkElement = document.getElementById("stonk");
let buyElement = document.getElementById("buy");
let sellElement = document.getElementById("sell");
let amountElement = document.getElementById("amount");
let valueElement = document.getElementById("value");

let stonkPrice;
let stonkAmount;
let maxAmount;

//STONK DROPDOWN SELECTION
function selectStonk(index, name) {
  stonkPrice = Math.floor(Math.random() * 100) + 1;

  stonkidElement.value = index;
  stonkElement.innerHTML = name;
  amountElement.disabled = false;
  valueElement.disabled = false;
  buyElement.disabled = false;

  selectOperation();

  let element = document.getElementsByClassName("btn btn-yellow");
  for (let i = 0; i < element.length; i++) {
    element[i].disabled = false;
  }
}

//BUY OR SELL RADIO BUTTON SELECTION
function selectOperation() {
  if (userStonks[stonkidElement.value] > 0) {
    sellElement.disabled = false;
  } else {
    sellElement.disabled = true;
    buyElement.checked = true;
  }

  if (buyElement.checked) {
    maxAmount = Math.floor(userBalance / stonkPrice);
  } else {
    maxAmount = userStonks[stonkidElement.value];
  }

  amountElement.value = 1;
  amountElement.max = maxAmount;

  valueElement.value = stonkPrice;
  valueElement.min = stonkPrice;
  valueElement.max = maxAmount * stonkPrice;
  valueElement.step = stonkPrice;
}

//ONINPUT VALUE UPDATER AND CALCULATORS
function updateValues(caller) {
  if (caller == 'amount') {
    calculatePrice();
  } else if (caller == 'value') {
    calculateAmount();
  }
}

function calculatePrice() {
  stonkAmount = amountElement.value;
  valueElement.value = stonkPrice * stonkAmount;
}

function calculateAmount() {
  stonkAmount = Math.floor(valueElement.value / stonkPrice);
  amountElement.value = stonkAmount;
}

//FACTOR INCREASE AND DECREASE FUNCTION
function setAmountFactor(factor) {
  if (factor == 'max') {
    amountElement.value = maxAmount;
  } else {
    amountElement.value = Math.floor(factor * amountElement.value);
  }

  if (amountElement.value > maxAmount) amountElement.value = maxAmount;
  else if (amountElement.value < 1) amountElement.value = 1;

  calculatePrice();
}

//INPUT VALIDATORS
function validateAmount() {
  if (amountElement.value > maxAmount) {
    valueElement.value = maxAmount * stonkPrice;
    amountElement.value = maxAmount;
  } else if (amountElement.value < 1) {
    valueElement.value = stonkPrice;
    amountElement.value = 1;
  }
}

function validateValue() {
  if (valueElement.value > maxAmount * stonkPrice) {
    valueElement.value = maxAmount * stonkPrice;
    amountElement.value = maxAmount;
  } else if (valueElement.value < 1) {
    valueElement.value = stonkPrice;
    amountElement.value = 1;
  }
}

</script>