<html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script type="text/javascript"
      src="https://app.sandbox.midtrans.com/snap/snap.js"
      data-client-key="Mid-client-xQsbqROz9on69pPc"></script>
  </head>

  <body>
    <button id="pay-button">Pay!</button>

    <script type="text/javascript">
      
      var payButton = document.getElementById('pay-button');
      payButton.addEventListener('click', function () {
        
        snap.pay('d9313e19-d335-4c79-bd1e-40f4c551931f', {
          onSuccess: function(result) {
              console.log('Payment successful', result);

              let data = {
                  api_token: '2619e7d67278a0aeea8666847636d3e135142cb007bc41331ffab7f3ebee3d1d',
                  order_id: result.order_id,
                  gross_amount: result.gross_amount,
                  payment_type: result.payment_type,
                  transaction_id: result.transaction_id,
                  transaction_time: result.transaction_time,
              }

              console.log("reeeee")
              console.log(data);

              $.ajax({
                type: "POST",
                url: "http://127.0.0.1:8000/v2/finish-payment",
                data: data,
                success: function (response) {
                  console.log(response);
                }
          });
          },
          onPending: function(result) {
              console.log('Payment pending', result);
          },
          onError: function(result) {
              console.log('Payment error', result);
          }
        });
      });
    </script>
  </body>
</html>