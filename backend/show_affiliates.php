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
require_once( 'config.inc.php' );
require_once( $backend_dir . 'get_affiliates.php' );

$affiliate_array = get_affiliates();

foreach( $affiliate_array as $aff ) {

	// show text if there is no images
	if( !isset( $aff["imagepath"] ) || $aff["imagepath"] == '' )  {

		if( !isset( $spacer ) )
			$spacer = '<br />';

		if( !isset( $link_target ) )
			$link_target = '_top';

		echo '<a href="' . $aff["url"] . '" target="' .
			$link_target . '">' . $aff["title"] .
			'</a>' . $spacer;
		}

	else {

		if( !isset( $spacer ) )
			$spacer = ' ';

		if( !isset( $link_target ) )
			$link_target = '_top';

		echo '<a href="' . $aff["url"] . '" target="' .
			$link_target . '">' .
			'<img src="' . $aff["imagepath"] .
			'" width="' . $aff["width"] .
			'" height="' . $aff["height"] .
			'" border="0" alt=" ' . $aff["title"] . '" /></a>' .
			$spacer;
		}
	}
?>