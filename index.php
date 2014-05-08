<?php

/********************************************
SQLgrey Web Interface
Filename:	index.php
Purpose: 	Renders the main menu page
Version: 	1.1.6
*********************************************/

	require "includes/functions.inc.php";
	
	$query = "SELECT COUNT(*) AS count FROM connect";
	$result = do_query($query);
	$line = fetch_row($result);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
	<title>SQLGrey Webinterface</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="main.css" type="text/css" charset="utf-8" />
	<style type="text/css">
		input { width: 150px; height: 25px; font-size: 13px; }
	</style>
</head>

<body>

<div id="page">

    <div class="navcontainer">
	<?php shownav('grey','','ind','ind'); ?>
    </div>

    <table width="77%" summary="main">
	<tr>
	    <td>
		<table width="820" border="0" summary="header">
		    <tr>
			<td>
			    <h1>SQLGrey Webinterface (Main menu)</h1>
			</td>
			<?php if ($close_btn == "yes") echo ('
			<td align="right">
			    <form action="../" method="post">
				<input type="submit" value="Close" />
			    </form>
			</td>');
			?>
		    </tr>
		</table>
			
		<table border="0" summary="sub">
			<tr>
				<td colspan="3">
				Hosts / domains that are currently greylisted: [<?php echo $line["count"]; ?>]
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<form action="connect.php" method="post">
						<input style="width:150px;" type="submit" value="Waiting (greylist)" />
					</form>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>

				<td colspan="3">Auto-whitelisted (hosts / domains that have passed greylisting)</td>
			</tr>
			<tr>
				<td>
					<form action="awl.php?mode=email" method="post">
						<input type="submit" value="E-mail addresses" />
					</form>
				</td>
				<td>
					<form action="awl.php?mode=domains" method="post">
						<input type="submit" value="Domains" />
					</form>
				</td>
				<td width="40%">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3">
					<form action="opt_in_out.php?direction=out&amp;what=domain" method="post">
						<input type="submit" value="Optout domain" />
						&nbsp;(<?php echo $dom_out; ?>)
					</form>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3">
					<form action="opt_in_out.php?direction=out&amp;what=email" method="post">
						<input type="submit" value="Optout e-mail" />
						&nbsp;(<?php echo $email_out; ?>)
					</form>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3">
					<form action="opt_in_out.php?direction=in&amp;what=domain" method="post">
						<input type="submit" value="Optin domain" />
						&nbsp;(<?php echo $dom_in; ?>)
					</form>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3">
					<form action="opt_in_out.php?direction=in&amp;what=email" method="post">
						<input type="submit" value="Optin e-mail" />
						&nbsp;(<?php echo $email_in; ?>)
					</form>
				</td>
			</tr>
		</table>
		<br />
	    </td>
	</tr>
    </table>

    <div id="footer" style="width: 800px;">
	<?php require "includes/copyright.inc.php" ?>
    </div>

</div>

</body>

</html>
