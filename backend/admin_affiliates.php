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

if( isset( $_REQUEST["action"] ) && $_REQUEST["action"] != '' )
	$action = $_REQUEST["action"];
?>

<p class="location">Enthusiast > Affiliates</p>

<?php

/******************************************************************************
 Add
******************************************************************************/
if( isset( $action ) && $action == 'add' ) {
	$show_add_form = true;
	$show_default = false;

	if( isset( $_POST["done"] ) ) {

		// check if required fields are present
		if( $_POST["url"] == '' || $_POST["title"] == '' ) {

			echo '<p>You must enter the URL and title ' .
				'at least.</p>';

			}

		else {
			// insert into table
			$query = 'INSERT INTO ' . $db_table . '_affiliates' .
				' VALUES( null, "' .
				$_POST["url"] . '", "' .
				$_POST["title"] . '", ';
			$query .= 'null, null, null )';

			$db_link = mysql_connect( $db_server, $db_user,
				$db_password )
				or die( 'Cannot connect to the ' .
					'database. Try again.' );
			mysql_select_db( $db_database )
				or die( 'Cannot connect to the ' .
					'database. Try again.' );
			$result = mysql_query( $query )
				or die( 'Error executing query: ' .
					mysql_error() );
			if( $result && mysql_affected_rows() > 0 ) {
				$insert_id = mysql_insert_id();
				echo '<p>Affiliate ' . $_POST["title"] .
					' added successfully as ' .
					'Affiliate #' . $insert_id .
					'.</p>';
				$show_add_form = false;
				$show_default = true;
				}
			else {
				echo '<p>Error adding affiliate. ' .
					'Please try again.</p>';
				}

			// check if there is a file to upload
			if( $_FILES["image"]["name"] != '' ) {			

				// check width and height values
				if( $_POST["width"] == '' )
					$_POST["width"] = $default_width;
				if( $_POST["height"] == '' )
					$_POST["height"] = $default_height;

				// filename
				$filename = 'affiliates/' .
					$insert_id .
					'_' . $_FILES["image"]["name"];

				$upload_success = move_uploaded_file(
					$_FILES["image"]["tmp_name"],
					'../' . $filename );

				if( $upload_success ) {
					$query = 'UPDATE ' . $db_table .
						'_affiliates SET ' .
						'imagepath = "' .
						$filename . '", width = ' .
						$_POST["width"] .
						', height = ' .
						$_POST["height"] . ' WHERE ' .
						'affiliateid = ' . $insert_id;
					$res = mysql_query( $query );
					if( !$res )
						echo mysql_error();
					}
				else {
					echo '<p>Error uploading image.</p>';
					}

				}

			} // end of if fields are present

		} // end of if post->done

	if( $show_add_form ) {
?>
		<p>You can add affiliates via this page. Fill out the
		form below and click "Add". Red fields are required.</p>

		<form method="post" action="<?= $_SERVER["PHP_SELF"] ?>"
			enctype="multipart/form-data">
		<input type="hidden" name="action" value="add" />
		<input type="hidden" name="done" />

		<p><table>

		<tr class="important"><td>
		Website URL
		</td><td>
		<input type="text" name="url" />
		</td></tr>

		<tr class="important"><td>
		Title of the site
		</td><td>
		<input type="text" name="title" />
		</td></tr>

		<tr><td>
		Image (will be uploaded)
		</td><td>
		<input type="file" name="image" />
		</td></tr>

		<tr><td>
		Image dimensions (width x height)
		</td><td>
		<input type="text" name="width" size="3" />px x
		<input type="text" name="height" size="3" />px
		</td></tr>

		<tr><td colspan="2">
		<input type="submit" value="Add the Affiliate" />
		<input type="reset" value="Clear form" />
		</td></tr>

		</table></p>

		</form>

<?php
		}
	}


/******************************************************************************
 Edit
******************************************************************************/
elseif( isset( $action ) && $action == 'edit' ) {
	$show_edit_form = true;
	$show_default = false;

	if( isset( $_POST["done"] ) &&
		isset( $_POST["title"] ) &&
		isset( $_POST["url"] ) &&
		$_POST["title"] != '' &&
		$_POST["url"] != '' ) {

		$db_link = mysql_connect( $db_server, $db_user, $db_password )
			or die( 'Cannot connect to the database. Try again.' );
		mysql_select_db( $db_database )
			or die( 'Cannot connect to the database. Try again.' );

		$upload_success = true;
		// check if image will be changed
		if( $_POST["image_change"] == 1 ) {
			// delete old image
			$query = 'SELECT imagepath FROM ' . $db_table .
				'_affiliates' . ' WHERE affiliateid = ' .
				$_POST["affiliateid"];
			$result = mysql_query( $query );
			$row = mysql_fetch_array( $result, MYSQL_ASSOC );
			if( $row["imagepath"] )
				unlink( '../' . $row["imagepath"] );

			// upload new image
			$filename = 'affiliates/' .
				$_POST["affiliateid"] . '_' .
				$_FILES["image"]["name"];

			$upload_success = move_uploaded_file(
				$_FILES["image"]["tmp_name"],
				'../' . $filename );

			if( $upload_success ) {
				echo '<p>Image successfully ' .
					'uploaded.</p>';
				}
			else {
				echo '<p>Error uploading new image. ' .
					'Database editing ' .
					'cannot continue. Images set to ' .
					'NULL.</p>';
				}

			}
		elseif( $_POST["image_change"] == 2 ) {
			// delete old image
			$query = 'SELECT imagepath FROM ' . $db_table .
				'_affiliates' . ' WHERE affiliateid = ' .
				$_POST["affiliateid"];
			$result = mysql_query( $query );
			$row = mysql_fetch_array( $result, MYSQL_ASSOC );
			if( $row["imagepath"] )
				unlink( '../' . $row["imagepath"] );
			}

		if( !$upload_success )
			$query = 'UPDATE ' . $db_table . '_affiliates SET ' .
				'imagepath = null, width = null, ' .
				'height = null WHERE affiliateid = ' .
				$_POST["affiliateid"];
		else {
			if( $_POST["width"] == '' )
				$_POST["width"] = 0;
			if( $_POST["height"] == '' )
				$_POST["height"] = 0;

			$query = 'UPDATE ' . $db_table . '_affiliates SET ' .
				'title = "' . $_POST["title"] . '", ' .
				'url = "' . $_POST["url"] . '", ';

			if( $_POST["image_change"] == 1 ) {
				$query .= 'imagepath = "' . $filename . '", ' .
					'width = ' . $_POST["width"] . ', ' .
					'height = ' . $_POST["height"];
				}
			elseif( $_POST["image_change"] == 2 ) {
				$query .= 'imagepath = null, ' .
					'width = null, ' .
					'height = null';
				}
			else {
				$query .= 'width = ' . $_POST["width"] . ', ' .
					'height = ' . $_POST["height"];
				}
			$query .= ' WHERE affiliateid = ' .
				$_POST["affiliateid"];

			}

		$result = mysql_query( $query )
			or die( 'Error executing query: ' . mysql_error() );

		if( $result && mysql_affected_rows() > 0 ) {
			echo '<p>The affiliate has been edited ' .
				'successfully.</p>';
			$show_edit_form = false;
			$show_default = true;
			}
		else
			echo '<p>Error editing the affiliate.</p>';

		}
	elseif( isset( $_POST["done"] ) &&
		isset( $_POST["subject"] ) &&
		isset( $_POST["url"] ) &&
		$_POST["subject"] == '' &&
		$_POST["url"] == '' ) {

		echo '<p>You are not allowed to delete the subject ' .
			'and URL fields.</p>';

		}

	if( $show_edit_form ) {
?>
		<p>You can edit existing affiliates via this page.
		The information of the affiliate you have selected for
		editing is shown below. Change the fields you wish to
		change.</p>

		<form method="post" action="<?= $_SERVER["PHP_SELF"] ?>"
			enctype="multipart/form-data">
		<input type="hidden" name="action" value="edit" />
		<input type="hidden" name="done" />
		<input type="hidden" name="affiliateid" value="<?=
			$_REQUEST["affiliateid"] ?>" />

<?php
		// get info
		require_once( 'get_affiliates.php' );
		$info = get_affiliates( $_REQUEST["affiliateid"] );
?>
		<p><table>

		<tr><td>
		Affiliate Title
		</td><td>
		<input type="text" name="title" value="<?=
			$info[0]["title"] ?>" />
		</td></tr>

		<tr><td>
		Website URL
		</td><td>
		<input type="text" name="url" value="<?= $info[0]["url"] ?>" />
		</td></tr>

		<tr><td rowspan="3">
		Image
		</td><td>
		<img src="../<?= $info[0]["imagepath"] ?>" width="<?=
			$info[0]["width"] ?>" height="<?= $info[0]["height"]
			?>" />
		</td></tr><tr><td>
		<input type="radio" name="image_change" value="0"
			checked="checked" /> Leave as is<br />
		<input type="radio" name="image_change" value="2" />
			Delete<br />
		<input type="radio" name="image_change" value="1" />
			Change with:<br />
		<input type="file" name="image">
		</td></tr><tr><td>
		<input type="text" name="width" size="3" value="<?=
			$info[0]["width"] ?>" />pixels x
		<input type="text" name="height" size="3" value="<?=
			$info[0]["height"] ?>" />pixels
		</td></tr>

		<tr><td colspan="2">
		<input type="submit" value="Edit this affiliate" />
		<input type="reset" value="Reset values" />
		</td></tr>

		</table></p>
<?php
		}
	}


/******************************************************************************
 Delete
******************************************************************************/
elseif( isset( $action ) && $action == 'delete' ) {

	if( isset( $_GET["affiliateid"] ) && $_GET["affiliateid"] != 0 ) {
		$query = 'SELECT imagepath FROM ' . $db_table . '_affiliates' .
			' WHERE ' . 'affiliateid = ' . $_GET["affiliateid"];
		$db_link = mysql_connect( $db_server, $db_user, $db_password )
			or die( 'Cannot connect to the database. Try again.' );
		mysql_select_db( $db_database )
			or die( 'Cannot connect to the database. Try again.' );
		$result = mysql_query( $query )
			or die( 'Error executing query: ' . mysql_error() );
		$row = mysql_fetch_array( $result, MYSQL_ASSOC );
		$imagepath = $row["imagepath"];

		$query = 'DELETE FROM ' . $db_table . '_affiliates' .
			' WHERE affiliateid = ' . $_GET["affiliateid"];
		$result = mysql_query( $query )
			or die( 'Error executing query: ' . mysql_error() );
		if( $result && mysql_affected_rows() > 0 )
			echo '<p>Fanlisting #' . $_GET["affiliateid"] .
				' deleted successfully.</p>';
		else
			echo '<p>Error deleting affiliate. Please try ' .
				'again.</p>';
		mysql_close( $db_link );
		if( $imagepath != '' )
			unlink( '../' . $imagepath );

		}
	elseif( isset( $_GET["catid"] ) &&
		$_GET["catid"] == 0 ) {
		echo '<p>You must select a category to delete.</p>';
		}

	}


/*****************************************************************************
 Default view
******************************************************************************/
if( $show_default ) {
?>

	<p>
	You can manage your affiliates using this page. Fanlistings are
	shown below,<br />
	and you can also do a search for fanlistings in the search criteria
	form below.
	</p>

	<p>
	<a href="?action=add"><img src="action_new.gif"></a>
	</p>


<?php
	require_once( 'get_affiliates.php' );
	$affiliate_array = get_affiliates();
	$affiliate_num = count( $affiliate_array );

	// set multiple page browsing
	if( !( isset( $_GET["page"] ) ) || $_GET["page"] == '' ) {
		$browse_page = 0;
		}
	else {
		$browse_page = $_GET["page"];
		}
	$array_position = $browse_page * 10;

	// determine where to start showing
	$start = $array_position;
	$end = $array_position + 10;

	echo '<p><table width="95%"><tr>';
	echo '<td><b>Affiliate ID</b></td>';
	echo '<td><b>Title/URL</b></td>';
	echo '<td><b>Image</b></td>';
	echo '<td><b>Action</b></td>';
	echo '</tr>';

	// loop showing entries
	while( $start < $affiliate_num && $start < $end ) {

		echo '<tr>';

		echo '<td>' . $affiliate_array[$start]["affiliateid"] .
			'</td>';
		echo '<td><a href="' . $affiliate_array[$start]["url"] . '">' .
			$affiliate_array[$start]["title"] .
			'</a></td>';

		echo '<td><img src="../' .
			$affiliate_array[$start]["imagepath"] .
			'" width="' .
			$affiliate_array[$start]["width"] .
			'" height="' .
			$affiliate_array[$start]["height"] .
			'"></td>';

		echo '<td><a href="' .
			$_SERVER["PHP_SELF"] . '?action=edit' .
			'&affiliateid=' .
			$affiliate_array[$start]["affiliateid"] .
			'"><img src="action_edit.gif"></a>';

		echo '<a href="' .
			$_SERVER["PHP_SELF"] .
			'?action=delete' . '&affiliateid=' .
			$affiliate_array[$start]["affiliateid"] .
			'" onclick="go=confirm(\'Are you sure you want to' .
			' delete ' . $affiliate_array[$start]["title"] .
			'?\'); ' .
			'return go;"><img src="action_delete.gif"></a></td>';

		echo '</tr>';

		$start++;

		}
	echo '</table></p>';

	if( $affiliate_num > 10 ) {
		$show_page_number = $affiliate_num / 10;
		$j = 0;

		$url = $_SERVER["PHP_SELF"] .
			'?catid=' . $catid .
			'&url=' . $url .
			'&subject=' . $subject .
			'&imagepath=' . $imagepath .
			'&width=' . $width .
			'&height=' . $height .
			'&status=' . $status .
			'&added_day=' . $added_day .
			'&added_month=' . $added_month .
			'&added_year=' . $added_year;

		echo '<p>Go to page: ';
		while( $j < $show_page_number ) {
			echo '<a href="' . $url . '&page=' . $j . '">' . $j .
				'</a> ';
			$j++;
			}
		echo '</p>';
		}

	}
require_once( 'footer.inc.php' );
?>