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
 * Private Messages
 *
 * @copyright The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package pm
 * @since 2.4.0
 * @author trabis <lusopoemas@gmail.com>
 * @version $Id$
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

/**
 * PM system preloads
 *
 * @copyright The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @author trabis <lusopoemas@gmail.com>
 */
class PmSystemPreload extends XoopsPreloadItem {
	/**
	 * PmSystemPreload::eventSystemBlocksSystem_blocksUsershow()
	 *
	 * @param mixed $args
	 * @return
	 */
	function eventSystemBlocksSystem_blocksUsershow( $args )
	{
		$args[0] = xoops_getModuleHandler( 'message', 'pm' );
	}
}

?>