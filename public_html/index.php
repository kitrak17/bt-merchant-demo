<?php require_once("../includes/braintree_init.php"); ?>

<html>
<?php require_once("../includes/head.php"); ?>
<body>

    <?php require_once("../includes/header.php"); ?>

    <div class="wrapper">
        <div class="checkout container">

            <header>
                <h1>Hi, <br>Let's test a transaction</h1>
                <p>
                    Make a test payment with Braintree using PayPal or a card
                </p>
            </header>

            <form method="post" id="payment-form" action="<?php echo $baseUrl;?>checkout.php">
                <section>
                    <label for="amount">
                        <span class="input-label">Amount</span>
                        <div class="input-wrapper amount-wrapper">
                            <input id="amount" name="amount" type="tel" min="1" placeholder="Amount" value="10">
                        </div>
                    </label>

                    <div class="bt-drop-in-wrapper">
                        <div id="bt-dropin"></div>
                    </div>
                </section>

                <input id="nonce" name="payment_method_nonce" type="hidden" />
                <button class="button" type="submit"><span>Test Transaction</span></button>
            </form>
        </div>
    </div>

    <script src="https://js.braintreegateway.com/web/dropin/1.23.0/js/dropin.min.js"></script>
    <script>
        var form = document.querySelector('#payment-form');
        var client_token = "<?php echo($gateway->ClientToken()->generate()); ?>";

        braintree.dropin.create({
          authorization: client_token,
          selector: '#bt-dropin',
          threeDSecure: true,
          paypal: {
            flow: 'vault'
          }
        }, function (createErr, instance) {
          if (createErr) {
            console.log('Create Error', createErr);
            return;
          }

          //3DS
          var threeDSecureParameters = {
            amount: '500.00',
            email: 'test@example.com',
            billingAddress: {
              givenName: 'Jill', // ASCII-printable characters required, else will throw a validation error
              surname: 'Doe', // ASCII-printable characters required, else will throw a validation error
              phoneNumber: '8101234567',
              streetAddress: '555 Smith St.',
              extendedAddress: '#5',
              locality: 'Oakland',
              region: 'CA',
              postalCode: '12345',
              countryCodeAlpha2: 'US'
            },
            additionalInformation: {
              workPhoneNumber: '8101234567',
              shippingGivenName: 'Jill',
              shippingSurname: 'Doe',
              shippingPhone: '8101234567',
              shippingAddress: {
                streetAddress: '555 Smith St.',
                extendedAddress: '#5',
                locality: 'Oakland',
                region: 'CA',
                postalCode: '12345',
                countryCodeAlpha2: 'US'
              }
            },
          };
          var my3DSContainer = document.createElement('div');

          form.addEventListener('submit', function (event) {
            event.preventDefault();

            instance.requestPaymentMethod({
              threeDSecure: threeDSecureParameters
              }, function (err, payload) {
              if (err) {
                console.log('Request Payment Method Error', err);
                return;
              }

              // Add the nonce to the form and submit
              document.querySelector('#nonce').value = payload.nonce;
              form.submit();
            });
          });
        });
    </script>
    <script src="javascript/demo.js"></script>
</body>
</html>
