<?php
/**
 * XOOPS main configuration file
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @version $Id$
 */

if ( !defined( 'XOOPS_MAINFILE_INCLUDED' ) ) {
	define( 'XOOPS_MAINFILE_INCLUDED', 1 );
	// Xoosla Physical Paths
	// Physical path to the Xoosla documents (served) directory WITHOUT trailing slash
	define("XOOPS_ROOT_PATH", "");
	// Physical path to the Xoosla library directory WITHOUT trailing slash
	define("XOOPS_PATH", "");
	// Physical path to the Xoosla datafiles (writable) directory WITHOUT trailing slash
	define("XOOPS_VAR_PATH", "");
	// Alias of Xoosla, for compatibility, temporary solution
	define( 'XOOPS_TRUST_PATH', XOOPS_PATH );
	// URL Association for SSL and Protocol Compatibility
	$http = 'http://';
	if ( !empty( $_SERVER['HTTPS'] ) ) {
		$http = ( $_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://';
	}
	define( 'XOOPS_PROT', $http );
	// XOOPS Virtual Path (URL)
	// Virtual path to your main XOOPS directory WITHOUT trailing slash
	// Example: define('XOOPS_URL', 'http://url_to_xoosls_directory');
	define("XOOPS_URL", "http://");
	// Shall be handled later, don't forget!
	define( 'XOOPS_CHECK_PATH', 0 );
	// Protect against external scripts execution if safe mode is not enabled
	if ( XOOPS_CHECK_PATH && !@ini_get( 'safe_mode' ) ) {
		if ( function_exists( 'debug_backtrace' ) ) {
			$xoopsScriptPath = debug_backtrace();
			if ( !count( $xoopsScriptPath ) ) {
				die( 'Access Denied: This is a restricted file you have tried to access.' );
			}
			$xoopsScriptPath = $xoopsScriptPath[0]['file'];
		} else {
			$xoopsScriptPath = isset( $_SERVER['PATH_TRANSLATED'] ) ? $_SERVER['PATH_TRANSLATED'] : $_SERVER['SCRIPT_FILENAME'];
		}
		if ( DIRECTORY_SEPARATOR != '/' ) {
			// IIS6 may double the \ chars
			$xoopsScriptPath = str_replace( strpos( $xoopsScriptPath, "\\\\", 2 ) ? "\\\\" : DIRECTORY_SEPARATOR, '/', $xoopsScriptPath );
		}
		if ( strcasecmp( substr( $xoopsScriptPath, 0, strlen( XOOPS_ROOT_PATH ) ), str_replace( DIRECTORY_SEPARATOR, '/', XOOPS_ROOT_PATH ) ) ) {
			exit( 'Xoosla path check: Script is not inside XOOPS_ROOT_PATH and cannot run.' );
		}
	}
	// Secure file
	require XOOPS_VAR_PATH . '/data/secure.php';
	define("XOOPS_GROUP_ADMIN", "1");
	define("XOOPS_GROUP_USERS", "2");
	define("XOOPS_GROUP_ANONYMOUS", "3");
	if ( !isset( $xoopsOption['nocommon'] ) && XOOPS_ROOT_PATH != '' ) {
		require XOOPS_ROOT_PATH . '/include/common.php';
	}

}

?>