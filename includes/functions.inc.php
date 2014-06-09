<?php

/***********************************************************
SQLgrey Web Interface
Filename:	awl.inc.php
Purpose: 	Database and navigation and other functions
Version: 	1.1.6-lcl
************************************************************/

require "config.inc.php";

// Globally used phrases.
if (isSet($_GET["locale"]))
	$locale = $_GET["locale"];
putenv("LC_ALL=$locale");
setlocale(LC_ALL, $locale);
bindtextdomain("messages", "./locale");

$dom_out = _('domains of recipients for whom messages are never greylisted');
$email_out = _('e-mail addresses of recipients for whom messages are never greylisted');
$dom_in = _('domains of recipients for whom messages are always greylisted unless they are in the optout domain table');
$email_in = _('e-mail addresses of recipients for whom messages are always greylisted unless they are in the optout e-mail table');

// Database functions.

function do_query($query) {
        global $db_hostname, $db_user, $db_pass, $db_db, $db_type;
        /* Connecting, selecting database */
	if ($db_type == "mysql") {
		$link = mysql_connect($db_hostname, $db_user, $db_pass) or die(_("Could not connect to database"));
		mysql_select_db($db_db) or die(_("Could not select database"));

		$result = mysql_query($query) or die(_("Query failed"));

		/* Closing connection */
		mysql_close($link);
	} else {
		$link = pg_connect("host=$db_hostname dbname=$db_db user=$db_user password=$db_pass") or die(_("Could not connect to database"));

	        $result = pg_query($link, $query) or die(_("Query failed"));

		/* Closing connection */
		pg_close($link);
	}
        return $result;
}

function fetch_row($result) {
	global $db_type;
	if ($db_type == "mysql") {
		return mysql_fetch_array($result, MYSQL_ASSOC);
	} else {
		return pg_fetch_assoc($result);
	}
}


// Navigation functions.

function shownav($colour, $mode, $direction, $what) {
	// Menubar setup for all pages
	global $dom_out, $email_out, $dom_in, $email_in;
	if ($colour == 'white') {
		// only awl.php
		echo ('
			<ul id="navlist">
			  <li><a href="index.php">Main menu</a></li>
			  <li><a href="connect.php" title="'._("hosts/domains that are currently greylisted") . '>'._("Waiting (greylist)").'</a></li>
			  <li><a href="awl.php?mode=email"'.is_active1("email", $mode).
				'title="'._("auto-whitelisted e-mailadresses (that have passed greylisting)").'">'._("E-mail addresses").'</a></li>
			  <li><a href="awl.php?mode=domains"'.is_active1('domains', $mode).
				'title="'._("auto-whitelisted domains (that have passed greylisting)").'">Domains</a></li>
			  <li><a href="opt_in_out.php?direction=out&amp;what=domain" title="&nbsp;'.$dom_out.'">'._("Optout domain").'</a></li>
			  <li><a href="opt_in_out.php?direction=out&amp;what=email" title="&nbsp;'.$email_out.'">'._("Optout e-mail").'</a></li>
			  <li><a href="opt_in_out.php?direction=in&amp;what=domain" title="&nbsp;'.$dom_in.'">'._("Optin domain").'</a></li>
			  <li><a href="opt_in_out.php?direction=in&amp;what=email" title="&nbsp;'.$email_in .'">'._("Optin e-mail").'</a></li>
			</ul>
		');
	} else {
		// index and connect (with dummies) and opt_in_out.
		echo ('
			<ul id="navlist">
			  <li><a href="index.php"'.is_active2("ind", $direction, "ind", $what).'>'._("Main menu").'</a></li>
			  <li><a href="connect.php"'.is_active2("con", $direction, "con", $what).
				'title="'._("hosts/domains that are currently greylisted").'">'._("Waiting (greylist)").'</a></li>
			  <li><a href="awl.php?mode=email" title="'._("auto-whitelisted e-mailadresses (that have passed greylisting)").'">'._("E-mail addresses").'</a></li>
			  <li><a href="awl.php?mode=domains" title="'._("auto-whitelisted domains (that have passed greylisting)").'">'._("Domains").'</a></li>
			  <li><a href="opt_in_out.php?direction=out&amp;what=domain"'.is_active2("out", $direction, "domain", $what).' title="'.$dom_out.'">'._("Optout domain").'</a></li>
			  <li><a href="opt_in_out.php?direction=out&amp;what=email"'.is_active2("out", $direction, "email", $what).' title="'.$email_out.'">'._("Optout e-mail").'</a></li>
			  <li><a href="opt_in_out.php?direction=in&amp;what=domain"'.is_active2('in',$direction,'domain',$what).' title="'.$dom_in.'">'._("Optin domain").'</a></li>
			  <li><a href="opt_in_out.php?direction=in&amp;what=email"'.is_active2('in',$direction,'email',$what).' title="'.$email_in.'">'._("Optin e-mail").'</a></li>
			</ul>
		');
	}
}

function is_active1($mode, $get) {
	// For awl menubar items - sets item active.
	if ($mode == $get) {
		return ' id="current" ';
	} else {
		return ' ';
	}
}

function is_active2($direction, $getdir, $what, $getwhat) {
	// For index, connect and opt_in_out menubar items - sets item active.
	if (($direction == $getdir) && ($what == $getwhat)) {
		return ' id="current" ';
	} else {
		return ' ';
	}
}


// Other functions.

function shorten_it($sendername, $nr) {
	//  For managing the width of the Sender name, Sender domain and Recipient columns.
	if (strlen($sendername) > $nr) {
		$sendername = substr($sendername, 0, $nr ).'<b>...</b>';
	}
	return $sendername;
}

function strip_millisecs($ts) {
	// Formats timestamp without milliseconds.
	global $no_millisecs;
	if ($no_millisecs == "yes") {
		$ts = date_create($ts);
		$ts = date_format($ts, 'Y-m-d H:i:s');
	}
	return $ts;
}

?>
