<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'uE8aSsq28IwAkUTQdmzlMbLHWKAKce74wEmGHLsFh0XEjzqKTPvPlhT2hHw9gEKJ0tKhAlK4s9TBJ+BdNtqpDQ==');
define('SECURE_AUTH_KEY',  'lGC9N2AekhAyAZb7k+S3vTD3OsiS8Y9/c5PbBLQX2yi1r2vz4JKE23ZSrlRh99Q883t7qD96Q9rDWTNPGyNFxg==');
define('LOGGED_IN_KEY',    'wnsb08xrsaJNNXzEVSCkP6KRCRr+3mpKR59OE7gjKULYRW41VR1g5r9ocPbAqdqVJ9UEyvqMUONTFsgXthP3AQ==');
define('NONCE_KEY',        'avvXdvSUSycFR6ZWWNG6xQFLPH0feFV8qGXx/m4OK0OW0sXFL+JOcSWQ9h3bMti+ABBKj+yACe67CWfN3wgfXQ==');
define('AUTH_SALT',        'JlaPfYQg8YasBV8OtMViI7s6IxzpMJAlbYET/Swuq102NmMBszyBeI6/qbkHafAfwlZIlCVqzJeM/YBaTyRBwA==');
define('SECURE_AUTH_SALT', 'aEsIoh+iKft8zLfUN/a6qAKBsTCFEEGZvXSzQ0pQEhfD+stpKzy7AQNT1wwlY5TPRSsPO9n2f/Ju7g4FBY41sw==');
define('LOGGED_IN_SALT',   'P52c3DAWNsjGxesxwX0l/WS41LZd394Yd4QCz/wbawqqkgdH61r9AEMQMp12sIMtdGTQ1XigQq6YAdvCN7Iviw==');
define('NONCE_SALT',       'm1Li5FHXDZ1bZ5dubo6jWWheKSofxOeCgThPFPQ6pwAvcf1SX/QCxhXp9SomW1GcLVXQFOGMZCmtCvFZ7kHBgA==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
