<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'truinc' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root@123' );

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
define( 'AUTH_KEY',         ',hDjor,9QcgwEi5GP@bonPb_O{h%QuE- ZxjUk,r5Aqj&K{}@d%|G-IHD4iwjECS' );
define( 'SECURE_AUTH_KEY',  'GMHeV(svGTF,1/9f>S_+/WvX01ZlZ12TQbD0JM3+M~SObPElc:OQ~B5puJ8j{hu$' );
define( 'LOGGED_IN_KEY',    'N`w*F=y^rae#5mp,vm_{YP `V`FW`u8Lx4D]9^@&Hqzq$FUJnFd%;|iH&(2uN1ca' );
define( 'NONCE_KEY',        '+3wb[Ym<Jl8s$D3_Cd-4,>[;&tn_+}S4Qz:Hj&fXUbCo$oMk<iH&}=Gd V+&;-%(' );
define( 'AUTH_SALT',        'M;B@$F`2L231[Dad1p{Ul*df8V?t=$ -cLWJ[^UYaAy4V0t+)+9<+Tr6<OL.vWb<' );
define( 'SECURE_AUTH_SALT', '7>/vVT/ek?%*5[!;=A;DUbdeOxxM;h;[PH$A5]WI~x&!J+*WMOgeZK]p:at OJ F' );
define( 'LOGGED_IN_SALT',   'Hmn41qLYG}=rp00]? Y=0|&jV}RD`o*c*3tU<ch*eE74Gx#9%3]oS9iA~Tl7hS`P' );
define( 'NONCE_SALT',       '%/!HQG7`;i!!Ui24WPo<#GSb2WGLo8_O)t;%6.Zo.6*}%+w15O]xt&bZ/b!y0qqX' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
