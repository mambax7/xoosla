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
 * XOOPS form element
 *
 * @copyright The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license GNU GPL 2 (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 * @package kernel
 * @subpackage form
 * @since 2.0.0
 * @author Kazumi Ono (AKA onokazu) http://www.myweb.ne.jp/, http://jp.xoops.org/
 * @version $Id$
 */
defined( 'XOOPS_ROOT_PATH' ) or die( 'Restricted access' );
// xoops_load( 'XoopsFormElement' );
/**
 * A textarea
 */
class XoopsFormTextArea extends XoopsFormElement {
	/**
	 * number of columns
	 *
	 * @var int
	 * @access private
	 */
	private $_cols;

	/**
	 * number of rows
	 *
	 * @var int
	 * @access private
	 */
	private $_rows;

	/**
	 * initial content
	 *
	 * @var string
	 * @access private
	 */
	private $_value;

	/**
	 * Constuctor
	 *
	 * @param string $caption caption
	 * @param string $name name
	 * @param string $value initial content
	 * @param int $rows number of rows
	 * @param int $cols number of columns
	 */
	public function __Construct( $caption, $name, $value = '', $rows = 5, $cols = 50 )
	{
		$this->setCaption( $caption );
		$this->setName( $name );
		$this->_rows = intval( $rows );
		$this->_cols = intval( $cols );
		$this->setValue( $value );
	}

	/**
	 * get number of rows
	 *
	 * @return int
	 */
	public function getRows()
	{
		return $this->_rows;
	}

	/**
	 * Get number of columns
	 *
	 * @return int
	 */
	public function getCols()
	{
		return $this->_cols;
	}

	/**
	 * Get initial content
	 *
	 * @param bool $encode To sanitizer the text? Default value should be "true"; however we have to set "false" for backward compat
	 * @return string
	 */
	public function getValue( $encode = false )
	{
		return $encode ? htmlspecialchars( $this->_value ) : $this->_value;
	}

	/**
	 * Set initial content
	 *
	 * @param  $value string
	 */
	public function setValue( $value )
	{
		$this->_value = $value;
	}

	/**
	 * prepare HTML for output
	 *
	 * @return sting HTML
	 */
	public function render()
	{
		return '<textarea name="' . $this->getName() . '" id="' . $this->getName() . '"  title="' . $this->getTitle() . '" rows="' . $this->getRows() . '" cols="' . $this->getCols() . '"' . $this->getExtra() . '>' . $this->getValue() . '</textarea>';
	}
}

?>