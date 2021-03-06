<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * XOOPS Authentification base class
 *
 * @copyright The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package kernel
 * @subpackage auth
 * @since 2.0
 * @author Pierre-Eric MENUET <pemphp@free.fr>
 * @version $Id$
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

/**
 *
 * @package kernel
 * @subpackage auth
 * @description Authentification base class
 * @author Pierre-Eric MENUET <pemphp@free.fr>
 * @copyright copyright (c) 2000-2003 XOOPS.org
 */
class XoopsAuth {
	/**
	 *
	 * @var
	 */
	var $_dao;
	/**
	 *
	 * @var
	 */
	var $_errors;

	/**
	 * Authentication Service constructor
	 */
	public function __Construct( XoopsDatabase $dao = null )
	{
		$this->_dao = $dao;
	}

	/**
	 *
	 * @abstract need to be write in the dervied class
	 */
	public function authenticate()
	{
		return false;
	}

	/**
	 * add an error
	 *
	 * @param string $value error to add
	 * @access public
	 */
	function setErrors( $err_no, $err_str )
	{
		$this->_errors[$err_no] = trim( $err_str );
	}

	/**
	 * return the errors for this object as an array
	 *
	 * @return array an array of errors
	 * @access public
	 */
	function getErrors()
	{
		return $this->_errors;
	}

	/**
	 * return the errors for this object as html
	 *
	 * @return string html listing the errors
	 * @access public
	 */
	function getHtmlErrors()
	{
		global $xoopsConfig;

		$ret = '<br>';
		if ( $xoopsConfig['debug_mode'] == 1 || $xoopsConfig['debug_mode'] == 2 ) {
			if ( !empty( $this->_errors ) ) {
				foreach ( $this->_errors as $errstr ) {
					$ret .= $errstr . '<br/>';
				}
			} else {
				$ret .= _NONE . '<br />';
			}
			$ret .= sprintf( _AUTH_MSG_AUTH_METHOD, $this->auth_method );
		} else {
			$ret .= _US_INCORRECTLOGIN;
		}
		return $ret;
	}
}

?>