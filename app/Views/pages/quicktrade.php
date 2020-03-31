<div class="container">
    <br>

    <div class="row">
        <div class="col-md-9">
            <form action="<?php echo base_url('/TX/quicktrade') ?>" method="post" accept-charset="utf-8">
                <p class="h6" id="stonktext">Select the stonk you would like to trade:</p>
                <div class="form-group">

                    <div class="dropdown d-inline-block">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="stonk" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Choose stonk
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">

                            <?php
                            foreach ($stonkproperties as $stonk) {
                                echo '<button class="dropdown-item" onclick="selectStonk('.$stonk->stonk_id.',\''.$stonk->stonk_name.'\')" type="button">'.$stonk->stonk_name.'</button>';
                            }
                            ?>

                        </div>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button class="btn btn-secondary dropdown-toggle" hidden type="button" id="operationdd" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            ...
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                            <button class="dropdown-item" disabled id="buy" onclick="selectOperation('buy')" type="button">Buy</button>
                            <button class="dropdown-item" disabled id="sell" onclick="selectOperation('sell')" type="button">Sell</button>
                        </div>
                    </div>

                    <input type="number" min="1" name="amount" class="form-control" disabled hidden id="amount" placeholder="Stonk amount" oninput="updateValues('amount')" onkeyup="validateAmount()">
                    <input type="number" min="1" name="value" class="form-control" disabled hidden id="value" placeholder="Stonk value" oninput="updateValues('value')" onkeyup="validateValue()">
                    <button type="button" class="btn btn-yellow" disabled hidden onclick="setAmountFactor(0.5)">0.5x</button>
                    <button type="button" class="btn btn-yellow" disabled hidden onclick="setAmountFactor(2)">2x</button>
                    <button type="button" class="btn btn-yellow" disabled hidden onclick="setAmountFactor(10)">10x</button>
                    <button type="button" class="btn btn-yellow" disabled hidden onclick="setAmountFactor('max')">MAX</button>
                    <br>
                    <input type="hidden" name="stonkid" id="stonkid">
                    <input type="hidden" name="operation" id="operation">

                    <button type="submit" id="send_form" hidden disabled class="btn btn-success">Authorize transaction</button>
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

    let PriceArrayJSON = <?php echo json_encode($pricenow) ?>;
    let PriceArray = [];
    Object.keys(PriceArrayJSON).forEach(key => {
        PriceArray[key] = PriceArrayJSON[key];
    });
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
    let sendElement = document.getElementById("send_form");

    //TRANSACTION VARIABLES
    let stonkPrice;
    let stonkAmount;
    let maxAmount;

    //STONK DROPDOWN SELECTION
    function selectStonk(index, name) {
        stonkPrice = PriceArray[index];
        stonkidElement.value = index;
        stonkElement.innerHTML = name;

        amountElement.hidden = false;
        valueElement.hidden = false;
        sendElement.hidden = false;
        operationddElement.hidden = true;

        if (userStonks[index] > 0) {
            stonkText.innerHTML = "You currently have " + userStonks[index] + " of this stonk in your wallet.";
            sellElement.disabled = false;
            operationddElement.hidden = false;
        } else {
            stonkText.innerHTML = "You don't have any of this stonk in your wallet yet.";
            sellElement.disabled = true;
        }

        if (userBalance <= 0) {
            stonkText.innerHTML += " Current balance does not permit stonk purchases.";
            buyElement.disabled = true;
        } else {
            buyElement.disabled = false;
            operationddElement.hidden = false;
            selectOperation('buy');
        }

        let element = document.getElementsByClassName("btn btn-yellow");
        for (let i = 0; i < element.length; i++) {
            element[i].disabled = false;
            element[i].hidden = false;
        }
    }

    //BUY OR SELL DROPDOWN SELECTION
    function selectOperation(type) {
        if (userStonks[stonkidElement.value] > 0) {
            sellElement.disabled = false;
        } else {
            sellElement.disabled = true;
            buyElement.checked = true;
        }

        if (type == 'buy') {
            maxAmount = Math.floor(userBalance / stonkPrice);
            operationddElement.innerHTML = "Buy";
        } else if (type == 'sell') {
            maxAmount = userStonks[stonkidElement.value];
            operationddElement.innerHTML = "Sell";
        }

        if (maxAmount > 0) {
            sendElement.disabled = false;
            amountElement.disabled = false;
            valueElement.disabled = false;

            amountElement.value = 1;
            amountElement.max = maxAmount;

            valueElement.value = stonkPrice;
            valueElement.min = stonkPrice;
            valueElement.max = maxAmount * stonkPrice;
            valueElement.step = stonkPrice;
        } else {
            sendElement.disabled = true;
            amountElement.disabled = true;
            valueElement.disabled = true;
            buyElement.disabled = true;
            if (type == 'buy') {
                operationddElement.innerHTML = "...";
                stonkText.innerHTML += " Not enough funds to acquire at current price of " + stonkPrice + " per stonk.";
            }
        }

        operationElement.value = type;
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
        } else if (amountElement.value < 0) {
            valueElement.value = stonkPrice;
            amountElement.value = 1;
        }
    }

    function validateValue() {
        if (valueElement.value > maxAmount * stonkPrice) {
            valueElement.value = maxAmount * stonkPrice;
            amountElement.value = maxAmount;
        } else if (valueElement.value < 0) {
            valueElement.value = stonkPrice;
            amountElement.value = 1;
        }
    }
</script>