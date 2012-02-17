<?php
// $Id$
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.xoops.org/ http://jp.xoops.org/  http://www.myweb.ne.jp/  //
// Project: The XOOPS Project (http://www.xoops.org/)                        //
// ------------------------------------------------------------------------- //

// RMV-NOTIFY

// FIXME: Do some caching, so we don't retrieve the same category / event
// info many times.


/**
 * Determine if notification is enabled for the selected module.
 *
 * @param  string  $style      Subscription style: 'block' or 'inline'
 * @param  int     $module_id  ID of the module  (default current module)
 * @return bool
 */
function notificationEnabled ($style, $module_id=null)
{
	if (isset($GLOBALS['xoopsModuleConfig']['notification_enabled'])) {
		$status = $GLOBALS['xoopsModuleConfig']['notification_enabled'];
	} else {
		if (!isset($module_id)) {
			return false;
		}
		$module_handler =& xoops_gethandler('module');
		$module =& $module_handler->get($module_id);
		if (!empty($module) && $module->getVar('hasnotification') == 1) {
			$config_handler =& xoops_gethandler('config');
			$config = $config_handler->getConfigsByCat(0,$module_id);
			$status = $config['notification_enabled'];
		} else {
			return false;
		}
	}
	include_once XOOPS_ROOT_PATH . '/include/notification_constants.php';
	if (($style == 'block') && ($status == XOOPS_NOTIFICATION_ENABLEBLOCK || $status == XOOPS_NOTIFICATION_ENABLEBOTH)) {
		return true;
	}
	if (($style == 'inline') && ($status == XOOPS_NOTIFICATION_ENABLEINLINE || $status == XOOPS_NOTIFICATION_ENABLEBOTH)) {
		return true;
	}
	// if ($status != XOOPS_NOTIFICATION_DISABLE) {
	// 		return true;
	// }
	return false;
}

/**
 * Get an associative array of info for a particular notification
 * category in the selected module.  If no category is selected,
 * return an array of info for all categories.
 *
 * @param  string  $name       Category name (default all categories)
 * @param  int     $module_id  ID of the module (default current module)
 * @return mixed
 */
function &notificationCategoryInfo ($category_name='', $module_id=null)
{
	if (!isset($module_id)) {
		global $xoopsModule;
		$module_id = !empty($xoopsModule) ? $xoopsModule->getVar('mid') : 0;
		$module =& $xoopsModule;
	} else {
		$module_handler =& xoops_gethandler('module');
		$module =& $module_handler->get($module_id);
	}
	$not_config =& $module->getInfo('notification');
	if (empty($category_name)) {
		return $not_config['category'];
	}
	foreach ($not_config['category'] as $category) {
		if ($category['name'] == $category_name) {
			return $category;
		}
	}
	$ret = false;
	return $ret;
}

/**
 * Get associative array of info for the category to which comment events
 * belong.
 *
 * @todo This could be more efficient... maybe specify in
 *        $modversion['comments'] the notification category.
 *       This would also serve as a way to enable notification
 *        of comments, and also remove the restriction that
 *        all notification categories must have unique item_name. (TODO)
 *
 * @param  int  $module_id  ID of the module (default current module)
 * @return mixed            Associative array of category info
 */
function &notificationCommentCategoryInfo($module_id=null)
{
	$ret = false;
	$all_categories =& notificationCategoryInfo ('', $module_id);
	if (empty($all_categories)) {
		return $ret;
	}
	foreach ($all_categories as $category) {
		$all_events =& notificationEvents ($category['name'], false, $module_id);
		if (empty($all_events)) {
			continue;
		}
		foreach ($all_events as $event) {
			if ($event['name'] == 'comment') {
				return $category;
			}
		}
	}
	return $ret;
}

// TODO: some way to include or exclude admin-only events...

/**
 * Get an array of info for all events (each event has associative array)
 * in the selected category of the selected module.
 *
 * @param  string  $category_name  Category name
 * @param  bool    $enabled_only   If true, return only enabled events
 * @param  int     $module_id      ID of the module (default current module)
 * @return mixed
 */
function &notificationEvents ($category_name, $enabled_only, $module_id=null)
{
	if (!isset($module_id)) {
		global $xoopsModule;
		$module_id = !empty($xoopsModule) ? $xoopsModule->getVar('mid') : 0;
		$module =& $xoopsModule;
	} else {
		$module_handler =& xoops_gethandler('module');
		$module = $module_handler->get($module_id);
	}
	$not_config = $module->getInfo('notification');
	$config_handler =& xoops_gethandler('config');
	$mod_config = $config_handler->getConfigsByCat(0,$module_id);

	$category = notificationCategoryInfo($category_name, $module_id);

	global $xoopsConfig;
	$event_array = array();

	$override_comment = false;
	$override_commentsubmit = false;
	$override_bookmark = false;
	
	foreach ($not_config['event'] as $event) {
		if ($event['category'] == $category_name) {
			$event['mail_template_dir'] = XOOPS_ROOT_PATH . '/modules/' . $module->getVar('dirname') . '/language/' . $xoopsConfig['language'] . '/mail_template/';
			if (!$enabled_only || notificationEventEnabled ($category, $event, $module)) {
				$event_array[] = $event;
			}
			if ($event['name'] == 'comment') {
				$override_comment = true;
			}
			if ($event['name'] == 'comment_submit') {
				$override_commentsubmit = true;
			}
			if ($event['name'] == 'bookmark') {
				$override_bookmark = true;
			}
		}
	}

	include_once XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/notification.php';

	// Insert comment info if applicable
	
	if ($module->getVar('hascomments')) {
		$com_config = $module->getInfo('comments');
		if (!empty($category['item_name']) && $category['item_name'] == $com_config['itemName']) {
			$mail_template_dir = XOOPS_ROOT_PATH . '/language/' . $xoopsConfig['language'] . '/mail_template/';
			include_once XOOPS_ROOT_PATH . '/include/comment_constants.php';
			$config_handler =& xoops_gethandler('config');
			$com_config = $config_handler->getConfigsByCat(0,$module_id);
			if (!$enabled_only) {
				$insert_comment = true;
				$insert_submit = true;
			} else {
				$insert_comment = false;
				$insert_submit = false;
				switch($com_config['com_rule']) {
				case XOOPS_COMMENT_APPROVENONE:
					// comments disabled, no comment events
					break;
				case XOOPS_COMMENT_APPROVEALL:
					// all comments are automatically approved, no 'submit'
					if (!$override_comment) {
						$insert_comment = true;
					}
					break;
				case XOOPS_COMMENT_APPROVEUSER:
				case XOOPS_COMMENT_APPROVEADMIN:
					// comments first submitted, require later approval
					if (!$override_comment) {
						$insert_comment = true;
					}
					if (!$override_commentsubmit) {
						$insert_submit = true;
					}
					break;
				}
			}
			if ($insert_comment) {
				$event = array ('name'=>'comment', 'category'=>$category['name'], 'title'=>_NOT_COMMENT_NOTIFY, 'caption'=>_NOT_COMMENT_NOTIFYCAP, 'description'=>_NOT_COMMENT_NOTIFYDSC, 'mail_template_dir'=>$mail_template_dir, 'mail_template'=>'comment_notify', 'mail_subject'=>_NOT_COMMENT_NOTIFYSBJ);
				if (!$enabled_only || notificationEventEnabled($category, $event, $module)) {
					$event_array[] = $event;
				}
			}
			if ($insert_submit) {
				$event = array ('name'=>'comment_submit', 'category'=>$category['name'], 'title'=>_NOT_COMMENTSUBMIT_NOTIFY, 'caption'=>_NOT_COMMENTSUBMIT_NOTIFYCAP, 'description'=>_NOT_COMMENTSUBMIT_NOTIFYDSC, 'mail_template_dir'=>$mail_template_dir, 'mail_template'=>'commentsubmit_notify', 'mail_subject'=>_NOT_COMMENTSUBMIT_NOTIFYSBJ, 'admin_only'=>1);
				if (!$enabled_only || notificationEventEnabled($category, $event, $module)) {
					$event_array[] = $event;
				}
			}
				

		}
	}

	// Insert bookmark info if appropriate

	if (!empty($category['allow_bookmark'])) {
		if (!$override_bookmark) {
			$event = array ('name'=>'bookmark', 'category'=>$category['name'], 'title'=>_NOT_BOOKMARK_NOTIFY, 'caption'=>_NOT_BOOKMARK_NOTIFYCAP, 'description'=>_NOT_BOOKMARK_NOTIFYDSC);
			if (!$enabled_only || notificationEventEnabled($category, $event, $module)) {
				$event_array[] = $event;
			}
		}	
	}


	return $event_array;
	
}

/**
 * Determine whether a particular notification event is enabled.
 * Depends on module config options.
 *
 * @todo  Check that this works correctly for comment and other
 *   events which depend on additional config options...
 *
 * @param  array  $category  Category info array
 * @param  array  $event     Event info array
 * @param  object $module    Module
 * @return bool
 **/
function notificationEventEnabled (&$category, &$event, &$module)
{
	$config_handler =& xoops_gethandler('config');
	$mod_config = $config_handler->getConfigsByCat(0,$module->getVar('mid'));

	if (is_array($mod_config['notification_events']) && $mod_config['notification_events'] != array()) {
	    $option_name = notificationGenerateConfig ($category, $event, 'option_name');
	    if (in_array($option_name, $mod_config['notification_events'])) {
	        return true;
	    }
	    $notification_handler =& xoops_gethandler('notification');
	}
	return false;
}


/**
 * Get associative array of info for the selected event in the selected
 * category (for the selected module).
 *
 * @param  string  $category_name  Notification category
 * @param  string  $event_name     Notification event
 * @param  int     $module_id      ID of the module (default current module)
 * @return mixed
 */
function &notificationEventInfo ($category_name, $event_name, $module_id=null)
{
	$all_events = notificationEvents ($category_name, false, $module_id);
	foreach ($all_events as $event) {
		if ($event['name'] == $event_name) {
			return $event;
		}
	}
	$ret = false;
	return $ret;
}


/**
 * Get an array of associative info arrays for subscribable categories
 * for the selected module.
 *
 * @param  int  $module_id  ID of the module
 * @return mixed
 */

function &notificationSubscribableCategoryInfo ($module_id=null)
{
	$all_categories = notificationCategoryInfo ('', $module_id);

	// FIXME: better or more standardized way to do this?
	$script_url = explode('/', $_SERVER['PHP_SELF']);
	$script_name = $script_url[count($script_url)-1];

	$sub_categories = array();

	foreach ($all_categories as $category) {

		// Check the script name

		$subscribe_from = $category['subscribe_from'];
		if (!is_array($subscribe_from)) {
			if ($subscribe_from == '*') {
				$subscribe_from = array($script_name);
				// FIXME: this is just a hack: force a match
			} else {
				$subscribe_from = array($subscribe_from);
			}
		}
		if (!in_array($script_name, $subscribe_from)) {
			continue;
		}	

		// If 'item_name' is missing, automatic match.  Otherwise
		// check if that argument exists...

		if (empty($category['item_name'])) {
			$category['item_name'] = '';
			$category['item_id'] = 0;
			$sub_categories[] = $category;
		} else {
			$item_name = $category['item_name'];
			$id = ($item_name != '' && isset($_GET[$item_name])) ? intval($_GET[$item_name]) : 0;
			if ($id > 0)  {
				$category['item_id'] = $id;
				$sub_categories[] = $category;
			}
		}
	}
	return $sub_categories;

}

/**
 * Generate module config info for a particular category, event pair.
 * The selectable config options are given names depending on the
 * category and event names, and the text depends on the category
 * and event titles.  These are pieced together in this function in
 * case we wish to alter the syntax.
 *
 * @param  array  $category  Array of category info
 * @param  array  $event     Array of event info
 * @param  string $type      The particular name to generate
 * return string
 **/
function notificationGenerateConfig (&$category, &$event, $type)
{
	switch ($type) {
	case 'option_value':
	case 'name':
		return 'notify:' . $category['name'] . '-' . $event['name'];
		break;
	case 'option_name':
		return $category['name'] . '-' . $event['name'];
		break;
	default:
		return false;
		break;
	}
}
?>