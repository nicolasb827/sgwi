<?php

/********************************************
SQLgrey Web Interface
Filename:	awl.php
Purpose: 	Renders the greylist page
Version: 	1.1.6
*********************************************/

	require "includes/functions.inc.php";
	require "includes/awl.inc.php";

	(isset($_GET["mode"])) ? $mode = $_GET["mode"] : $mode = "email";
	(isset($_GET["action"])) ? $action = $_GET["action"] : $action = "";
	
	// For sort order.
	(isset($_GET["csort"])) ? $csort = $_GET["csort"] : $csort = "";
	(isset($_GET["sort"])) ? $sort = $_GET["sort"] : $sort = "";
	if ($sort==null || $sort=="") {
		if ($mode == "email") {
			$sort = "sender_name";
		} else {
			$sort = "sender_domain";
		}
	}
	$dir = "asc";
	$ndir = "desc";
	if ($sort == $csort && $_GET["order"] == "desc") {
		$dir = "desc";
		$ndir = "asc";
	}
	
	//  Perform demanded action.
	$clearit = '<br /><br /><a class="navlike" href="awl.php?mode='.$mode.'">'._("Clear this report").'</a>';
	switch ($action) {
    		case "del_selection":
	        	// For batch deleting.
			(isset($_POST["chk"])) ? $chk = $_POST["chk"] : $chk = "";
			if ($chk == "") {
				$report = '<br />'._("Nothing was selected - nothing has been deleted.").$clearit;
			} else {
				foreach ($chk as $args) {
					$parts = explode("@@", $args);
					delete_entry($mode, $parts[0], $parts[1], $parts[2]);
				}
				$report = $deleted.$clearit;
			}
			$report2 = "";
	        	break;
    		case "del_undef":
	        	delete_undef($mode);
	        	$report = "";
	        	$report2 = $message;
	        	break;
    		case "add_sender":
	    		(isset($_POST["sender_name"])) ? $sn = $_POST["sender_name"] : $sn = "";
	        	add_sender($mode, $sn, $_POST["sender_domain"], $_POST["src"]);
	        	$report = "";
			$report2 = $added;
	        	break;
	        case "":
	        	$report = "";
	        	$report2 = "";
	        	break;
	}
	
	//  Make a nice header.
	if ($mode=="email") {
		$query = "SELECT COUNT(*) AS count FROM from_awl";
		$title = _("e-mail addresses")." (";
	} else {
		$query = "SELECT COUNT(*) AS count FROM domain_awl";
		$title = _("domains")." (";
	}
	$result = do_query($query);
	$n = fetch_row($result);
	$title .= $n["count"].")";

	/*
	  mysql> describe from_awl;
	  +---------------+---------------+------+-----+----------------+-------+
	  | Field         | Type          | Null | Key | Default        | Extra |
	  +---------------+---------------+------+-----+----------------+-------+
	  | sender_name   | varchar(64)   |      | PRI |                |       |
	  | sender_domain | varchar(255)  |      | PRI |                |       |
	  | src           | varchar(39)   |      | PRI |                |       |
	  | first_seen    | timestamp(14) | YES  |     | NULL           |       |
	  | last_seen     | timestamp(14) | YES  | MUL | 00000000000000 |       |
	  +---------------+---------------+------+-----+----------------+-------+
	
	  mysql> describe domain_awl;
	  +---------------+---------------+------+-----+----------------+-------+
	  | Field         | Type          | Null | Key | Default        | Extra |
	  +---------------+---------------+------+-----+----------------+-------+
	  | sender_domain | varchar(255)  |      | PRI |                |       |
	  | src           | varchar(39)   |      | PRI |                |       |
	  | first_seen    | timestamp(14) | YES  |     | NULL           |       |
	  | last_seen     | timestamp(14) | YES  | MUL | 00000000000000 |       |
	  +---------------+---------------+------+-----+----------------+-------+
	*/	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="<?php echo $locale; ?>">

<head>
	<title><?php echo _(sprintf ("Whitelisted %s", ($mode=="email" ? "e-mail addresses" : "domains"))); ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="main.css" type="text/css" charset="utf-8" />
</head>

<body>

<div id="page">

	<div class="navcontainer">
		<?php shownav('white', $mode, '', ''); ?>
	</div>
	
	<table width="100%" summary="header">
            <tr>
		<td>
		    <h1>Whitelisted <?php echo $title; ?></h1>
		</td>
		<td align="right">
			<a class="navlike" href="#end" title="End of List">EoL</a>
		</td>
	    </tr>
	</table>
	
	<table border="0" summary="sortbar"><?php echo ('
	    <tr>
	        <td width="20">&nbsp;</td>');
		if ($mode=="email") echo ('
		<td width="300">&nbsp;<b><a href="awl.php?mode='.$mode.'&amp;sort=sender_name&amp;csort='.$sort.'&amp;order='.$ndir.'">Sender name</a></b></td>');
		echo ('
		<td width="240"><b><a href="awl.php?mode='.$mode.'&amp;sort=sender_domain&amp;csort='.$sort.'&amp;order='.$ndir.'">Sender domain</a></b></td>
		<td width="100"><b><a href="awl.php?mode='.$mode.'&amp;sort=src&amp;csort='.$sort.'&amp;order='.$ndir.'">Source</a></b></td>
		<td width="120"><b><a href="awl.php?mode='.$mode.'&amp;sort=first_seen&amp;csort='.$sort.'&amp;order='.$ndir.'">First seen</a></b></td>
		<td width="120"><b><a href="awl.php?mode='.$mode.'&amp;sort=last_seen&amp;csort='.$sort.'&amp;order='.$ndir.'">Last seen</a></b></td>
	    </tr>');
	?></table>
	
	<form method="post" action="awl.php?mode=<?php echo $mode; ?>&amp;action=del_selection">
		<div id="table_awl">
			<table border="0" summary="data">
			    <tr><td><a name="top"></a></td></tr>
			    <?php
				if ($mode=="email") {
					if ($sort == "sender_name") {
						$order = "sender_name ".$dir.", sender_domain ".$dir;
					}
					else if ($sort == "sender_domain") {
						$order = "sender_domain ".$dir.", sender_name ".$dir;
					} else {
						$order = $sort." ".$dir;
					}
					$query = "SELECT sender_name, sender_domain, src, first_seen, last_seen FROM from_awl ORDER BY ".$order;
				} else {
					$order = $sort." ".$dir;
					$query = "SELECT sender_domain, src, first_seen, last_seen FROM domain_awl ORDER BY ".$order;
				}
				$result = do_query($query);
				
				while($line = fetch_row($result)) {
					$sd = $line["sender_domain"];
					$src = $line["src"];
					$fs = $line["first_seen"];
					$ls = $line["last_seen"];
					if ($mode == "email") {
						$sn = $line["sender_name"];
						echo ('
					<tr>
						<td width="20"><input type="checkbox" name="chk[]" value="'.$sn.'@@'.$sd.'@@'.$src.'" /></td>
						<td width="300"><span title="'.$sn.'">'.shorten_it($sn, 42).'</span></td>
						');
					} else {
						$sn = "noname";
						echo ('
					<tr>
						<td width="20"><input type="checkbox" name="chk[]" value="'.$sn.'@@'.$sd.'@@'.$src.'" /></td>
						');
					}
					echo ('
						<td width="240"><span title="'.$sd.'">'.shorten_it($sd, 35).'</span></td>
						<td width="100">'.$src.'</td>
						<td width="120">'.strip_millisecs($fs).'</td>
						<td width="120">'.strip_millisecs($ls).'</td>
					</tr>
					');
				}
			    ?>
			    <tr><td><a name="end"></a></td></tr>
			</table>
		</div>
		
		<br />
		
		<table width="100%" summary="buttons">
	            <tr>
			<td>
			    <input class="btn" type="submit" value="<?php echo _("Delete selected entries"); ?>" />
			</td>
			<td align="right">
			    <a class="navlike" href="#top" title="<?php echo _("Top of List"); ?>">ToL</a>
			</td>
		    </tr>
		</table>
	</form>
	
	<?php if (! $report == '' ) echo '<span class="alert">'.$report.'</span>'; ?>

	<div id="form">
		<h2>Add to whitelist</h2>
		<form action="awl.php?mode=<?php echo $mode; ?>&amp;action=add_sender" method="post">
			<table width="100%" border="0" summary="add form">
				<?php if ($mode == "email") { ?>
				<tr>
					<td width="120"><?php echo _("Sender name"); ?>:</td>
					<td width="240"><input class="txt" type="text" name="sender_name" /></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<?php } ?>
				<tr>
					<td width="120"><?php echo _("Sender domain"); ?>:</td>
					<td width="240"><input class="txt" type="text" name="sender_domain" /></td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td width="120"><?php echo _("Source (class c or d):"); ?></td>
					<td width="240"><input class="txt" type="text" name="src" /></td>
					<td align="left"><input class="btn" type="submit" value="<?php echo _("Add"); ?>" /></td>
					<td align="right">
					<a class="navlike" href="awl.php?mode=<?php echo _($mode); ?>&amp;action=del_undef"><?php echo _("Delete '-undef-' entries"); ?></a>
					</td>
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
