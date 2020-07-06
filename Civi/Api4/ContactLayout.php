<?php

namespace Civi\Api4;

use Civi\Api4\Action\ContactLayout\Replace;
use Civi\Api4\Action\ContactLayout\GetBlocks;
use Civi\Api4\Action\ContactLayout\GetTabs;

/**
 * ContactLayout entity - visual layouts for the contact summary screen.
 *
 */
class ContactLayout extends Generic\DAOEntity {

  /**
   * @return \Civi\Api4\Action\ContactLayout\GetBlocks
   */
  public static function getBlocks() {
    return new GetBlocks(__CLASS__, __FUNCTION__, ['CRM_Contactlayout_BAO_ContactLayout', 'getAllBlocks']);
  }

  /**
   * @return \Civi\Api4\Action\ContactLayout\GetTabs
   */
  public static function getTabs() {
    return new GetTabs(__CLASS__, __FUNCTION__, ['CRM_Contactlayout_BAO_ContactLayout', 'getAllTabs']);
  }

  /**
   * @return \Civi\Api4\Action\ContactLayout\Replace
   */
  public static function replace() {
    return new Replace(__CLASS__, __FUNCTION__);
  }

  public static function permissions() {
    return [
      'get' => ['access CiviCRM'],
    ];
  }

}
