<div class="container">
    <br>

    <div class="row">
        <div class="col-md-9">
            <form action="<?php echo base_url('/TX/quicktrade') ?>" method="post" accept-charset="utf-8">
                <p class=h3 id="stonk"></p>
                <p class="h6" id="stonktext"></p>
                <div class="form-group">

                    <div class="dropdown d-inline-block">
                        <button class="btn btn-secondary dropdown-toggle" hidden type="button" id="operationdd" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            ...
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <button class="dropdown-item" disabled id="buy" onclick="selectOperation('buy')" type="button">Buy</button>
                            <button class="dropdown-item" disabled id="sell" onclick="selectOperation('sell')" type="button">Sell</button>
                        </div>
                    </div>

                    <input type="number" min="1" name="amount" class="form-control" hidden id="amount" placeholder="Stonk amount" oninput="calculatePrice()">
                    <input type="number" min="1" name="value" class="form-control" hidden id="value" placeholder="Stonk value" oninput="calculateAmount()">
                    <button type="button" class="btn btn-yellow" hidden onclick="setAmountFactor(0.5)">0.5x</button>
                    <button type="button" class="btn btn-yellow" hidden onclick="setAmountFactor(2)">2x</button>
                    <button type="button" class="btn btn-yellow" hidden onclick="setAmountFactor(10)">10x</button>
                    <button type="button" class="btn btn-yellow" hidden onclick="setAmountFactor('max')">MAX</button>
                    <br>
                    <input type="hidden" name="stonkid" id="stonkid">
                    <input type="hidden" name="operation" id="operation">

                    <button type="submit" id="send_form" hidden class="btn btn-success">Authorize transaction</button>
                </div>
            </form>
        </div>

    </div>

</div>

<script>
    //ELEMENT VARIABLES
    let stonkidElement = document.getElementById("stonkid");
    let operationElement = document.getElementById("operation");
    let stonkElement = document.getElementById("stonk");
    let operationddElement = document.getElementById("operationdd");
    let stonkText = document.getElementById("stonktext");
    let buyElement = document.getElementById("buy");
    let sellElement = document.getElementById("sell");
    let amountElement = document.getElementById("amount");
    let valueElement = document.getElementById("value");
    let factorElements = document.getElementsByClassName("btn btn-yellow");
    let sendElement = document.getElementById("send_form");

    //TRANSACTION VARIABLES
    let stonkPrice;
    let stonkAmount;
    let maxAmount;

    //INITIALIZE AND PARSE DATA FROM PHP
    let userBalance = <?php
        if (is_numeric($balance)) {
            echo $balance;
        } else {
            echo 0;
        }?>;

    let userStonksJSON = <?php echo json_encode($userstonks) ?>;
    let userStonks = [];
    for (let i = 0; i < userStonksJSON.length; i++) {
        userStonks[userStonksJSON[i].stonk_id] = parseInt(userStonksJSON[i].stonk_amount);
    }

    let stonksJSON = <?php echo json_encode($stonkproperties) ?>;
    let stonkNames = [];
    for (let i = 0; i < stonksJSON.length; i++) {
        stonkNames[stonksJSON[i].stonk_id] = stonksJSON[i].stonk_name;
    }

    let stonkPrices = <?php echo json_encode($pricenow) ?>;

    //CHECK URL STRING FOR STONK SELECTION
    let url = window.location.href;
    let index = url.substr(url.lastIndexOf('/') + 1);
    if (index != "quicktrade") {
        if (!(index in stonkPrices)) {
            stonkElement.innerHTML = "Unknown Stonk";
            stonkText.innerHTML = "The specified stonk was not found.";
        } else {
            stonkPrice = stonkPrices[index];
            stonkidElement.value = index;
            stonkElement.innerHTML = stonkNames[index];

            if (userStonks[index] > 0) {
                stonkText.innerHTML = "You currently have " + userStonks[index] + " of this stonk in your wallet.";
                sellElement.disabled = false;
                showInputs();
                selectOperation('sell');
            } else {
                stonkText.innerHTML = "You don't have any of this stonk in your wallet yet.";
                sellElement.disabled = true;
            }

            if (userBalance < stonkPrice) {
                stonkText.innerHTML += " Not enough funds to acquire at current price of " + stonkPrice + " per stonk.";
                buyElement.disabled = true;
            } else {
                buyElement.disabled = false;
                selectOperation('buy');
                showInputs();
            }
        }
    }

    //BUY OR SELL DROPDOWN SELECTION
    function selectOperation(type) {
        if (type == 'buy') {
            maxAmount = Math.floor(userBalance / stonkPrice);
            operationddElement.innerHTML = "Buy";
        } else if (type == 'sell') {
            maxAmount = userStonks[stonkidElement.value];
            operationddElement.innerHTML = "Sell";
        }

        amountElement.value = 1;
        amountElement.max = maxAmount;

        valueElement.value = stonkPrice;
        valueElement.min = stonkPrice;
        valueElement.max = maxAmount * stonkPrice;
        valueElement.step = stonkPrice;

        operationElement.value = type;
    }

    //ELEMENT VISIBILITY FUNCTION
    function showInputs() {
        operationddElement.hidden = false;
        amountElement.hidden = false;
        valueElement.hidden = false;
        for (let i = 0; i < factorElements.length; i++) {
            factorElements[i].hidden = false;
        }
        sendElement.hidden = false;
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

    //INPUT CALCULATORS AND VALIDATORS
    function calculatePrice() {
        validateInput();
        stonkAmount = amountElement.value;
        valueElement.value = stonkPrice * stonkAmount;
    }

    function calculateAmount() {
        validateInput();
        stonkAmount = Math.floor(valueElement.value / stonkPrice);
        amountElement.value = stonkAmount;
    }

    function validateInput() {
        if (amountElement.value > maxAmount || valueElement.value > maxAmount * stonkPrice) {
            valueElement.value = maxAmount * stonkPrice;
            amountElement.value = maxAmount;
        } else if (amountElement.value < 0 || valueElement.value < 0) {
            valueElement.value = stonkPrice;
            amountElement.value = 1;
        }
    }
</script>