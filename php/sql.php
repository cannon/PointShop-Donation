<?php

	//SQL config in config.php

	function sql_start() {
		$db = new PDO(SQL_DSN, SQL_USER, SQL_PASS);
		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		//Remove this section to skip checking for tables
		if(!tableExists($db, 'users')) {
			$db->query("
			CREATE TABLE IF NOT EXISTS `users` (
			  `id64` bigint(20) unsigned NOT NULL,
			  `points` int(10) unsigned NOT NULL DEFAULT '0',
			  `items` text NOT NULL,
			  `donation_total` int(10) unsigned NOT NULL DEFAULT '0',
			  `donation_credited` int(10) unsigned NOT NULL DEFAULT '0',
			  `last_updated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
			);

			ALTER TABLE `users` ADD PRIMARY KEY (`id64`);
			");
		}
		if(!tableExists($db, 'charges')) {
			$db->query("
			CREATE TABLE IF NOT EXISTS `charges` (
			  `id64` bigint(20) unsigned NOT NULL,
			  `email` text NOT NULL,
			  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  `amount` int(10) unsigned NOT NULL,
			  `points_before` int(10) unsigned NOT NULL,
			  `points_after` int(10) unsigned NOT NULL
			);
			");
		}
		// ^ Remove this section to skip checking for tables ^

		return $db;
	}

	function tableExists($pdo, $table) {
	    try {
	        $result = $pdo->query("SELECT 1 FROM $table LIMIT 1");
	    } catch (Exception $e) {
	        // We got an exception == table not found
	        return FALSE;
	    }
	    return $result !== FALSE;
	}

	//Return info about a user
	function sql_get_info($id, $db)  {	
		$found = $db->query("SELECT * FROM users WHERE id64 = ".$db->quote($id)." LIMIT 1")->fetch(PDO::FETCH_ASSOC);
		if(empty($found)) {
			return array("id64"=>$id,"points"=>0,"donation_total"=>0,"donation_credited"=>0);
		} else {
			return $found;
		}
	}

	//Update info about a user
	function sql_set_info($id, $db, $info)  {
		if(!is_numeric($id) || !is_numeric($info["points"]) || !is_numeric($info["donation_total"]) || !is_numeric($info["donation_credited"])) {
			die("sql_set_info invalid input");
		}
		$db->query("INSERT INTO users (id64, points, donation_total, donation_credited)
					VALUES (".$db->quote($id).", ".$db->quote($info["points"]).", ".$db->quote($info["donation_total"]).", ".$db->quote($info["donation_credited"]).")
					ON DUPLICATE KEY UPDATE points=VALUES(points), donation_total=VALUES(donation_total), donation_credited=VALUES(donation_credited)");
	}

	//Total amount of reward for total amount of donation
	function calculate_donation($amount) {
		return floor($amount/100)*POINTS_PER_DOLLAR;
	}

	//How many points will a user recieve if they donate a certain amount, taking past donations into account
	//This will also retroactively credit someone if POINTS_PER_DOLLAR is changed.
	function calculate_donation_increment($id,$db,$amount) {
		$info = sql_get_info($id,$db);
		return max(0,calculate_donation($amount+$info["donation_total"])-$info["donation_credited"]);
	}

?>