
Enthusiast Lite
-----------------------------
Copyright (c) Angela Sabas
http://scripts.indisguise.org
=============================

Enthusiast Lite is a tool for fanlisting owners to easily maintain their
fanlistings.

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



Features:
---------
. Includes TheFanlistings.Org required fields Name, Email, and Country, and
  additional fields such as:
     - Password
     - Website URL
     - Show/Hide Email
     - Show/Hide Website URL (admin-editable only)
     - Comments (not stored in database)
     - Multiple Customizable Fields (toggable on/off, as many as needed)
. Sort members list in any way using the additional customized fields
  (default by country)
. Integrated (image/text link) affiliates system, enabled by default
. Customize emails sent out from fanlisting via templates and special variables
. Easily customize members' list using a list template and special variables
. Add additional custom email templates for easy emailing
. Members' passwords are encrypted in the database for security
. Single-click membership approval (single or multiple)
. Member search in admin system
. Automatic emailing of members upon joining, upon approval, and upon
  their changing of information (all automated)
. Automatic emailing of owner when a person joins the listing
. Ability to show number of approved members, number of pending members,
  date of last member update, and newest members
. Ability to mass email/email members individually via the admin system
. Owner-only capability of "hiding" website urls of members but retaining
  the URL recorded in the database
. Easily add additional fields and customize the way they are displayed on
  the join form
. Supports join/list/update urls with ?'s (i.e., index.php?file=join)
. Ability to specify the link target (i.e., _blank) of member links in member
  list
. Supports resetting of passwords in case a member forgets his/her password
. Supports changing the type of listing used (i.e., fanlisting, hatelisting,
  clique, etc)
. Supports turning off 'country' field for use in cliques
. Repair/update databases even when deleting and adding new aditional columns
. Database errors when joining can be easily sent by fans attempting to
  join for quicker response/fix
. Checks if a valid email address (with an '@' sign) is used to join
. Automatically appends 'http://' to the front of a website URL if it is not
  present
. "Remember me?" function for the admin panel
. Spam-protected member emails if shown on member list
. XHTML 1.0 Transitional-friendly


What's new:
-----------
See changelog.txt


Requirements:
-------------
PHP 4.3.0
MySQL 3.23.53
* These are what I used to develop the script; if you have tested it with
lower versions of PHP and MySQL and it works, please tell me. :)


Installation and configuration:
-------------------------------
See install.txt


Upgrading:
----------
See upgrading.txt


Troubleshooting and Help
------------------------
For troubleshooting and help, please browse/ask the Enthusiast Script
discussion list at http://groups.yahoo.com/group/enthusiast_script -- please
DO NOT email me for troubleshooting help on ANY email address or ANY other
contact form. I will NOT answer support requests emailed to my email
addresses -- no exceptions.