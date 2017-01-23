<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemCookieredirect extends JPlugin {
  private $redirectURL = '';
  private $cookieToCheck = '';
  private $tagsToCheck = '';

  public function __construct(&$subject, $config = array()) {
    parent::__construct($subject, $config);

    // get the plugin settings
    $this->cookieToCheck = $this->params->get('cookietocheck', '');
    $this->redirectURL = $this->params->get('redirectrul', '');
    $this->tagsToCheck = $this->params->get('tagstocheck');
  }

  function onBeforeCompileHead() {
    $app = JFactory::getApplication();
    $doc = JFactory::getDocument();
    $db = JFactory::getDbo();

    // check if we shoud get in action:
    // - we must be on the site (not in admin)
    // - it must be an article
    if ($app->isSite() && $app->input->get('view') == 'article') {
      // see if the article is tagged with one the required tags
      $query = $db->getQuery(true);
      $query->select($db->quoteName('content_item_id'))
        ->from($db->quoteName('#__contentitem_tag_map'))
        ->where($db->quoteName('type_alias') . ' = ' . $db->quote('com_content.article'))
        ->where($db->quoteName('content_item_id') . ' = ' . $app->input->get('id'))
        ->where($db->quoteName('tag_id') . ' in (' . $this->tagsToCheck . ')');

      $db->setQuery($query);
      $row = $db->loadAssoc();

      // get the cookie we need to check
      if ($row['content_item_id'] && $this->cookieToCheck) {
        $inputCookie = $app->input->cookie;
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
            $app->redirect($this->redirectURL . $rd);
          }

          return FALSE;
        }
      }
    }

    return TRUE;
  }
}