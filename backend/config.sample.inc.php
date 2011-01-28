<?php
/******************************************************************************
 Your backend directory; do not forget the trailing slash
******************************************************************************/
$backend_dir = 'backend/';



/******************************************************************************
 Type of listing (i.e., 'fanlisting' or 'hatelisting', or 'clique', etc)
******************************************************************************/
$listing_type = 'fanlisting';



/******************************************************************************
 Disable country field, enable affiliates, show sorting in drop-down
******************************************************************************/
$disable_country = false;
$enable_affiliates = true;
$sort_dropdown = true;




/******************************************************************************
 Password you will use to log into the admin system
******************************************************************************/
$set_password = 'password';



/******************************************************************************
 Fanlisting information
******************************************************************************/
$owner_name = 'My Name';
$fanlisting_title = 'Fanlisting Title';
$fanlisting_subject = 'Fanlisting Subject';
$fanlisting_email = 'username@domain.tld';
$fanlisting_url = 'http://localhost/scripts/Enthusiast';



/******************************************************************************
 Database variables
 $db_server - server your MySQL is installed in, usually "localhost"
 $db_user - username you use to connect to your MySQL installation/account
 $db_password - password you use to connect to your MySQL installation/account
 $db_database - database your fanlisting uses (MUST BE PRESENT DURING
                INSTALLATION)
 $db_table - the table within the database for your fanlisting
******************************************************************************/
$db_server = 'localhost';
$db_user = 'username';
$db_password = 'password';
$db_database = 'databasename';
$db_table = 'fanlistingtable';



/******************************************************************************
 Sorting fanlisting members
 $fl_sort - sorts members via the same database column name specified
 $show_sort_field = allows display toggling of the sort field
******************************************************************************/
$fl_sort = 'country';
$show_sort_field = false;



/******************************************************************************
 How many fans are shown per page in the list
******************************************************************************/
$fans_per_page = 5;



/******************************************************************************
 Where links open (value of TARGET attribute in the A tag).
******************************************************************************/
$link_target = '_blank';



/******************************************************************************
 Addtional field variables:
 Set $additional_field to true if you would like to have additional fields;
 Add as many $additional_field_names[] as you want/need, single-quoted
   (should also be the column name of your field in the database, all
   lowercase (automatically title-cased in the site));
******************************************************************************/
$additional_field = true;
$additional_field_names = array();
$additional_field_names[] = 'fieldone';
$additional_field_names[] = 'fieldtwo';



/******************************************************************************
 URLs of various pages in your fanlisting.
******************************************************************************/
$list_url = 'list.php';
$update_url = 'update.php';
$join_url = 'join.php';
$lostpass_url = 'lostpass.php';



/******************************************************************************
 Spacer for affiliates list, optional.
 I.e., if you want them to be separated only by a space, leave it as it is;
 if you want affiliate to be listed on each line, set it to '<br />'
******************************************************************************/
$spacer = ' ';



/******************************************************************************
 Default width and height of affiliate images, optional.
 This is commented by default (no value). If you use one image size for your
 affiliates, uncomment (take away the '//' before each line) the lines below
 and plug the correct values in the variable.
******************************************************************************/
//$default_width = '88';
//$default_height = '31';
?>