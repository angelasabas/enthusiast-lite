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

// check if person is logged in
if( !isset( $_COOKIE["login_password"] ) ||
	$_COOKIE['login_password'] != md5( $set_password . 'ENTH2' ) ) {

	// if person is not logged in, go to index page
	$_SESSION["login_message"] = 'Please log in first before viewing ' .
		'anything.';
	header( 'location: index.php' );
	die( 'Redirecting you...' );

	}

// include header and config files
require_once( 'header.inc.php' );

// show default page
$show_default = true;
?>

<p class="location">Enthusiast > Members</p>

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
 Delete member from database.
******************************************************************************/
if( isset( $_GET["action"] ) && $_GET["action"] == 'delete' ) {

	// create query
	$query = 'DELETE FROM ' . $db_table . ' WHERE email = "' .
		$_GET["email"] . '"';

	// connect to database
	$db_link = mysql_connect( $db_server, $db_user, $db_password )
		or die( 'Cannot connect to the MySQL server: ' .
			mysql_error() );
	mysql_select_db( $db_database )
		or die( 'Cannot select database: ' . mysql_error() );
	mysql_query( $query )
		or die( 'Cannot execute query: ' . mysql_error() );

	// if there are no affected rows (nothing deleted)
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
	You can manage your members using this page. Members are
	shown below,<br />
	and you can also do a search for members in the search criteria form
	below.
	</p>

	<p>
	To approve members, please go to the
	<a href="admin_pending.php">Pending</a> page.
	</p>

	<p>
	<table><tr><td>
	Total members:
	</td><td>
<?php
	include 'get_member_count.php';
?>
	</td></tr>
	</table></p>

	<form method="get" action="<?= $_SERVER["PHP_SELF"] ?>">
	<input type="hidden" name="action" value="view" />

	<p><table><tr><td colspan="2">
	What are you looking for?
	</td></tr>

	<tr><td>
	Email address
	</td><td>
	<input type="text" name="search_email" />
	</td></tr>

	<tr><td>
	Name
	</td><td>
	<input type="text" name="search_name" />
	</td></tr>

<?php
	// if country is enabled, show this form element
	if( !isset( $disable_country ) || !$disable_country ) {
?>
		<tr><td>
		Country
		</td><td>
		<select name="search_country" />
		<option value=""></option>
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
	<input type="text" name="search_url" />
	</td></tr>

<?php
	// show form elements for each additional fields
	if( $additional_field ) {
		foreach( $additional_field_names as $field ) {
?>
			<tr><td>
			<?= ucfirst( $field ) ?>
			</td><td>
			<input type="text" name="edit_<?= $field ?>" />
			</td></tr>
<?php
			}
		}
?>

	<tr><td>
	Date added
	</td><td>
	<input type="text" name="search_date_day" size="2" />
	<select name="search_date_month">
<?php
	include( 'select_month.inc.php' );
?>
	<option value="" selected="selected">No month</option>
	</select>
	<select name="search_date_year">
<?php
	include( 'select_year.inc.php' );
?>
	<option value="" selected="selected">No year</option>
	</select>
	</td></tr>

	<tr><td colspan="2">
	<input type="submit" value="Search for this record" />
	<input type="reset" value="Start criteria over" />
	</td></tr>

	</table></p>

	</form>

<?php
	// set search criteria
	$search_email = '';
	$search_name = '';
	$search_country = '';
	$search_url = '';
	$search_extra = '';
	$search_date_day = '';
	$search_date_month = '';
	$search_date_year = '';
	if( isset( $_GET["search_email"] ) )
		$search_email = $_GET["search_email"];
	if( isset( $_GET["search_name"] ) )
		$search_name = $_GET["search_name"];
	if( isset( $_GET["search_country"] ) )
		$search_country = $_GET["search_country"];
	if( isset( $_GET["search_url"] ) )
		$search_url = $_GET["search_url"];
	if( isset( $_GET["search_extra"] ) )
		$search_extra = $_GET["search_extra"];
	if( isset( $_GET["search_date_day"] ) )
		$search_date_day = $_GET["search_date_day"];
	if( isset( $_GET["search_date_month"] ) )
		$search_date_month = $_GET["search_date_month"];
	if( isset( $_GET["search_date_year"] ) )
		$search_date_year = $_GET["search_date_year"];

	$total_members = 0;
	require_once( 'get_members.php' );
	$members_array = get_members( $search_email, $search_name,
		$search_country, $search_url, $search_extra, 0, 
		$search_date_day, $search_date_month, $search_date_year,
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

	echo '<p><table width="95%"><tr>';
	echo '<td><b>Email</b></td>';
	echo '<td><b>Name</b></td>';
	if( !isset( $disable_country ) || !$disable_country )
		echo '<td><b>Country</b></td>';
	echo '<td><b>Website URL</b></td>';
	if( $additional_field )
		echo '<td><b>Additional fields</b></td>';
	echo '<td width="70"><b>Date</b></td>';
	echo '<td colspan="3"><b>Action</b></td>';
	echo '</tr>';

	if( !isset( $link_target ) )
		$link_target = '_top';

	// loop showing entries
	while( $start < $member_num && $start < $end ) {

		echo '<tr>';

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

		echo '<td>' . $members_array[$start]["added"] . '</td>';

		echo '<td class="actioncell"><a href="' .
			$_SERVER["PHP_SELF"] . '?action=edit' .
			'&email=' . $members_array[$start]["email"] .
			'"><img src="action_edit.gif"></a></td>';

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

	if( $member_num > $fans_per_page ) {
		$show_page_number = $member_num / $fans_per_page;
		$j = 0;
		$show = 1;

		$url = $_SERVER["PHP_SELF"] . '?action=view' .
			'&search_email=' . $search_email .
			'&search_name=' . $search_name .
			'&search_country=' . $search_country .
			'&search_date_day=' . $search_date_day .
			'&search_date_month=' . $search_date_month .
			'&search_date_year=' . $search_date_year;

		echo '<p>Go to page: ';
		while( $j < $show_page_number ) {
			echo '<a href="' . $url . '&page=' . $j . '">' .
				$show . '</a> ';
			$j++;
			$show++;
			}
		echo '</p>';
		}
?>
	<p><table><tr><td>
	Sum of all members falling in the search criteria:
	</td><td>
	<?= number_format( $total_members ) ?>
	</td></tr>
	</table></p>

<?php
	}
require_once( 'footer.inc.php' );
?>