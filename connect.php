<?php

/***********************************************
SQLgrey Web Interface
Filename:	connect.php
Purpose: 	Renders the email/domains pages
Version: 	1.1.6
************************************************/

	require "includes/functions.inc.php";
	require "includes/connect.inc.php";
	
	(isset($_GET["action"])) ? $action = $_GET["action"] : $action = "";
	
	// For sort order.
	(isset($_GET["csort"])) ? $csort = $_GET["csort"] : $csort = "";
	(isset($_GET["sort"])) ? $sort = $_GET["sort"] : $sort = "";
	if ($sort==null || $sort=="") {
		$sort = "sender_name";
	}
	$dir = "asc";
	$ndir = "desc";
	if ($sort == $csort && $_GET["order"] == "desc") {
		$dir = "desc";
		$ndir = "asc";
	}
	
	//  Perform demanded action.
	$clearit = '<br /><br /><a class="navlike" href="connect.php">Clear this report</a>';
	$report2 = "";
	switch ($action) {
    		case "act":
    			(isset($_POST["acttype"])) ? $acttype = $_POST["acttype"] : $acttype = "";
	    		(isset($_POST["chk"])) ? $chk = $_POST["chk"] : $chk = "";
	    		switch ($acttype) {
	            		case "dodelete":
			        // For batch deleting.
					if ($chk == '') {
						$report = '<br />Nothing was selected - nothing has been deleted.'.$clearit;
					} else {
						foreach ($chk as $args) {
							$parts = explode("@@", $args);
							forget_entry($parts[0], $parts[1], $parts[2], $parts[3]);
						}
						$report = $deleted.$clearit;
					}
					break;
				case "domove":
			    	// For batch moving to whitelist.
			        	if ($chk == '') {
						$report = '<br />Nothing was selected - nothing has been moved.'.$clearit;
					} else {
						foreach ($chk as $args) {
							$parts = explode("@@", $args);
							move_entry($parts[0], $parts[1], $parts[2], $parts[3]);
						}
						$report = $moved.$clearit;
					}
					break;
				case "":
					$report = '<br />Please select Forget... or Move...';
					break;
			}
	        	break;
    		case "del_old":
    			$year = $_POST["year"];
    			$month = $_POST["month"];
    			$day = $_POST["day"];
    			$hour = $_POST["hour"];
    			$minute = $_POST["minute"];
    			$seconds = $_POST["seconds"];
    			$err = 0;
			
			if ($year < 2000 || $year > 9999) $err = 1;
			else if ($month < 1 || $month > 12) $err = 1;
			else if ($day < 1 || $day > 31) $err = 1;
			else if ($hour < 0 || $hour > 23) $err = 1;
			else if ($minute < 0 || $minute > 59) $err = 1;
			else if ($seconds < 0 || $seconds > 60) $err = 1; # indeed, 60
			
	        	del_older_than($year, $month, $day, $hour, $minute, $seconds, $err);
			$report2 = $message.$warning; 
			$report = "";
	        	break;
	        case "":
	        	$report = "";
	        	break;
	}
	
	// For the header.
	$query = "SELECT COUNT(*) AS count FROM connect";
	$result = do_query($query);
	$n = fetch_row($result);
	
	/* mysql> describe connect;
	  +---------------+---------------+------+-----+---------+-------+
	  | Field         | Type          | Null | Key | Default | Extra |
	  +---------------+---------------+------+-----+---------+-------+
	  | sender_name   | varchar(64)   |      |     |         |       |
	  | sender_domain | varchar(255)  |      |     |         |       |
	  | src           | varchar(39)   |      | MUL |         |       |
	  | rcpt          | varchar(255)  |      |     |         |       |
	  | first_seen    | timestamp(14) | YES  | MUL | NULL    |       |
	  +---------------+---------------+------+-----+---------+-------+
	*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>Greylisted hosts/domains</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="main.css" type="text/css" charset="utf-8" />
</head>

<body>

<div id="page">
	<div class="navcontainer">
		<?php shownav('grey','','con','con'); ?>
	</div>
	
	<table width="100%" border="0" summary="header">
            <tr>
		<td>
		    <h1>Greylisted hosts/domains (<?php echo $n["count"] . ")" ?></h1>
		</td>
		<td align="right">
		    <a class="navlike" href="#end" title="End of List">EoL</a>
		</td>
	    </tr>
	</table>
	
	<table border="0" summary="sortbar"><?php echo ('
	    <tr>
		<td width="20">&nbsp;</td>
		<td width="210"><b><a href="connect.php?sort=sender_name&amp;csort='.$sort.'&amp;order='.$ndir.'">Sender name</a></b></td>
		<td width="190"><b><a href="connect.php?sort=sender_domain&amp;csort='.$sort.'&amp;order='.$ndir.'">Sender domain</a></b></td>
		<td width="100"><b><a href="connect.php?sort=src&amp;csort='.$sort.'&amp;order='.$ndir.'">IP address</a></b></td>
		<td width="260"><b><a href="connect.php?sort=rcpt&amp;csort='.$sort.'&amp;order='.$ndir.'">Recipient</a></b></td>
		<td width="120"><b><a href="connect.php?sort=first_seen&amp;csort='.$sort.'&amp;order='.$ndir.'">Seen at</a></b></td>
	    </tr>
	')?></table>
	
	<form method="post" action="connect.php?action=act">
		<div id="table_con">
			<table border="0" summary="data">
			    <tr><td><a name="top"></a></td></tr>
			    <?php
				if ($sort == "sender_name")
				  $order = "sender_name ".$dir.", sender_domain ".$dir;
				else if ($sort == "sender_domain")
				  $order = "sender_domain ".$dir.", sender_name ".$dir;
				else
				  $order = $sort." ".$dir;
				$query = "SELECT sender_name, sender_domain, src, rcpt, first_seen FROM connect ORDER BY ".$order;
				$result = do_query($query);
				while($line = fetch_row($result)) {
					$sn = $line["sender_name"];
					$sd = $line["sender_domain"];
					$src = $line["src"];
					$sr = $line["rcpt"];
					$fs = $line["first_seen"];
					echo ('
				<tr>
					<td width="20"><input type="checkbox" name="chk[]" value="'.$sn.'@@'.$sd.'@@'.$src.'@@'.$sr.'" /></td>
				    	<td width="210"><span title="'.$sn.'">'.shorten_it($sn, 30).'</span></td>
			    		<td width="190"><span title="'.$sd.'">'.shorten_it($sd, 30).'</span></td>
			    		<td width="100">'.$src.'&nbsp;</td>
					<td width="260"><span title="'.$sr.'">'.shorten_it($sr, 40).'</span></td>
					<td width="120">'.strip_millisecs($fs).'</td>
				</tr>
					');
				}
			    ?>
			    <tr><td><a name="end"></a></td></tr>
			</table>
		</div>
		
		<br />
		
		<table width="100%" summary="options">
	            <tr>
			<td>
			    <input type="radio" name="acttype" value="dodelete" /> Forget (delete) selected entries<br />
			    <input type="radio" name="acttype" value="domove" /> Move selected entries to whitelist
			</td>
			<td align="right">
			    <a class="navlike" href="#top" title="Top of List">ToL</a>
			</td>
		    </tr>
		    <tr>
		        <td colspan="2"><input class="btn" type="submit" value="Submit" /></td>
		    </tr>
		</table>
	</form>
	
	<?php if (! $report == '' ) echo '<span class="alert">'.$report.'</span>'; ?>

	<div id="form">
	    <h2>Delete older than...</h2>
	    <form method="post" action="connect.php?action=del_old&amp;sort=first_seen&amp;csort=first_seen&amp;order=asc">
		<table summary="date">
	    	    <tr>
			<td>y</td><td>m</td><td>d</td><td>h</td><td>m</td><td>s</td>
		    </tr>
	    	    <tr class="datefld">
			<td><input type="text" value="0" name="year" />-</td>
			<td><input type="text" value="0" name="month" />-</td>
			<td><input type="text" value="0" name="day" /> </td>
			<td><input type="text" value="0" name="hour" />:</td>
			<td><input type="text" value="0" name="minute" />:</td>
			<td><input type="text" value="0" name="seconds" /></td>
			<td><input class="btn" type="submit" value="Delete" /></td>
		    </tr>
		</table>
	    </form>
	</div>
	
	<?php if (! $report2 == '' ) echo '<span class="alert">'.$report2.'</span>'; ?>
	
	<div id="footer">
	<?php require "includes/copyright.inc.php" ?>
	</div>

</div>

</body>

</html>
