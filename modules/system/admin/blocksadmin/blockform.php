<?php
/**
 * Xoosla
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 *
 * @copyright The Xoosla Project http://sourceforge.net/projects/xoosla/
 * @license GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package blockform.php
 * @since 1.0.0.0
 * @author John Neill <zaquria@xoosla.com>
 * @version blockform.php 00 27/02/2012 04:47 Catzwolf $Id:
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

$form = new XoopsThemeForm( $block['form_title'], 'blockform', 'admin.php', 'post', true );
if ( isset( $block['name'] ) ) {
	$form->addElement( new XoopsFormLabel( _AM_NAME, $block['name'] ) );
}
$form->addElement( new XoopsFormSelectGroup( _AM_GROUP, 'bgroups', true, $block['groups'], 5, true ) );

$side_select = new XoopsFormSelect( _AM_BLKTYPE, 'bside', $block['side'] );
$side_select->addOptionArray( array( 0 => _AM_SBLEFT, 1 => _AM_SBRIGHT, 3 => _AM_CBLEFT, 4 => _AM_CBRIGHT, 5 => _AM_CBCENTER, 7 => _AM_CBBOTTOMLEFT, 8 => _AM_CBBOTTOMRIGHT, 9 => _AM_CBBOTTOM, ) );
$form->addElement( $side_select );

$mod_select = new XoopsFormSelect( _AM_VISIBLEIN, 'bmodule', $block['modules'], 5, true );
$module_handler = xoops_gethandler( 'module' );
$criteria = new CriteriaCompo( new Criteria( 'hasmain', 1 ) );
$criteria->add( new Criteria( 'isactive', 1 ) );

$display_list_spec[ - 1] = _AM_TOPONLY;
$display_list_spec[ - 2] = _AM_ALLPAGES;
$display_list_spec[ - 3] = _AM_UNASSIGNED;

$module_list = $module_handler->getList( $criteria );
$module_list = $display_list_spec + $module_list;
foreach ( $module_list as $k => $v ) {
	$m_list[$k] = $v;
}
$mod_select->addOptionArray( $module_list );
$form->addElement( $mod_select );

$form->addElement( new XoopsFormText( _AM_NAME, 'bname', 50, 255, $block['name'] ), true );
$form->addElement( new XoopsFormText( _AM_TITLE, 'btitle', 50, 255, $block['title'] ), false );

if ( $block['is_custom'] ) {
	$textarea = new XoopsFormDhtmlTextArea( _AM_CONTENT, 'bcontent', $block['content'], 15, 70 );
	$textarea->setDescription( '<span style="font-size:x-small;font-weight:bold;">' . _AM_USEFULTAGS . '</span><br /><span style="font-size:x-small;font-weight:normal;">' . sprintf( _AM_BLOCKTAG1, '{X_SITEURL}', XOOPS_URL . '/' ) . '</span>' );
	$textarea->doHtml = true;
	$form->addElement( $textarea, true );

	$description = new XoopsFormTextArea( _AM_DESCRIPTION, 'bdescription', $block['description'], 4, 20 );
	// $description->setDescription(  _AM_DESCRIPTION_DESC );
	$description->doHtml = false;
	$form->addElement( $description, true );

	$ctype_select = new XoopsFormSelect( _AM_CTYPE, 'bctype', $block['ctype'] );
	$ctype_select->addOptionArray( array( 'H' => _AM_HTML, 'P' => _AM_PHP, 'S' => _AM_AFWSMILE, 'T' => _AM_AFNOSMILE ) );
	$form->addElement( $ctype_select );
} else {
	if ( $block['template'] != '' ) {
		$tplfile_handler = xoops_gethandler( 'tplfile' );
		$btemplate = $tplfile_handler->find( $GLOBALS['xoopsConfig']['template_set'], 'block', $block['bid'] );
		if ( count( $btemplate ) > 0 ) {
			$form->addElement( new XoopsFormLabel( _AM_CONTENT, '<a href="' . XOOPS_URL . '/modules/system/admin.php?fct=tplsets&amp;op=edittpl&amp;id=' . $btemplate[0]->getVar( 'tpl_id' ) . '">' . _AM_EDITTPL . '</a>' ) );
		} else {
			$btemplate2 = $tplfile_handler->find( 'default', 'block', $block['bid'] );
			if ( count( $btemplate2 ) > 0 ) {
				$form->addElement( new XoopsFormLabel( _AM_CONTENT, '<a href="' . XOOPS_URL . '/modules/system/admin.php?fct=tplsets&amp;op=edittpl&amp;id=' . $btemplate2[0]->getVar( 'tpl_id' ) . '" rel="external">' . _AM_EDITTPL . '</a>' ) );
			}
		}
	}
	$form->addElement( new XoopsFormLabel( _AM_DESCRIPTION, $block['description'] ) );
	if ( $block['edit_form'] != false ) {
		$form->addElement( new XoopsFormLabel( _AM_OPTIONS, $block['edit_form'] ) );
	}
	$form->addElement( new XoopsFormHidden( 'bdescription', $block['description'] ) );
}

$form->addElement( new XoopsFormText( _AM_WEIGHT, 'bweight', 2, 5, $block['weight'] ) );
$form->addElement( new XoopsFormRadioYN( _AM_VISIBLE, 'bvisible', $block['visible'] ) );

$cache_select = new XoopsFormSelect( _AM_BCACHETIME, 'bcachetime', $block['cachetime'] );
$cache_select->addOptionArray( array( '0' => _NOCACHE, '30' => sprintf( _SECONDS, 30 ), '60' => _MINUTE, '300' => sprintf( _MINUTES, 5 ), '1800' => sprintf( _MINUTES, 30 ), '3600' => _HOUR, '18000' => sprintf( _HOURS, 5 ), '86400' => _DAY, '259200' => sprintf( _DAYS, 3 ), '604800' => _WEEK, '2592000' => _MONTH ) );
$form->addElement( $cache_select );

$form->addElement( new XoopsFormHidden( 'op', $block['op'] ) );
$form->addElement( new XoopsFormHidden( 'fct', 'blocksadmin' ) );
if ( isset( $block['bid'] ) ) {
	$form->addElement( new XoopsFormHidden( 'bid', $block['bid'] ) );
}
$button_tray = new XoopsFormElementTray( '', '&nbsp;' );
if ( $block['is_custom'] ) {
	$button_tray->addElement( new XoopsFormButton( '', 'previewblock', _PREVIEW, "submit" ) );
}
$button_tray->addElement( new XoopsFormButton( '', 'submitblock', _SUBMIT, "submit" ) );
$form->addElement( $button_tray );

?>