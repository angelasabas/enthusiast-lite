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
function parse_email( $template, $fan_email, $password = '' ) {
	require( 'config.inc.php' );

	// retrieve values from database
	$query = 'SELECT * FROM ' . $db_table . ' WHERE email = "' .
		$fan_email . '"';
	$db_link = mysql_connect( $db_server, $db_user, $db_password )
		or die( 'Cannot connect to the database. Try again.' );
	mysql_select_db( $db_database )
		or die( 'Cannot connect to the database. Try again.' );
	$result = mysql_query( $query );
	$row = mysql_fetch_array( $result );

	// search and replace special variables
	$template = str_replace( '$$owner_name$$', $owner_name, $template );
	$template = str_replace( '$$fanlisting_title$$', $fanlisting_title,
		$template );
	$template = str_replace( '$$fanlisting_subject$$',
		$fanlisting_subject, $template );
	$template = str_replace( '$$fanlisting_email$$', $fanlisting_email,
		$template );
	$template = str_replace( '$$fanlisting_url$$', $fanlisting_url,
		$template );
	$template = str_replace( '$$fanlisting_list$$', $list_url,
		$template );
	$template = str_replace( '$$fanlisting_update$$', $update_url,
		$template );
	$template = str_replace( '$$fanlisting_join$$', $join_url,
		$template );
	$template = str_replace( '$$fanlisting_lostpass$$', $lostpass_url,
		$template );
	$template = str_replace( '$$listing_type$$', $listing_type,
		$template );
	$template = str_replace( '$$fan_name$$', $row["name"], $template );
	$template = str_replace( '$$fan_email$$', $row["email"], $template );
	if( !isset( $disable_country ) || !$disable_country )
		$template = str_replace( '$$fan_country$$', $row["country"],
			$template );
	$template = str_replace( '$$fan_url$$', $row["url"], $template );
	$template = str_replace( '$$fan_password$$', $password, $template );
	foreach( $additional_field_names as $field ) {
		$template = str_replace( '$$fan_' . $field . '$$',
			$row["$field"], $template );
		}

	return $template;
	}
?>