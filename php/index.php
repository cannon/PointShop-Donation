<?php include 'common_head.php'; ?>
		<title><?=SERVER_NAME?> - Donate</title>
<?php include 'common_body.php'; ?>

	<h2>Donate</h2>

	<h3>Why donate?</h3>

	<p>
		<!-- Sample "blurb" -->
		Our server is hosted on a high-performance dedicated service, which allows us to support our players with near-24/7 uptime.
		However, this service comes at a cost, and to fund it, we offer players the ability to donate for Pointshop Points.
		These points can be spent on a variety of items.
		<br>All donations will be put toward the upkeep of this community!
	<p>

	<h3>Interested?</h3>

	<?php
	if (isset($_GET["logout"])) {
		session_start();
		session_destroy();
		session_regenerate_id(TRUE);
	}
	session_start();
	if(isset($_SESSION["steamid"])) {
   		$steamid=$_SESSION["steamid"];
   	}
	error_reporting(E_ALL & ~E_NOTICE);
	$steamsignin = new SteamSignIn();
	if(!isset($steamid)) {
		$steamid = $steamsignin->validate();
		if($steamid) {
			$_SESSION["steamid"]=$steamid;
		}
	}
	?>

	<h2>Step 1: Sign in through Steam<?php if($steamid){ ?> <img src="check.png" style="width:60px;" alt="Validated"><?php } ?></h2>

	<?php
	$current_points=0;
	$past_donations=0;
	$db=sql_start();
	if($steamid){
		$pdata = sql_get_info($steamid, $db);
		$current_points = $pdata["points"];
		$past_donations = $pdata["donation_total"];
		$infostring = $current_points." Points";
		if($past_donations>0) { 
			$infostring .= ", ".dollars($past_donations)." Donated";
		}
		$players = fetch_steam_info($steamid);
		output_steam_info($steamid,$players,$infostring);
		?>
		<p style="margin-top:0px;">
			Validated Successfully - <a href="index.php?logout" class="hoverul">Sign Out</a>
		</p>
		<?php	
	} else {
		?>
		<p>
		<a href="<?=$steamsignin->genUrl()?>"><img src="steamsignin.png" class="steambutton" alt="Sign in through Steam"></a>
		<br>
		Click this button to sign in through your Steam account.<br>
		This is only for validation - no private account information will be shared with us!
		</p>
		<?php
	}
	?>

	<h2>Step 2: Choose Amount</h2>

	<?php
	function output_fixedamt($price) {
	?>
	<div class="fixedamt">
		<h2><?=dollars($price)?></h2>
		<p>
			<span class="reward">You will recieve:</span><br>
			<span class="points"><?=number_format(POINTS_PER_DOLLAR*floor($price/100))?> Points</span><br>
			<button onclick="location.href = 'confirm.php?amount=<?=$price?>'">Select</button>
		</p>
	</div>
	<?php
	}

	//Output $5 and $10 as recommended amounts
	output_fixedamt(500);
	output_fixedamt(1000);
	?>	

	<div class="settableamt">
		<h2>Custom Amount</h2>
		<p>
			<span class="reward">You will recieve:</span><br>
			<span class="points"><?=number_format(POINTS_PER_DOLLAR)?> Points Per Dollar</span><br>
			<strong>$ </strong><input type="text" id="dollars" size="3" value="15">
			<button onclick="location.href = 'confirm.php?amount='+dollarAmount()">Select</button>
			<script>
			function dollarAmount() {
				return Math.floor(parseFloat(document.getElementById("dollars").value)*100);
			}
			</script>
		</p>
	</div>

	<p id="lognote">
		<strong>Your donation will be permanently logged.</strong><br>
		If donation benefits are changed in the future, you will be credited/promoted automatically.
	</p>
	
<?php include 'common_foot.php'; ?>
