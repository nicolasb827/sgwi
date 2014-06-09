<?php

/**************************************
SQLgrey Web Interface
Filename:	opt_in_out.inc.php
Purpose: 	Opt in/out functions
Version: 	1.1.6
***************************************/

	if ($_GET["direction"] == "out") {
		$title = "Opt-out";
		$helptag_dir = "<br />"._("(recipients for whom messages are never greylisted)");
		$table = "optout_";
	} else {
		$title = "Opt-in";
		$helptag_dir = "<br />"._("(recipients for whom messages are always greylisted unless they are in the ");
		$table = "optin_";
	}
	
	if ($_GET["what"] == "domain") {
		$title .= " "._("domains");
		$helptag_what = _("optout domain table)");
		$table .= "domain";
		$field = "domain";
	} else {
		$title .= " "._("e-mail addresses");
		$helptag_what = _("optout e-mail table)");
		$table .= "email";
		$field = "email";
	}
?>