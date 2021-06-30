<?php
/**
 * Handles storage of the custom user fields meta keys to inject while searching through a directory.
 *
 * @package     wpum-group
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('WPUM_DB_Table')) {
	return;
}
/**
 * Setup the global "wpum_search_fields" database table
 */
final class WPUMG_DB_Table_Group_Users extends WPUM_DB_Table {

	/**
	 * Table name
	 *
	 * @access protected
	 * @var string
	 */
	protected $name = 'wpum_group_users';

	/**
	 * Database version
	 *
	 * @access protected
	 * @var int
	 */
	protected $version = 20210616;

	/**
	 * Setup the database schema
	 *
	 * @access protected
	 * @return void
	 */
	protected function set_schema() {

		$datetime = current_time( 'mysql' );

		$this->schema     = "id bigint(20) unsigned NOT NULL auto_increment,
			group_id bigint(20) NOT NULL default '0',
			user_id bigint(20) NOT NULL default '0',
			role varchar(255) NOT NULL default '',
			status varchar(255) NOT NULL default 'pending',
			joined_at date NOT NULL default '{$datetime}',
			PRIMARY KEY (id)";
	}

	/**
	 * Handle schema changes
	 *
	 * @access protected
	 * @return void
	 */
	protected function upgrade() {
		$this->create();
	}

}
