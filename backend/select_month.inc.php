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
$month_array = array();
$month_array[] = 'January';
$month_array[] = 'February';
$month_array[] = 'March';
$month_array[] = 'April';
$month_array[] = 'May';
$month_array[] = 'June';
$month_array[] = 'July';
$month_array[] = 'August';
$month_array[] = 'September';
$month_array[] = 'October';
$month_array[] = 'November';
$month_array[] = 'December';

$i = 1;
foreach( $month_array as $month ) {
	if( $month == date( 'F' ) )
		echo '<option value="' . $i .
			'" selected="selected">' . $month . '</option>';
	else
		echo '<option value="' . $i . '">' . $month .
			'</option>';
	$i++;
	}
?>