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
 * TextSanitizer extension
 *
 * @copyright The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package class
 * @subpackage textsanitizer
 * @since 2.3.0
 * @author Taiwen Jiang <phppp@users.sourceforge.net>
 * @version $Id$
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );

/**
 * MytsWiki
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2012
 * @version $Id$
 * @access public
 */
class MytsWiki extends MyTextSanitizerExtension {
	/**
	 * MytsWiki::encode()
	 *
	 * @param mixed $textarea_id
	 * @return
	 */
	public function encode( $textarea_id )
	{
		$config = parent::loadConfig( dirname( __FILE__ ) );
		$code = "<img src='{$this->image_path}/wiki.gif' alt='" . _XOOPS_FORM_ALTWIKI . "' onclick='xoopsCodeWiki(\"{$textarea_id}\",\"" . htmlspecialchars( _XOOPS_FORM_ENTERWIKITERM, ENT_QUOTES ) . "\");'  onmouseover='style.cursor=\"hand\"'/>&nbsp;";
		$javascript = <<<EOH
            function xoopsCodeWiki(id, enterWikiPhrase){
                if (enterWikiPhrase == null) {
                    enterWikiPhrase = "Enter the word to be linked to Wiki:";
                }
                var selection = xoopsGetSelect(id);
                if (selection.length > 0) {
                    var text = selection;
                }else {
                    var text = prompt(enterWikiPhrase, "");
                }
                var domobj = xoopsGetElementById(id);
                if ( text != null && text != "" ) {
                    var result = "[[" + text + "]]";
                    xoopsInsertText(domobj, result);
                }
                domobj.focus();
            }
EOH;
		return array( $code ,
			$javascript );
	}

	/**
	 * MytsWiki::load()
	 *
	 * @param MyTextSanitizer $ts
	 * @return
	 */
	public function load( MyTextSanitizer &$ts )
	{
		$ts->patterns[] = '/\[\[([^\]]*)\]\]/esU';
		$ts->replacements[] = __CLASS__ . '::decode( "\\1" )';
	}

	/**
	 * MytsWiki::decode()
	 *
	 * @param mixed $text
	 * @return
	 */
	function decode( $text )
	{
		$config = parent::loadConfig( dirname( __FILE__ ) );
		if ( empty( $text ) || empty( $config['link'] ) ) {
			return $text;
		}
		$charset = !empty( $config['charset'] ) ? $config['charset'] : 'UTF-8';
		$ret = '<a href="' . sprintf( $config['link'], urlencode( XoopsLocal::convert_encoding( $text, $charset ) ) ) . '" rel="external" title="">'.$text.'</a>';
		return $ret;
	}
}

?>