<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemCookieredirect extends JPlugin {
  private $redirectURL = '';
  private $cookieToCheck = '';

  public function __construct(&$subject, $config = array()) {
    parent::__construct($subject, $config);

    // get the plugin settings
    $this->cookieToCheck = $this->params->get('cookietocheck', '');
    $this->redirectURL = $this->params->get('redirectrul', '');
  }

  function onBeforeCompileHead() {
    $app = JFactory::getApplication();
    $doc = JFactory::getDocument();

    // check if we shoud get in action:
    // - we must be on the site (not in admin)
    // - the user must not be logged in
    if ($app->isSite()) {
      // get the cookie we need to check
      if ($this->cookieToCheck) {
        $inputCookie = JFactory::getApplication()->input->cookie;
        $cookieValue = $inputCookie->get($this->cookieToCheck, '');

        if ($cookieValue == 1) {
          // OK, the cookie is set, we can continue
          return TRUE;
        }
        else {
          // the cookie is not set, redirect to the specified URL
          if ($this->redirectURL) {
            // add the current URL as a query parameter "rd"
            $rd = "&rd=" . urlencode(JURI::current());
            JFactory::getApplication()->redirect($this->redirectURL . $rd);
          }

          return FALSE;
        }
      }
    }

    return TRUE;
  }
}