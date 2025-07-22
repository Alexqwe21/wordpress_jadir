<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u230564252_jadirstore' );
define( 'DB_USER', 'u230564252_jadirstore' );
define( 'DB_PASSWORD', '21566647aA#' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'y-3k|8h%$>Ugs<8GffXTGn}L-<7l)1 IUSb vv~#:ob3@fV}~74]E0;PmtJx@rB=' );
define( 'SECURE_AUTH_KEY',  'g|VEt3i*7lIE(_Fe!^an7xov+rM998U;u>?mjwy{>sLr7.>2%~s[]wYu{Zl37xGB' );
define( 'LOGGED_IN_KEY',    '`r/)+Vy`R,:b_^9ov43Q{%37.i<T&@c<Qua%)m1gV(?Q1kW26ZCeu9C7NaQm_o?I' );
define( 'NONCE_KEY',        '8F?Vw^(fI(B?&A3@kPnf1;2x`vV5v2;PG&jp97zyzj&sw`{i%HhjHU~84dXIhF[V' );
define( 'AUTH_SALT',        '7p6tm`#4TpK:uqBsmaek2KfD17~.=yW8_~Mi>ox<kxOP5A[)|(V2Cpv2Y;d*|K1P' );
define( 'SECURE_AUTH_SALT', '!u`T}y1l!?W?z3Yj5{W7:n89st6#F75lvQ_u2A_EsMwoOXz<Sat+4ZFj%d<DMZOV' );
define( 'LOGGED_IN_SALT',   '_RI^9 T2R1^zd4PJgR#[^%{C N6p<f2XcWLD.3l;iPsW#nw>rq&RYf8,Z<?~dV3=' );
define( 'NONCE_SALT',       'PGh>b5y8cVF.Ji/mhC*5.k]tVHkSP~Q97>WJP1mOxTm, <PIcdZ!z@PdJoOsd]N ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
