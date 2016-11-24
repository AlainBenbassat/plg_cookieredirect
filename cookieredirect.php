<?php

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

class plgSystemCookieredirect extends JPlugin {
  private $redirectURL = '';
  private $cookieToCheck = '';
  private $categoryToCheck = '';

  public function __construct(&$subject, $config = array()) {
    parent::__construct($subject, $config);

    // get the plugin settings
    $this->cookieToCheck = $this->params->get('cookietocheck', '');
    $this->redirectURL = $this->params->get('redirectrul', '');
    $this->categoryToCheck = $this->params->get('categorytocheck', '');
  }

  function onBeforeCompileHead() {
    $app = JFactory::getApplication();
    $doc = JFactory::getDocument();
    $db = JFactory::getDbo();

    // check if we shoud get in action:
    // - we must be on the site (not in admin)
    // - it must be an article
    if ($app->isSite() && $app->input->get('view') == 'article') {
      // get the category ID of the article
      $catID = $app->input->getInt('catid');

      // check if one of its parent categories = Huisarts Nu archief
      $inValidCategory = FALSE;
      if ($catID > 0) {
        do {
          // get the category details
          $query = $db->getQuery(true);
          $query->select($db->quoteName(array('id', 'title', 'parent_id')))
            ->from($db->quoteName('#__categories'))
            ->where($db->quoteName('id') . ' = ' . $catID)
            ->where($db->quoteName('extension') . ' = ' . $db->quote('com_content'));

          $db->setQuery($query);
          $row = $db->loadAssoc();

          // check the category title
          if ($row['title'] == $this->categoryToCheck) {
            $inValidCategory = TRUE;
          }
          else {
            // not the category we need, continue with its parent
            $catID = $row['parent_id'];
          }
        } while ($catID != 0 && $inValidCategory == FALSE);
      }

      if ($inValidCategory == FALSE) {
        // we're not in the category we need, so just quit
        return TRUE;
      }

      // get the cookie we need to check
      if ($this->cookieToCheck) {
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