<?php
use CRM_Contactsummary_ExtensionUtil as E;

class CRM_Contactsummary_Page_Inline_ProfileBlock extends CRM_Core_Page {

  public function run() {
    $contactId = CRM_Utils_Request::retrieveValue('cid', 'Positive');
    if (!$contactId) {
      // Let's fail silently.
      return;
    }
    self::addProfileBlock($this, $contactId);
    parent::run();
  }

  /**
   * @param CRM_Core_Page $page
   * @param int $contactId
   */
  public static function addProfileBlock(&$page, $contactId) {
  }

}
