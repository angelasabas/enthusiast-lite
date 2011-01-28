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
require_once( 'header.inc.php' );
require_once( 'config.inc.php' );
?>

<p class="location">Enthusiast > Alter database table</p>

<?php

if( isset( $_GET["done"] ) ) {
	$db_link = mysql_connect( $db_server, $db_user, $db_password )
		or die( 'Cannot connect to the MySQL server: ' .
			mysql_error() );
	mysql_select_db( $db_database )
		or die( 'Cannot select database: ' . mysql_error() );

	// alter table to delete "new"
	$query = 'ALTER TABLE `' . $db_table . '` DROP COLUMN `new`';

	$result = mysql_query( $query );
	if( $result )
		echo '<p>Garbage column "new" dropped successfully.</p>';
	else
		echo '<p>Garbage column "new" does not exist anymore.</p>';

	// check if country is disabled, if yes, delete column country
	if( isset( $disable_country ) && $disable_country ) {

		$query = 'ALTER TABLE `' . $db_table .
			'` DROP COLUMN `country`';
		$result = mysql_query( $query );
		if( $result )
			echo '<p>Column "country" disabled, ' .
				'has been dropped.</p>';
		else
			echo '<p>Column "country" disabled, ' .
				'does not exist anymore.</p>';
		}
	else {
		// check country

		$query = 'ALTER TABLE `' . $db_table .
			'` ADD COLUMN `country` varchar(128) NOT NULL ' .
			'default \'\' AFTER name';
		$result = mysql_query( $query );
		if( $result )
			echo '<p>Column "country" enabled but not present, ' .
				'created.</p>';
		else
			echo '<p>Column "country" enabled, ' .
				'already exists.</p>';
		}

	// check if affiliates are enabled
	if( isset( $enable_affiliates ) && $enable_affiliates ) {

		// create affiliates table
		$query = 'CREATE TABLE `' . $db_table . '_affiliates` (' .
			'`affiliateid` int(5) NOT NULL auto_increment, ' .
			'`url` varchar(254) NOT NULL default \'\', ' .
			'`title` varchar(254) NOT NULL default \'\', ' .
			'`imagepath` varchar(254) default NULL, ' .
			'`width` int(3) default NULL, ' .
			'`height` int(3) default NULL, ' .
			'PRIMARY KEY  (`affiliateid`) ' .
			') TYPE=MyISAM AUTO_INCREMENT=1 ;';

		$result = mysql_query( $query );

		if( $result ) {
		echo '<p>Affiliates database table created ' .
			'successfully.</p>';
			}
		else {
			echo '<p>Affiliates table already exists.</p>';
			}
		}
	else {
		echo '<p>Affiliates not enabled, skipping affiliate table ' .
			'creation.</p>';
		}

	//add new fields
	if( $additional_field ) {

		$old_column_name = 'url';
		foreach( $additional_field_names as $field ) {

			$query = 'ALTER TABLE `' . $db_table . '` ADD ' .
				'COLUMN `' . $field . '` VARCHAR(255) ' .
				'DEFAULT NULL AFTER ' . $old_column_name;
			$result = mysql_query( $query );
			if( $result ) {
				echo '<p>Column "' . $field . '" added ' .
					'successfully.</p>';
				}
			else
				echo '<p>Column "' . $field . '" already ' .
					'exists.</p>';
			$old_column_name = $field;
			}

		}

	// to repair deleted/nonexistent columns in database

	// get columns
	$query = 'DESCRIBE ' . $db_table;
	$result = mysql_query( $query );

	$columns = array();
	while( $row = mysql_fetch_array( $result ) ) {
		$columns[] = $row["Field"];
		}

	// check if number of fields is correct
	if( count( $columns ) == ( 9 + count( $additional_field_names ) ) ){
?>
		<p>
		Table count is correct.
		</p>
<?php
		}
	else {
		foreach( $columns as $col ) {
			if( false === array_search( $col,
				$additional_field_names ) &&
				$col != 'email' &&
				$col != 'name' &&
				$col != 'country' &&
				$col != 'url' &&
				$col != 'pending' &&
				$col != 'password' &&
				$col != 'showemail' &&
				$col != 'showurl' &&
				$col != 'added' ) {
				// if $col is not found in
				// $additional_field_names and is not an
				// essential column, delete it
				$query = 'ALTER TABLE ' . $db_table .
					' DROP COLUMN `' . $col . '`';
				$result = mysql_query( $query );
				if( $result ) {
?>
					<p>Previously deleted column "<?=
					$col ?>" successfully deleted from
					the database.</p>
<?php
					}
				else {
?>
					<p>Error deleting previously deleted
					column "<?= $col ?>" from the
					database.</p>
<?php
					}
				}
			}
		}

	}
else {
?>

	<p>
	You can alter/update your database table via this page.<br />
	It will take the values from your config.inc.php and perform<br />
	appropriate actions.
	</p>

	<form action="<?= $_SERVER["PHP_SELF"] ?>" method="get">
	<input type="hidden" name="done" />
	<input type="submit" value="Update my FL Table" />
	</form>

<?php
	}
?>