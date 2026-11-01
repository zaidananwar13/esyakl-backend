<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test</title>
    <script id="midtrans-script" type="text/javascript" src="https://api.midtrans.com/v2/assets/js/midtrans-new-3ds.min.js" data-environment="sandbox" data-client-key="SB-Mid-client-v-hBoxDI4iRKeoxi"></script>
</head>
<body>
    <button onclick="clickMeh()">Click Meh</button>
    
    <script>
        function clickMeh() {
            // card data from customer input, for example
            var cardData = {
            "card_number": 4811111111111114,
            "card_exp_month": 02,
            "card_exp_year": 2025,
            "card_cvv": 123,
            };

            // callback functions
            var options = {
            onSuccess: function(response){
                // Success to get card token_id, implement as you wish here
                console.log('Success to get card token_id, response:', response);
                var token_id = response.token_id;

                console.log('This is the card token_id:', token_id);
                // Implement sending the token_id to backend to proceed to next step
            },
            onFailure: function(response){
                // Fail to get card token_id, implement as you wish here
                console.log('Fail to get card token_id, response:', response);

                // you may want to implement displaying failure message to customer.
                // Also record the error message to your log, so you can review
                // what causing failure on this transaction.
            }
            };

            // trigger `getCardToken` function
            MidtransNew3ds.getCardToken(cardData, options);
        }
    </script>
</body>
</html>