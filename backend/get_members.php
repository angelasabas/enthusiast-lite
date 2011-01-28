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
function get_members( $email, $name, $country, $url, $extra, $pending,
	$date_day, $date_month, $date_year, &$count ) {

	require( 'config.inc.php' );
	// create SQL query
	$sql_query = "SELECT * FROM `$db_table`";
	$criteria = '';

	if( $email ) {
		$email_criteria = " `email` LIKE '%$email%'";
		if( $criteria )
			$criteria .= " AND $email_criteria";
		else
			$criteria = $email_criteria;
		}

	if( $name ) {
		$name_criteria = ' `name` LIKE "%' . $name . '%"';
		if( $criteria )
			$criteria .= ' AND' . $name_criteria;
		else
			$criteria = $name_criteria;
		}

	if( $country ) {
		$country_criteria = ' `country` LIKE "%' . $country . '%"';
		if( $criteria )
			$criteria .= ' AND' . $country_criteria;
		else
			$criteria = $country_criteria;
		}

	if( $url ) {
		$url_criteria = ' `url` LIKE "%' . $url . '%"';
		if( $criteria )
			$criteria .= ' AND' . $url_criteria;
		else
			$criteria = $url_criteria;
		}

	if( $extra ) {
		$extra_criteria = ' `' . $additional_field_name . '` LIKE "%' .
			$extra . '%"';
		if( $criteria )
			$criteria .= ' AND' . $extra_criteria;
		else
			$criteria = $extra_criteria;
		}

	if( $pending == 0 || $pending == 1 ) {
		$pending_criteria = " pending = '$pending'";
		if( $criteria )
			$criteria .= ' AND' . $pending_criteria;
		else
			$criteria = $pending_criteria;
		}

	if( $date_day ) {
		$date_day_criteria = " DAYOFMONTH( date ) = '$date_day'";
		if( $criteria )
			$criteria .= ' AND' . $date_day_criteria;
		else
			$criteria = $date_day_criteria;
		}

	if( $date_month ) {
		$date_month_criteria = " MONTH( date ) = '$date_month'";
		if( $criteria )
			$criteria .= ' AND' . $date_month_criteria;
		else
			$criteria = $date_month_criteria;
		}

	if( $date_year ) {
		$date_year_criteria = " YEAR( date ) = '$date_year'";
		if( $criteria )
			$criteria .= ' AND' . $date_year_criteria;
		else
			$criteria = $date_year_criteria;
		}

	if( $criteria ) {
		$sql_query .= ' WHERE' . $criteria;
		}
	$sql_query .= ' ORDER BY `email` ASC';

	// connect to the database using config file
	$db_link = mysql_connect( $db_server, $db_user, $db_password )
		or die( 'Cannot connect to the MySQL server: ' .
			mysql_error() );
	mysql_select_db( $db_database )
		or die( 'Cannot select database: ' . mysql_error() );

	// get results
	$result_set = mysql_query( $sql_query )
		or die( 'Cannot execute query: ' . mysql_error() );
	$member_array = array();
	while( $row = mysql_fetch_array( $result_set ) )
		$member_array[] = $row;

	$sql_query = str_replace( '*', 'COUNT( email ) AS num', $sql_query );
	$result_num = mysql_query( $sql_query )
		or die( 'Cannot execute query: ' . mysql_error() );
	$row = mysql_fetch_array( $result_num );
	$count = $row["num"];
	
	// free resources
	mysql_free_result( $result_set );
	mysql_free_result( $result_num );
	mysql_close( $db_link );
	
	// return value
	return $member_array;
	}
?>