<?php
/*****************************************************************************
 Enthusiast Lite: Fanlisting Management System
 Copyright (c) by Angela Sabas
 http://scripts.indisguise.org

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.

 For more information please view the readme.txt file.
******************************************************************************/
session_start();
require_once( 'config.inc.php' );
if( !isset( $_COOKIE["login_password"] ) ||
	$_COOKIE['login_password'] != md5( $set_password . 'ENTH2' ) ) {
	$_SESSION["login_message"] = 'Please log in first before viewing ' .
		'anything.';
	header( 'location: index.php' );
	die( 'Redirecting you...' );
	}
require_once( 'header.inc.php' );

$show_default = true;

// approve function
function approve( $mem_email ) {
	require 'config.inc.php';

	// query string
	$query = 'UPDATE ' . $db_table . ' SET pending = 0, ' .
		'added = CURDATE() WHERE email = "' . $mem_email . '"';

	// connect to the database using config file
	$db_link = mysql_connect( $db_server, $db_user, $db_password )
		or die( 'Cannot connect to the MySQL server: ' .
			mysql_error() );
	mysql_select_db( $db_database )
		or die( 'Cannot select database: ' . mysql_error() );

	// execute query
	mysql_query( $query )
		or die( 'Cannot execute query: ' . mysql_error() .
		'.<br /><br /> The SQL query is: <i>' . $query .
		'</i>.' );

	if( mysql_affected_rows() > 0 ) {
		// success, send email

		$to = $mem_email;
		$subject = $fanlisting_title .
			': You have been added!';
		$headers = 'From: ' . $fanlisting_title . ' <' .
			$fanlisting_email . '>';

		$approve_file = 'emails/approval.txt';
		$approve_email = fopen( $approve_file, 'r' );
		$message = fread( $approve_email, filesize(
			$approve_file ) );
		fclose( $approve_email );

		require_once( 'parse_email.php' );
		$message = parse_email( $message, $mem_email );

		mail( $to, $subject, $message, $headers );

		echo '<p>You have successfully approved the fan with' .
			' email address <i>' . $mem_email .
			'</i> and an email has been sent to ' .
			'him/her.</p>';

		}
	else {
		echo '<p>Error approving member.</p>';
		}

	mysql_close( $db_link );

	}
?>

<p class="location">Enthusiast > Pending</p>

<?php
/******************************************************************************
 Edit member information
******************************************************************************/
if( isset( $_GET["action"] ) && $_GET["action"] == 'edit' ) {

	// show edit form, hide default page
	$show_edit_form = true;
	$show_default = false;

	if( isset( $_GET["done"] ) ) {

		// create query to update the information
		$query = 'UPDATE ' . $db_table . ' SET email = "' .
			$_GET["edit_email"] . '", name = "' .
			$_GET["edit_name"] . '", ';

		// if country is enabled, include country
		if( !isset( $disable_country ) || !$disable_country )
			$query .= ' country = "' . $_GET["edit_country"] .
				'", ';

		// if there is no url, make it null
		if( $_GET["edit_url"] == '' )
			$query .= 'url = null, ';
		else
			$query .= 'url = "' . $_GET["edit_url"] . '", ';

		// add aditional fields, if any, to query string
		if( $additional_field ) {

			// for each field name, add it to the query string
			foreach( $additional_field_names as $field ) {

				$query .= $db_table . '.' . $field . ' = "' .
					$_GET["edit_$field"] .'", ';

				}

			}

		// finish creating string
		$query .= 'showurl = ' . $_GET["edit_showurl"] . ' WHERE ' .
			'email = "' . $_GET["email"] . '"';

		// connect to database
		$db_link = mysql_connect( $db_server, $db_user, $db_password )
			or die( 'Cannot connect to the MySQL server: ' .
				mysql_error() );
		mysql_select_db( $db_database )
			or die( 'Cannot select database: ' . mysql_error() );

		// run query
		mysql_query( $query )
			or die( 'Cannot execute query: ' . mysql_error() );

		// if no rows were affected, there's something wrong
		if( mysql_affected_rows() <= 0 )
			echo '<p class="important">Error editing member.' .
				' Please try again.</p>';
		else {

			// editing was successful
			echo '<p>You have successfully edited the ' .
				'information of the member ' .
				'with the email address of ' .
				$_GET["email"] . '.</p>';

			// don't show edit form, but show default page
			$show_edit_form = false;
			$show_default = true;

			}

		// close database link
		mysql_close( $db_link );

		}

	// create edit form
	if( $show_edit_form ) {

		// get member information
		require_once( 'get_members.php' );
		$num = 0;
		$memberinfo = get_members( $_GET["email"], '', '', '', '',
			2, '', '', '', $num );
?>
		<p>
		You can edit the member's information using the
		form below. Change only the fields you wish to change.
		</p>

		<form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" name="done" />
		<input type="hidden" name="email" value="<?=
			$_GET["email"] ?>" />

		<p><table>

		<tr><td>
		Email address
		</td><td>
		<input type="text" name="edit_email" value="<?=
			$memberinfo[0]["email"] ?>" />
		</td></tr>

		<tr><td>
		Name
		</td><td>
		<input type="text" name="edit_name" value="<?=
			$memberinfo[0]["name"] ?>" />
		</td></tr>

<?php
		// if country is enabled, show this form element
		if( !isset( $disable_country ) || !$disable_country ) {
?>
			<tr><td>
			Country
			</td><td>
			<select name="edit_country" />
			<option><?= $memberinfo[0]["country"] ?></option>
			<option value=""></option>
			<option value="">-----</option>
<?php
			include 'countries.inc.php';
?>
			</select>
			</td></tr>
<?php
			}
?>

		<tr><td>
		Website URL
		</td><td>
		<input type="text" name="edit_url" value="<?=
			$memberinfo[0]["url"] ?>" />
		</td></tr>

<?php
		// if there are additional fields, show these form elements
		if( $additional_field ) {

			// for each additional field, show a form element
			foreach( $additional_field_names as $field ) {
?>
				<tr><td>
				<?= ucfirst( $field ) ?>
				</td><td>
				<input type="text" name="edit_<?= $field ?>"
				value="<?= $memberinfo[0]["$field"] ?>" />
				</td></tr>
<?php

				}

			}
?>

		<tr><td colspan="2" class="leftalign">
<?php
		// if showurl is set to yes, make "show" the selected value
		if( $memberinfo[0]["showurl"] == 1 ) {
?>
			<input type="radio" name="edit_showurl" value="1"
				checked="checked" /> Show Website URL<br />
			<input type="radio" name="edit_showurl" value="0" />
				Hide Website URL
<?php
			}
		else { // else, make "hide" the selected value
?>
			<input type="radio" name="edit_showurl" value="1" />
				Show Website URL<br />
			<input type="radio" name="edit_showurl" value="0"
				checked="checked"/> Hide Website URL
<?php
			}
?>
		</td></tr>

		<tr><td colspan="2">
		<input type="submit" value="Change information" />
		<input type="reset" value="Reset form values" />
		</td></tr>

		</table></p>

		</form>

<?php
		}
	}

/******************************************************************************
 Approve a member
******************************************************************************/
if( isset( $_GET["action"] ) && $_GET["action"] == 'approve' ) {

	if( $_GET["email"] ) {
		approve( $_GET["email"] );
		}
	}

/******************************************************************************
 Approve a number of members
******************************************************************************/
if( isset( $_REQUEST["action"] ) && $_REQUEST["action"] == 'multiapprove' ) {

	if( isset( $_POST["emails"] ) ) {
		foreach( $_POST["emails"] as $fan ) {
			approve( $fan );
			}
		}
	}

/******************************************************************************
 Delete a member
******************************************************************************/
if( isset( $_GET["action"] ) && $_GET["action"] == 'delete' ) {
	$query = 'DELETE FROM ' . $db_table . ' WHERE email = "' .
		$_GET["email"] . '"';
	$db_link = mysql_connect( $db_server, $db_user, $db_password )
		or die( 'Cannot connect to the MySQL server: ' .
			mysql_error() );
	mysql_select_db( $db_database )
		or die( 'Cannot select database: ' . mysql_error() );
	mysql_query( $query )
		or die( 'Cannot execute query: ' . mysql_error() .
		'.<br /><br /> The SQL query is: <i>' . $query .
		'</i>.' );

	if( mysql_affected_rows() <= 0 )
		echo '<p class="important">Error deleting member. Please' .
			' try again.</p>';
	else
		echo '<p>You have successfully deleted the member with the ' .
			'email address of ' . $_GET["email"] . '.</p>';
	mysql_close( $db_link );
	}

/******************************************************************************
 Default page view
******************************************************************************/
if( $show_default ) {
?>
	<p>
	You can approve, edit, or delete pending members from this page.
	</p>

	<p>
	If you wish to approve more than one pending member, click on the
	checkbox preceding their information, and click on "Approve
	Selected Fans".
	</p>

	<p>
	<table><tr><td>
	Total pending members:
	</td><td>
<?php
	include 'get_pending_count.php';
?>
	</td></tr>
	</table></p>

<?php
	$total_members = 0;
	require_once( 'get_members.php' );
	$members_array = get_members( '', '', '', '', '', 1, '', '', '',
		$total_members );
	$member_num = count( $members_array );

	// set multiple page browsing
	if( !( isset( $_GET["page"] ) ) || $_GET["page"] == '' ) {
		$browse_page = 0;
		}
	else {
		$browse_page = $_GET["page"];
		}
	$array_position = $browse_page * $fans_per_page;

	// determine where to start showing
	$start = $array_position;
	$end = $array_position + $fans_per_page;

	echo '<form method="post">';
	echo '<input type="hidden" name="action" value="multiapprove" />';

	echo '<p><table width="95%"><tr>';
	echo '<td>&nbsp;</td>';
	echo '<td><b>Email</b></td>';
	echo '<td><b>Name</b></td>';

	if( !isset( $disable_country ) || !$disable_country )
		echo '<td><b>Country</b></td>';
	echo '<td><b>Website URL</b></td>';
	if( $additional_field )
		echo '<td><b>Additional fields</b></td>';
	echo '<td colspan="4"><b>Action</b></td>';
	echo '</tr>';

	if( !isset( $link_target ) )
		$link_target = '_top';

	// loop showing entries
	while( $start < $member_num && $start < $end ) {

		echo '<tr>';

		echo '<td><input type="checkbox" name="emails[]" value="' .
			$members_array[$start]["email"] . '" /></td>';

		echo '<td>' . $members_array[$start]["email"] . '</td>';
		echo '<td>' . $members_array[$start]["name"] . '</td>';
		if( !isset( $disable_country ) || !$disable_country )
			echo '<td>' . $members_array[$start]["country"] .
				'</td>';
		echo '<td><a href="' . $members_array[$start]["url"] .
			'" target="' . $link_target . '">' .
			$members_array[$start]["url"] . '</a></td>';

		if( $additional_field ) {
			echo '<td>';
			foreach( $additional_field_names as $field ) {
				echo ucfirst( $field ) . ': ' .
					$members_array[$start]["$field"] .
					'<br />';
				}
			echo '</td>';
			}

		echo '<td class="actioncell"><a href="' .
			$_SERVER["PHP_SELF"] . '?action=edit' .
			'&email=' . $members_array[$start]["email"] .
			'"><img src="action_edit.gif"></a></td>';

		echo '<td class="actioncell"><a href="' .
			$_SERVER["PHP_SELF"] .
			'?action=approve' . '&email=' .
			$members_array[$start]["email"] .
			'"><img src="action_approve.gif"></a></td>';

		echo '<td class="actioncell"><a href="' .
			$_SERVER["PHP_SELF"] .
			'?action=delete' . '&email=' .
			$members_array[$start]["email"] .
			'" onclick="go=confirm(\'Are you sure you want to' .
			' delete ' . $members_array[$start]["name"] . ' (' .
			$members_array[$start]["email"] . ')?\'); ' .
			'return go;"><img src="action_delete.gif"></a></td>';

		echo '<td class="actioncell"><a href="admin_email.php' .
			'?type=single' . '&email=' .
			$members_array[$start]["email"] .
			'"><img src="action_email.gif"></a></td>';

		echo '</tr>';

		$start++;

		}
	echo '</table></p>';

	echo '<p><input type="submit" value="Approve Selected Fans" /></p>';
	echo '</form>';

	if( $member_num > $fans_per_page ) {
		$show_page_number = $member_num / $fans_per_page;
		$j = 0;
		$show = 1;

		$url = $_SERVER["PHP_SELF"];

		echo '<p>Go to page: ';
		while( $j < $show_page_number ) {
			echo '<a href="' . $url . '?page=' . $j . '">' .
				$show . '</a> ';
			$j++;
			$show++;
			}
		echo '</p>';
		}
	}
require_once( 'footer.inc.php' );
?>