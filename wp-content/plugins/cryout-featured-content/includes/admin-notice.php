<?php

/*
Name:        Admin Notice Helper
URI:         https://github.com/iandunn/admin-notice-helper
Version:     0.2
Author:      Ian Dunn
Author URI:  http://iandunn.name
License:     GPLv2
*/

/*  
 * Copyright 2014 Ian Dunn (email : ian@iandunn.name)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as 
 * published by the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Access denied.' );
}

if ( ! class_exists( 'Cryout_Admin_Notice' ) ) {

	class Cryout_Admin_Notice {
		// Declare variables and constants
		protected static $instance;
		protected $notices, $notices_were_updated;

		/**
		 * Constructor
		 */
		protected function __construct() {
			add_action( 'init',          array( $this, 'init' ), 9 );         // needs to run before other plugin's init callbacks so that they can enqueue messages in their init callbacks
			add_action( 'admin_notices', array( $this, 'print_notices' ) );
			add_action( 'shutdown',      array( $this, 'shutdown' ) );
		}

		/**
		 * Provides access to a single instances of the class using the singleton pattern
		 */
		public static function get_singleton() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new Cryout_Admin_Notice();
			}

			return self::$instance;
		}

		/**
		 * Initializes variables
		 */
		public function init() {
			$default_notices             = array( 'info' => array(), 'warning' => array(), 'error' => array(), 'success' => array() );
			$this->notices               = array_merge( $default_notices, (array) get_transient( 'cryout_featured_content_notices' ) );
			$this->notices_were_updated  = false;
		}

		/**
		 * Queues up a message to be displayed to the user
		 */
		public function enqueue( $message, $type = 'warning' ) {
			if ( in_array( $message, array_values( $this->notices[ $type ] ) ) ) {
				return;
			}

			$this->notices[ $type ][]   = (string) $message;
			$this->notices_were_updated = true;
		}

		/**
		 * Displays updates and errors
		 */
		public function print_notices() {
			foreach ( array( 'info', 'success', 'warning', 'error' ) as $type ) {
				if ( count( $this->notices[ $type ] ) ) {
					switch ($type) {
						case 'error': $class = 'notice notice-error is-dismissible'; break;
						case 'success': $class = 'notice notice-success is-dismissible'; break;
						case 'info':  $class= 'notice notice-info is-dismissible'; break;
						case 'warning': 
						default: 	  $class = 'notice notice-warning is-dismissible';	break;
					}
					require( dirname(dirname( __FILE__ )) . '/view/notification.php' );

					$this->notices[ $type ]      = array();
					$this->notices_were_updated  = true;
				}
			}
		}

		/**
		 * Writes notices to the database
		 */
		public function shutdown() {
			if ( $this->notices_were_updated ) {
				set_transient( 'cryout_featured_content_notices', $this->notices, 300 );
			}
		}
	} // end Cryout_Admin_Notice

	Cryout_Admin_Notice::get_singleton(); // Create the instance immediately to make sure hook callbacks are registered in time

	if ( ! function_exists( 'cryout_add_admin_notice' ) ) {
		function cryout_add_admin_notice( $message, $type = 'warning' ) {
			Cryout_Admin_Notice::get_singleton()->enqueue( $message, $type );
		}
	}
}
