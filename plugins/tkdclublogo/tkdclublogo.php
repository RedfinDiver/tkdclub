<?php
/**
* @package    Taekwondo Club
* @copyright  Copyright (C) 2017 Markus Moser. All rights reserved.
* @license    GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

class plgQuickiconTkdclublogo extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 *
	 * @since       2.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * This method is called when the Quick Icons module is constructing its set
	 * of icons. You can return an array which defines a single icon and it will
	 * be rendered right after the stock Quick Icons.
	 *
	 * @param  $context  The calling context
	 *
	 * @return array A list of icon definition associative arrays, consisting of the
	 *				 keys link, image, text and access.
	 *
	 * @since       2.5
	 */
	public function onGetIcons($context)
	{
		if ($context != $this->params->get('context', 'mod_quickicon') || !JFactory::getUser()->authorise('core.manage', 'com_tkdclub')) {
			return;
		}

        $document = JFactory::getDocument();
        $document->addStyleSheet('../administrator/components/com_tkdclub/assets/css/tkdclub.css');

		return array(array(
			'link' => 'index.php?option=com_tkdclub',
			'image' => 'tkdclub-grey',
			'text' => JText::_('PLG_QUICKICON_TKDCLUBLOGO'),
			'id' => 'plg_quickicon_tkdclublogo'
		));
	}
}