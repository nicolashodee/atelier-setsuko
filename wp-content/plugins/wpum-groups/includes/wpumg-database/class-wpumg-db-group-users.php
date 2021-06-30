<?php
/**
 * Handles connection with the db to manage the search fields.
 *
 * @package     wpum-group
 * @license     https://opensource.org/licenses/GPL-3.0 GNU Public License
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('WPUM_DB')) {
	return;
}

/**
 * WPUM_DB_Search_Fields Class
 */
class WPUMG_DB_Group_Users extends WPUM_DB {

	/**
	 * The name of the cache group.
	 *
	 * @access public
	 * @var    string
	 */
	public $cache_group = 'group_users';

	/**
	 * Initialise object variables and register table.
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name  = $wpdb->prefix . 'wpum_group_users';
		$this->primary_key = 'id';
		$this->version     = '1.0';
	}

	/**
	 * Retrieve table columns and data types.
	 *
	 * @access public
	 * @return array Array of table columns and data types.
	 */
	public function get_columns() {
		return array(
			'id'        => '%d',
			'group_id'  => '%d',
			'user_id'   => '%d',
			'role'      => '%s',
			'joined_at' => '%s',
			'status'    => '%s',
		);
	}

	/**
	 * Get default column values.
	 *
	 * @access public
	 * @return array Array of the default values for each column in the table.
	 */
	public function get_column_defaults() {
		return array(
			'id'        => 0,
			'group_id'  => 0,
			'user_id'   => 0,
			'role'      => 'subscriber',
			'joined_at' => current_time( 'mysql' ),
			'status'    => 'approved',
		);
	}

	/**
	 * Insert a new field.
	 *
	 * @access public
	 *
	 * @param array  $data
	 * @param string $type
	 *
	 * @return int ID of the inserted field.
	 */
	public function insert( $data, $type = '' ) {
		$result = parent::insert( $data, $type );

		if ( $result ) {
			$this->set_last_changed();
		}

		return $result;
	}

	/**
	 * Update a field.
	 *
	 * @access public
	 * @param int   $row_id field ID.
	 * @param array $data
	 * @param mixed string|array $where Where clause to filter update.
	 *
	 * @return  bool
	 */
	public function update( $row_id, $data = array(), $where = '' ) {
		$result = parent::update( $row_id, $data, $where );

		if ( $result ) {
			$this->set_last_changed();
		}

		return $result;
	}

	/**
	 * Delete field.
	 *
	 * @access public
	 * @param int $row_id ID of the field to delete.
	 * @return bool True if deletion was successful, false otherwise.
	 */
	public function delete( $row_id = 0 ) {
		if ( empty( $row_id ) ) {
			return false;
		}

		$result = parent::delete( $row_id );

		if ( $result ) {
			$this->set_last_changed();
		}

		return $result;
	}

	/**
	 * Retrieve a specific row's value by the the specified column / value
	 *
	 * @access  public
	 *
	 * @param $group_id
	 * @param $user_id
	 *
	 * @return int
	 */
	public function get_row_by( $group_id, $user_id ) {
		global $wpdb;
		$group_id      = esc_sql( $group_id );
		$user_id       = esc_sql( $user_id );
		return $wpdb->get_var( $wpdb->prepare( "SELECT id FROM $this->table_name WHERE group_id = %s AND user_id = %s LIMIT 1;", $group_id, $user_id ) );
	}

	/**
	 * Retrieve a specific row's value by the the specified column / value
	 *
	 * @access  public
	 *
	 * @param int $group_id
	 * @param int $user_id
	 *
	 * @return  array
	 */
	public function get_user_roles( $group_id, $user_id ) {
		global $wpdb;
		$group_id = esc_sql( $group_id );
		$user_id  = esc_sql( $user_id );
		$data     = $wpdb->get_var( $wpdb->prepare( "SELECT role FROM $this->table_name WHERE group_id = %s AND user_id = %s LIMIT 1;", $group_id, $user_id ) );
		if ( empty( $data ) ) {
			return array();
		}

		return unserialize( $data );
	}

	/**
	 * Retrieve a users id value by the the specified column / value
	 *
	 * @access  public
	 *
	 * @param int  $group_id
	 * @param null|string $status
	 *
	 * @return  array
	 */
	public function get_users_by( $group_id, $status = null ) {
		global $wpdb;
		$group_id = esc_sql( $group_id );

		$sql = $wpdb->prepare( "SELECT user_id, role, joined_at, status FROM $this->table_name WHERE group_id = %s", $group_id );

		if ( $status ) {
			$sql .= $wpdb->prepare( ' AND status = %s', $status );
		}

		return $wpdb->get_results( $sql );
	}

	/**
	 * @param int   $group_id
	 * @param string $status
	 *
	 * @return array
	 */
	public function get_users_by_status( $group_id, $status = 'approved' ) {
		$group_users = $this->get_users_by( $group_id );
		$users = array();

		foreach ( $group_users as $user ) {
			if ( $user->status === $status ) {
				$users[] = $user;
			}
		}

		return $users;
	}

	/**
	 * @param int   $group_id
	 * @param array $roles
	 *
	 * @return array
	 */
	public function get_users_by_role( $group_id, $roles = array() ) {
		$group_users = $this->get_users_by( $group_id );
		$users = array();

		foreach( $group_users as $user ) {
			$user_roles = unserialize( $user->role );

			if ( array_intersect( $user_roles, $roles ) ) {
				$user->user_email = get_user_by( 'id', $user->user_id )->user_email;
				$users[]          = $user;
			}
		}

		return $users;
	}

	/**
	 * Retrieve a group list by the the specified user_id
	 *
	 * @access  public
	 *
	 * @param int $user_id
	 *
	 * @return array
	 */
	public function get_groups_by( $user_id ) {
		global $wpdb;
		$user_id = esc_sql( $user_id );

		return $wpdb->get_results( $wpdb->prepare( "SELECT group_id FROM $this->table_name ug INNER JOIN $wpdb->posts p ON ug.group_id = p.ID  WHERE user_id = %s AND p.post_status = 'publish' AND status ='approved';", $user_id ), 'ARRAY_A' );
	}

	/**
	 * Retrieve a row for a user and group
	 *
	 * @access  public
	 *
	 * @param int $group_id
	 * @param int $user_id
	 *
	 * @return object
	 */
	public function get_row( $group_id, $user_id ) {
		global $wpdb;
		$group_id = esc_sql( $group_id );
		$user_id  = esc_sql( $user_id );

		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $this->table_name WHERE group_id = %s AND user_id = %s;", $group_id, $user_id ) );
	}

	/**
	 * Retrieve a group list by the the specified user_id
	 *
	 * @access  public
	 *
	 * @param int $group_id
	 * @param int $user_id
	 *
	 * @return  bool
	 */
	public function is_group_member( $group_id, $user_id ) {
		global $wpdb;
		$group_id      = esc_sql( $group_id );
		$user_id      = esc_sql( $user_id );
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT group_id FROM $this->table_name WHERE group_id = %s AND user_id = %s AND status = 'approved';", $group_id, $user_id ), 'ARRAY_A' );

		return ! empty( $results );
	}

	/**
	 * Sets the last_changed cache key for fields.
	 *
	 * @access public
	 */
	public function set_last_changed() {
		wp_cache_set( 'last_changed', microtime(), $this->cache_group );
	}

	/**
	 * Retrieves the value of the last_changed cache key for fields.
	 *
	 * @access public
	 * @return string Value of the last_changed cache key for fields.
	 */
	public function get_last_changed() {
		if ( function_exists( 'wp_cache_get_last_changed' ) ) {
			return wp_cache_get_last_changed( $this->cache_group );
		}

		$last_changed = wp_cache_get( 'last_changed', $this->cache_group );
		if ( ! $last_changed ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, $this->cache_group );
		}

		return $last_changed;
	}

}
