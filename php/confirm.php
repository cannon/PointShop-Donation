<?php include 'common_head.php'; ?>
		<title><?=SERVER_NAME?> - Donate</title>
<?php include 'common_body.php'; ?>

	<h2>Confirm Donation</h2>
		<div class="blob center">
			<?php
			session_start();
			if(isset($_SESSION["steamid"])) {
		   		$steamid=$_SESSION["steamid"];
		   	} else {
		   		$error="You're not signed in! Please go back and sign in through Steam.";
		   	}
		   	if(isset($_GET["amount"])){
		   		$amount=$_GET["amount"];
		   		if(is_numeric($amount)){
		   			if($amount>=100 && $amount<=500000) { //Sanity check
		   				//Success...
		   				$db=sql_start();
		   			} else {
		   				$error="Please donate at least $1.00.";
		   			}
		   		} else {
		   			$error="Invalid donation amount.";
		   		}
		   	} else {
		   		$error="Invalid donation amount.";
		   	}

		   	if(isset($error)) {
		   		?>
		   		<strong style="font-size:16pt"><?=$error?></strong><br><br>
		   		<a href="index.php">&lt;&lt; Go Back</a>
		   		<?php
		   	} else {
		   		?>
		   		<div class="finalinfo" style="width:250px;margin-left:200px;">
			   		You are about to donate:<br>
			   		You will recieve:<br>
			   		To your account:
			   	</div>
			   	<div class="finalinfo" style="width:400px;">
			   		<strong><?=dollars($amount)?></strong><br>
			   		<strong><?=number_format(calculate_donation_increment($steamid,$db,$amount))?></strong> Points<br>
			   		<strong>
			   			<?php
			   			$players = fetch_steam_info($steamid);
			   			echo htmlspecialchars(steam_name($steamid, $players));
			   			?>
			   		</strong>
			   		(<?= steamid64ToSteamid((string)$steamid) ?>)
			   	</div>
			   	<div style="clear:both;">
			   		<br>
			   		<strong>By paying you agree to the <a href="#policy">Donation Policy</a>.</strong>
			   		<form style="margin:12px;" action="finish.php" method="POST">
					  <script
					    src="https://checkout.stripe.com/checkout.js"
					    class="stripe-button"
					    data-key="<?=STRIPE_PUBLIC_KEY?>"
					    data-image="<?=SERVER_LOGO?>"
					    data-name="<?=SERVER_NAME?>"
					    data-description="Donation"
					    data-amount="<?=$amount?>">
					  </script>
					  <input type="hidden" name="amount" value="<?=$amount?>">
					  <!-- This is a workaround so we don't have to use the slow as heck STEAM API again. -->
					  <input type="hidden" name="steam_personaname" value="<?=htmlspecialchars($players[0]["personaname"])?>">
					  <input type="hidden" name="steam_personastate" value="<?=htmlspecialchars($players[0]["personastate"])?>">
					  <input type="hidden" name="steam_gameid" value="<?=htmlspecialchars($players[0]["gameid"])?>">
					  <input type="hidden" name="steam_avatarfull" value="<?=htmlspecialchars($players[0]["avatarfull"])?>">
					</form>
				</div>
				Payment processing is handled securely by <a href="https://stripe.com/" class="hoverul">Stripe</a>. 
				<br><strong>No sensitive information will ever cross our server!</strong>
				<br><br>
				<a href="index.php">&lt;&lt; Cancel</a>

		   		<?php
		   	}   
		   	?>
			
		</div>

		<br>
		<hr>

		<h2 id="policy">Donation Policy</h2>
		<div style="padding:10px 50px;">
			<!--Sample donation policy-->
			Your donation is exactly that - a <em>donation</em>.
			You are not immune to the rules, and if you become banned from our server, you will not be refunded.
			If donation rewards are removed, or if the server shuts down entirely, you are not entitled to any alternate rewards or refunds.
			<br><br>
			<strong>DO NOT USE A STOLEN CREDIT CARD TO DONATE!</strong>
			If the payment is contested or chargebacked at any time, you will be <em>permanently banned</em> from our server.
			Refunds are not offered except in special situations, such as the unauthorized use of a credit card.
		</div>

<?php include 'common_foot.php'; ?>
