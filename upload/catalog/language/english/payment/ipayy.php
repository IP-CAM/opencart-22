<?php

$_['ipayy_logo_url']				 = 'https://portal.ipayy.com/v001/images/logo.png';

// Text
$_['text_title']    = '<img src="' . $_['ipayy_logo_url'] . '" alt="iPayy" title="iPayy" height="30" /> &ndash; Pay by your mobile';

$_['ERROR_INVALID_PARAMS'] = "We didn't receive valid parameters from the gateway.";

$_['ERROR_EXCEPTION'] = 'Received an invalid response from iPayy Server. We do not know the payment status. Please contact us.';

$_['ERROR_UC'] = 'You cancelled the transaction.';
$_['ERROR_ST'] = 'Session timed out. Please try again.';
$_['ERROR_PF'] = 'Too many failure attempts.';
$_['ERROR_LB'] = 'Your mobile balance is low to complete the transaction. Please try with some other mobile number.';
$_['ERROR_IS'] = 'Your mobile number does not belong to the operator selected. Please try again.';
$_['ERROR_OF'] = 'We were unable to bill you. Please try again.';
$_['ERROR_OTHER'] = 'We were unable to bill you. Please try again.';

$_['ERROR_FRAUD'] = 'Origin of the transaction could not be detected. Please try again.';

$_['text_payment_title'] = 'Payment failed';

$_['text_payment_error'] = '<p>Sorry, your order has not been processed. <p></p>Unfortunately there was an error while processing your payment.</p><p>Please, try again and verify the payment information.</p><p>We apologize for any inconvenience, please <a href="%s">contact us</a> if you are having difficulties making your payment.</p>';
$_['text_basket']   = 'Basket';
$_['text_checkout'] = 'Checkout';
$_['text_error']  = 'Error';
?>