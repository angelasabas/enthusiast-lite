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
function get_affiliates( $affiliate_id ='', $url = '',
	$title = '', $image_path = '', $width = '', $height = '',
	$comparison = 'AND' ) {
	require( 'config.inc.php' );

	// create query
	$query = 'SELECT * FROM ' . $db_table . '_affiliates';
	$query .= ' ORDER BY title ASC';

	if( $affiliate_id ) {
		$query = 'SELECT * FROM ' . $db_table . '_affiliates' .
			' WHERE affiliateid = ' . $affiliate_id;
		}

	$db_link = mysql_connect( $db_server, $db_user, $db_password )
		or die( 'Cannot connect to the database. Try again.' );
	mysql_select_db( $db_database )
		or die( 'Cannot connect to the database. Try again.' );
	$result = mysql_query( $query )
		or die( 'Error executing query: ' . mysql_error() );

	$affiliate_array = array();
	while( $row = mysql_fetch_array( $result, MYSQL_ASSOC ) ) {
		$affiliate_array[] = $row;
		}

	mysql_free_result( $result );
	mysql_close( $db_link );

	return $affiliate_array;
	}
?>