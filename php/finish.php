<?php include 'common_head.php'; ?>
		<title><?=SERVER_NAME?> - Thanks!</title>
<?php include 'common_body.php'; ?>
	
	<?php
	session_start();
	if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION["steamid"]) && isset($_POST["stripeToken"]) && isset($_POST["stripeEmail"]) && isset($_POST["amount"])) {

		$amount=$_POST["amount"];

		if($amount>=100 && $amount<=500000) {

			require_once('vendor/autoload.php');

			$token = $_POST['stripeToken'];

			try {
			    // Set your secret key
				\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
			    $charge = \Stripe\Charge::create(array(
			        'amount' => $amount, //Amount in cents
			        'currency' => 'usd',
			        'source' => $token,
			        'description' => 'Donation'
			    ));

			    //If the charge fails, we won't reach this point.
			    $db = sql_start();
			    $steamid = $_SESSION["steamid"];
			    $info = sql_get_info($steamid, $db);
			    $donationpoints = calculate_donation_increment($steamid, $db, $amount);

			    //Log charge
			    $db->query("INSERT INTO charges (id64, email, amount, points_before, points_after)
			    			VALUES (".$db->quote($steamid).", ".$db->quote($_POST['stripeEmail']).", ".$db->quote($amount).", ".$db->quote($info["points"]).", ".$db->quote($info["points"]+$donationpoints).")");

			    //Credit user
			    $info["points"]+=$donationpoints;
			    $info["donation_total"]+=$amount;
			    $info["donation_credited"]+=$donationpoints;
			    sql_set_info($steamid, $db, $info);

			    ?>
			    <h2>Donation complete - Thank you!</h2>
			    <?php
			    	$pdata = sql_get_info($steamid, $db);
					$current_points = $pdata["points"];
					$past_donations = $pdata["donation_total"];
					$infostring = $current_points." Points";
					if($past_donations>0) { 
						$infostring .= ", ".dollars($past_donations)." Donated";
					}
					//Workaround so we don't have to use the STEAM API again...
					output_steam_info($steamid,array(0=>array("steamid"=>$steamid,"personaname"=>$_POST["steam_personaname"],"personastate"=>$_POST["steam_personastate"],"gameid"=>$_POST["steam_gameid"],"avatarfull"=>$_POST["steam_avatarfull"])),$infostring);
			    ?>
			    <p>Please rejoin the game for your points to update on the server.</p>
			    <?php
			} catch (\Stripe\Error\ApiConnection $e) {
			   output_error("Networking error ApiConnection");
			} catch (\Stripe\Error\InvalidRequest $e) {
			    output_error("Error: Form was resubmitted");
			} catch (\Stripe\Error\Api $e) {
			    output_error("Networking error Api");
			} catch (\Stripe\Error\Card $e) {
			    output_error("Error: Your card could not be charged!");

			    //Uncomment for more info about why cards can't be charged
			    //$e_json = $e->getJsonBody();
			    //print_r($e_json['error']);
			}
		} else {
			output_error("Invalid data recieved!");
		}
	} else { 
		output_error("Error submitting form! Is javascript enabled?");
	} 

	function output_error($error) {
		?><h2><?=$error?></h2><?php
	}
	?>

	<div>
		<p>If you have any problems with this automated donation processing system, please notify the owner!</p>
		<p><a href="index.php">&lt;&lt; Go Back</a></p>
	</div>

<?php include 'common_foot.php'; ?>
