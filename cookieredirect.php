<?php

/*
 * TE GEBRUIKEN VOOR REDIRECT URL:
 * $this->params->get('redirecturl', '')
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemCookieredirect extends JPlugin {
  var $_initialized = false;

  function onBeforeCompileHead() {
    if ($this->_initialized) {
      return TRUE;
    }

    $app = JFactory::getApplication();
    $doc = JFactory::getDocument();
                
    // exit, if we're on admin or non html pages
    if ($app->isAdmin() || $doc->getType() != 'html' || in_array(JRequest::getString('tmpl'),array('component','raw'))) {
      return TRUE;
    }

		return TRUE;
	}
}