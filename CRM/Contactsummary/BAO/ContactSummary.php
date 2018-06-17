<?php
use CRM_Contactsummary_ExtensionUtil as E;

class CRM_Contactsummary_BAO_ContactSummary extends CRM_Contactsummary_DAO_ContactSummary {

  /**
   * Create a new ContactSummary based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Contactsummary_DAO_ContactSummary|NULL
   *
  public static function create($params) {
    $className = 'CRM_Contactsummary_DAO_ContactSummary';
    $entityName = 'ContactSummary';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

}
