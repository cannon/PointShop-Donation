<?php

//Basic functions for fetching and outputting steam account data
//Designed for use with Pointshop-Donation

//Fetch data about an array of steamid64's
function fetch_steam_info($players) {
	if(!is_array($players)){
		$players = array($players);
	}
	//Random STEAM API KEY from one of my old accounts. If it doesn't work then replace it.
	return json_decode(file_get_contents("http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=510D063C73EF5420C11545A82F194CA7&steamids=".implode(",",$players)),TRUE)["response"]["players"];
}

//Output a box showing info for the given steamid64. Players is the array returned by fetch_steam_info and desc is a custom subtext
function output_steam_info($id,$players,$desc) {
	?>
	<div class="blob steaminfo" onclick="location.href = 'http://steamcommunity.com/profiles/<?=$id?>';">
		<?php
		$player = null;
		if($players) {
			foreach($players as $p){
				if($p["steamid"] == $id) { $player = $p; }
			}
		}
		if($player) {
			$avatarstyle="";
			if($player["personastate"]>0) { $avatarstyle="border-color:#4477aa;"; }
			if(isset($player["gameid"])) { $avatarstyle="border-color:#77aa44;"; }
			?>
			<img src="<?=$player["avatarfull"]?>" alt="avatar" class="steamavatar" style="<?=$avatarstyle?>">
			<div>
				<span class="steamname"><?=htmlspecialchars($player["personaname"])?></span><br><span class="steamdesc"><?=$desc?></spam>
			</div>
			<?php
		} else {
			print("Steam Error ".$id);
		}
		?>
	</div>
	<?php
}

//Return this id64's username
function steam_name($id,$players) {
	$player = null;
	if($players) {
		foreach($players as $p){
			if($p["steamid"] == $id) { $player = $p; }
		}
	}
	if($player) {
		return htmlspecialchars($player["personaname"]);
	} else {
		return "Steam ID64: ".$id;
	}
}

//Convert id64 to the more familiar SteamID
function steamid64ToSteamid($id){
		function parseInt($string) {
	//    return intval($string);
	    if(preg_match('/(\d+)/', $string, $array)) {
	        return $array[1];
	    } else {
	        return 0;
	    }}

	// Convert SteamID64 into SteamID
	$subid = substr($id, 4);
	$steamY = parseInt($subid);
	$steamY = $steamY - 1197960265728;
	$steamX = 0;

	if ($steamY%2 == 1){
	$steamX = 1;
	} else {
	$steamX = 0;
	}
	$steamY = (($steamY - $steamX) / 2);
	$steamID = "STEAM_0:" . (string)$steamX . ":" . (string)$steamY;
	return $steamID;
}
?>