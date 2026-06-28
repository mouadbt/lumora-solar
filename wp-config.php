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
define( 'DB_NAME', 'lumora_solar' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
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
define( 'AUTH_KEY',         '-vzDOUD>#&N;eO*DQ5yu0j#{6P|0%V{J3GEFAA>E#kQP-05m?LK1hY(-qa_6@DS~' );
define( 'SECURE_AUTH_KEY',  'l]%5[:4iK@YYj7br>]b@L[:X:X.}xj6eRM! dvW/x4}djKbeCO+=,Lj>#-x|Bx#&' );
define( 'LOGGED_IN_KEY',    'eY-@k:k4}br*Oy2QR[TcoCEi`_h+7:hp2bfIY<=%T=P/:m&#$Xt3j0zy!kSgMg[@' );
define( 'NONCE_KEY',        '^55~YaG]2iKT4BBFxu6:E9Qy[F|~]Ho8)ii][},y$.I}[LW!C#|3#-c@t+D/_ar>' );
define( 'AUTH_SALT',        '(aH<]9BIA[OAzL]S9)=gqasY>YYAK,TLO$qJ[Iby etuVbgjwmOkg~V><;F.i_a;' );
define( 'SECURE_AUTH_SALT', '69A1i4yTA|{8(X>Uh>RX*}se>pIZV_{lQ)<P8V,-Q:d?dz(KQ@(fI%op>utK.IW$' );
define( 'LOGGED_IN_SALT',   '4py<c^,xUMn*mc|RX+=!M82I3+#SHs*O^J#:e?[Q%3[Y7m|bc_(@=`>T/p>d`G`r' );
define( 'NONCE_SALT',       'IZV5h%YVkEdiEvd}jyw`)F=.s)Lz)2+cAV1l<EYmdz6nn^Us_EgK)CGpoRmertN{' );

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
