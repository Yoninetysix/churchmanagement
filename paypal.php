<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Page</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AfNrWd12WzXv70BILjT4s_9Cki1SNLbnkl_E5jp1SM9rq21VzHLzHCZkYZJdZuOkVje7xiHImwIofCAa&components=buttons"></script>
</head>
<body>
    <h2>Secure Donation</h2>

    <div>
        <button id="donateNow">Donate $10</button>
    </div>

    <!-- Container for PayPal button -->
    <div id="paypal-button-container"></div>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                // Create order here
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '10'  // Set the donation amount here dynamically
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Payment has been approved by the user
                    alert('Donation Successful! Thank you, ' + details.payer.name.given_name);

                    // Optionally, make a call to the server to save the payment information
                    fetch('process_payment.php', {
                        method: 'POST',
                        body: JSON.stringify({
                            orderID: data.orderID,
                            payerID: data.payerID,
                            amount: 10,
                            donation_type: 'once'
                        })
                    }).then(response => response.json()).then(response => {
                        console.log(response);
                    }).catch(error => {
                        console.error('Error:', error);
                    });
                });
            },
            onError: function(err) {
                // Handle errors here
                console.log('Error:', err);
            }
        }).render('#paypal-button-container');  // Render PayPal button in the container
    </script>
</body>
</html>
