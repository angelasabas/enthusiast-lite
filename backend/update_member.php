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

// clean function
function clean( $data ) {
   $data = trim( htmlentities( strip_tags( $data ), ENT_QUOTES ) );

   if( get_magic_quotes_gpc() )
      $data = stripslashes( $data );

   $data = addslashes( $data );

   return $data;
}

$show_form = true;
$message = '';

$old_email = '';

$new_name = '';
$new_email = '';
$new_country = '';
$new_url = '';
$delete_url = 0;
if( $additional_field ) {
	$additional_fields = array();
	foreach( $additional_field_names as $field )
		$additional_fields["$field"] = ' ';
	}

if( isset( $_POST["update"] ) && $_POST["update"] == "yes" ) {

   // do some spam/bot checking first
   $goahead = false;
   $badStrings = array( 'Content-Type:',
      'MIME-Version:',
      'Content-Transfer-Encoding:',
      'bcc:',
      'cc:',
      'content-type',
      'onload',
      'onclick',
      'javascript' );
   // 1. check that user is submitting from browser
   // 2. check the POST was indeed used
   // 3. no bad strings in any of the form fields
   if( isset( $_SERVER['HTTP_USER_AGENT'] ) &&
      $_SERVER['REQUEST_METHOD'] == 'POST' ) {
      foreach( $_POST as $k => $v ) {
         foreach( $badStrings as $v2 ){
            if( strpos( $v, $v2 ) !== false ) {
               die( "<p$errorstyle>Bad strings found in form.</p>" );
               exit;
            }
         }
      }
      $goahead = true;
   }
   unset( $k, $v, $v2, $badStrings );
   if( !$goahead ) {
      echo "<p$errorstyle>ERROR: Attempted circumventing of the form detected.</p>";
      return;
   }

   $matchstring = "^([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+" .
      "@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}$";
   if( $_POST['old_email'] && ereg( $matchstring, $_POST['old_email'] ) )
		$old_email = clean( $_POST["old_email"] );
   else
		$message = 'Invalid email supplied.';

	$old_password = clean( $_POST["old_password"] );

   $matchstring = "^([0-9a-zA-Z]+[-._+&])*[0-9a-zA-Z]+" .
      "@([-0-9a-zA-Z]+[.])+[a-zA-Z]{2,6}$";
   if( $_POST['new_email'] && ereg( $matchstring, $_POST['new_email'] ) )
		$new_email = clean( $_POST["new_email"] );
	else
		$message = 'Invalid email supplied.';

	$new_password = clean( $_POST["new_password"] );
	$new_vpassword = clean( $_POST["new_vpassword"] );

	$new_name = clean( $_POST["new_name"] );

	if( isset( $_POST["new_country"] ) )
		$new_country = clean( $_POST["new_country"] );

	$new_url = clean( $_POST["new_url"] );
	$new_show_email = clean( $_POST["new_show_email"] );
	if( isset( $_POST["delete_url"] ) )
		$delete_url = clean( $_POST["delete_url"] );

	if( $additional_field ) {
		foreach( $additional_field_names as $field ) {
			$additional_fields["$field"] = clean( $_POST["new_$field"] );
			}
		}

	if( !$message && !( $old_email ) && !( $old_password ) ) {
		$message = 'You must enter your old email and your old ' .
			'password to verify your request for information ' .
			'update.';
		}
	elseif( !$message && $new_password && $new_vpassword &&
		$new_password != $new_vpassword ) {
		$message = 'Your new password does not match the ' .
			'verification. Please retype your new password.';
		}
	else if( !$message ) {
		$show_form = false;

		// create sql query
		$query = 'UPDATE ' . $db_table . ' SET ';
		if( $new_email )
			$query .= 'email = "' . $new_email . '",';
		if( $new_name )
			$query .= 'name = "' . $new_name . '",';
		if( $new_country )
			$query .= 'country = "' . $new_country . '",';
		if( $new_password && $new_password == $new_vpassword )
			$query .= 'password = PASSWORD( "' .
				$new_password . '" ),';
		if( $new_show_email != 2 )
			$query .= 'showemail = ' . $new_show_email . ',';
		if( $delete_url == 1 )
			$query .= 'url = NULL,';
		elseif( $new_url )
			$query .= 'url = "' . $new_url . '",';

		if( $additional_field ) {
			reset( $additional_fields );
			do {
				if( current( $additional_fields ) ==
					'deletethisfield' )
					$query .= $db_table . '.' .
						key( $additional_fields ) . ' = NULL,';
				else if(current($additional_fields) != '' &&
                                   current($additional_fields) != ' ' )
					$query .= $db_table . '.' .
						key( $additional_fields ) . ' = "' .
						current( $additional_fields ) . '",';
				} while( next( $additional_fields ) );
			}

		$query = rtrim( $query, ',' );
		$query .= ' WHERE email = "' . $old_email . '" AND password ' .
			'= PASSWORD( "' . $old_password . '" )';

		$db_link = mysql_connect( $db_server, $db_user, $db_password )
			or die( 'Cannot connect to the database. Try again.' );
		mysql_select_db( $db_database )
			or die( 'Cannot connect to the database. Try again.' );

		mysql_query( $query );
		$affected_rows = mysql_affected_rows();
		mysql_close( $db_link );

		if( $affected_rows > 0 ) {
			$show_form = false;

			if( $new_email )
				$to = $new_email;
			else
				$to = $old_email;
			$subject = $fanlisting_title .
				' FL: Information change';
			$headers = "From: " . $fanlisting_title . " <" .
				$fanlisting_email . ">\r\n";

			$update_file = $backend_dir . 'emails/update.txt';
			$update_email = fopen( $update_file, 'r' );
			$message = fread( $update_email, filesize(
				$update_file ) );
			fclose( $update_email );

			require_once( $backend_dir . 'parse_email.php' );
			$message = parse_email( $message, $to, $new_password );

			$success_mail = mail( $to, $subject, $message,
				$headers );

			if( $success_mail ) {
?>
				<p>
				Your information has been successfully changed
				and an email has been sent to you regarding
				these changes.
				</p>
<?php
				}
			else {
?>
				<p>
				Your information has been successfully changed,
				however, there was a problem sending you an
				email regarding these changes. You can check
				if your changes has been successfully
				stored by looking for your fan record at
				the <a href="<?= $list_url ?>">fan list</a>
				instead.
				</p>
<?php
				}
			}
		else {
?>
			<p>
			There was an error in your request to update your
			information. Please check your email and password and
			try again.
			</p>
<?php
			}

		} // end of if email and password is present

	} // end of if update = yes

if( $show_form ) {
?>
	<p>
	If you're a member of the <?= $listing_type ?> and you want to modify
	your information listed here, please fill out the form below. Your
	[old] email address and password is required for this form.
	</p>

	<p>
	<b>Important:</b> Leave the fields you wish unchanged blank, and hit
	submit only once when you are sure you want to change your
	information.
	</p>

	<p>
	<b><?= $message ?></b>
	</p>

	<p>
	<a href="<?= $join_url ?>">If you want to join the <?= $listing_type
	?>, please use this other form.</a>
	</p>

	<form method="post" action="<?= $update_url ?>">
	<input type="hidden" name="update" value="yes" />

	<p class="noindent">
	* Old email address:<br />
	<input type="text" name="old_email" value="<?= $old_email ?>" />
	</p>

	<p class="noindent">
	* Current password: (<a href="<?= $lostpass_url ?>">Lost it?</a>)<br />
	<input type="password" name="old_password" />
	</p>

	<p class="noindent">
	New name:<br />
	<input type="text" name="new_name" />
	</p>

	<p class="noindent">
	New email address:<br />
	<input type="text" name="new_email" />
	</p>

	<p class="noindent">
	New password (type twice):<br />
	<input type="password" name="new_password" />
	<input type="password" name="new_vpassword" />
	</p>

	<p class="noindent">
	Show email address?<br />
	<input type="radio" name="new_show_email" value="2" checked="checked"
		class="noborder" /> Leave it as it is<br />
	<input type="radio" name="new_show_email" value="1" class="noborder" />
		Yes (SPAM-protected on the site)<br />
	<input type="radio" name="new_show_email" value="0" class="noborder" />
		No
	</p>

<?php
	if( !isset( $disable_country ) || !$disable_country ) {
?>
		<p class="noindent">
		New country<br />
		<select name="new_country">
		<option value=""></option>
<?php
		include $backend_dir . 'countries.inc.php';
?>
		</select>
		</p>
<?php
		}
?>

	<p class="noindent">
	New website URL:<br />
	<input type="text" name="new_url" /><br />
	<input type="checkbox" name="delete_url" value="1" class="noborder" />
		Delete your website?
	</p>

<?php
	if( $additional_field ) {
		reset( $additional_fields );
		do {
?>
			<p class="noindent">
			New <?= key( $additional_fields ) ?>:<br />
			<input type="text" name="new_<?=
				key( $additional_fields ) ?>" value="<?=
				current( $additional_fields ) ?>" /><br />
			<input type="checkbox" name="new_<?=
				key( $additional_fields ) ?>"
				value="deletethisfield" /> Delete <?=
				key( $additional_fields ) ?>
			</p>
<?php
			} while( next( $additional_fields ) );
		}
?>

	<p class="noindent">
	<input type="submit" value="Modify my information" />
	</p>

	</form>

<?php
	}
?>