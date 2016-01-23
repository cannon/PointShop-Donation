<?php
// PointShop-Donation by Jackson Cannon
// https://github.com/jackson-c/PointShop-Donation
// This software is provided as-is, without warranty.

// WARNING: I AM NOT RESPONSIBLE FOR ANY LOSS OR DAMAGES THAT MAY COME FROM THE USE OF THIS SOFTWARE!
// In particular, note that users might issue chargebacks and incurr fines against you.
// This is a risk you take by accepting donations and I am not responsible for the actions of malicious/fraudulent users.

//Title of website
define('SERVER_NAME', 'My Server');

//Logo of server
define('SERVER_LOGO', 'https://upload.wikimedia.org/wikipedia/commons/a/a0/GMod-logo.png');

//Stripe Keys, get from Stripe dashboard
//Be sure to use live keys when you release it!
define('STRIPE_PUBLIC_KEY', 'pk...');
define('STRIPE_SECRET_KEY', 'sk...');

//Points per dollar of donation
//If this is changed, and someone donates again, they will be retroactively credited.
define('POINTS_PER_DOLLAR', '1000');

//DSN to connect to mysql. If you are connecting to localhost, simply change mydatabase
define('SQL_DSN', 'mysql:dbname=mydatabase');

//MySQL credentials
define('SQL_USER', 'username');
define('SQL_PASS', 'password');

?>