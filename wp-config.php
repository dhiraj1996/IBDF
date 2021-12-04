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
define('DB_NAME', 'ibdf');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'ei81I1`+qlOz2P=k!{C/F6)KTdcsJx{48_C8i87dwY!#**&~+p#vQ8$i4&RKEXl|');
define('SECURE_AUTH_KEY',  ' tR}9t],!3a,UJ0Wr;<+bd)]*,l*zxJ;^CtuZc[$?1] FFBv)^A{$,BDJcD* -AV');
define('LOGGED_IN_KEY',    ';8^jMpuE(FZ:%3m&1JjCRtvi>M`,3XLjt~u}e9Mn:EE(74R[C#(7q`gnyg-y*JtG');
define('NONCE_KEY',        'C]MOn/8-)Qssfn[]nnS4Ku!ov#2?wsqDqxB[Y9QzI}InyC|8NsfkKMaVQph%-E-W');
define('AUTH_SALT',        'Jgs#frj0>L` kh6FI$;)v9UA/r5r]I/ET=7KcB1P1[<6DN&R@nQ}]j+$?b]TCJ9+');
define('SECURE_AUTH_SALT', '3XbKHdCM*hi@UOL1@3S`WrTDsgfK6%`l)s6T6tO[Uf#fWxL=QQ6W|GBR*ha?9zY=');
define('LOGGED_IN_SALT',   't{3>t8z=.`CN{nishve8iNZp#X-92[F2SLQ[ubza*xWV)irG&GF*Q:#Q8y.WwKx|');
define('NONCE_SALT',       'K)#qX|X,`}%w}=5DK_vW14L!wIy;*.m%1B9cI-%7%c.)>N6lR7a*t {=PQjrSlzB');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
