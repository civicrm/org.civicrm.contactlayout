<?php
use CRM_Contactlayout_ExtensionUtil as E;

class CRM_Contactlayout_Page_Angular extends CRM_Core_Page {

  public function run() {
    $contactEditOptions = CRM_Core_OptionGroup::values('contact_edit_options', TRUE, FALSE, FALSE, NULL, 'name');
    $contactEditPrefs = \Civi\Api4\Setting::get(FALSE)
      ->addSelect('contact_edit_options')
      ->execute()->first()['value'];

    Civi::resources()->addVars(E::SHORT_NAME, [
      'layouts' => (array) civicrm_api4('ContactLayout', 'get', ['orderBy' => ['weight' => 'ASC']]),
      'blocks' => (array) civicrm_api4('ContactLayout', 'getBlocks'),
      'tabs' => (array) civicrm_api4('ContactLayout', 'getTabs'),
      'contactTypes' => (array) civicrm_api4('ContactType', 'get', [
        'where' => [['is_active', '=', 1]],
        'orderBy' => ['label' => 'ASC'],
      ]),
      'groups' => (array) civicrm_api4('Group', 'get', [
        'select' => ['name', 'title', 'description'],
        'where' => [['is_hidden', '=', 0], ['is_active', '=', 1], ['saved_search_id', 'IS NULL']],
      ]),
      'relationshipTypes' => (array) civicrm_api4('RelationshipType', 'get', ['where' => [['is_active', '=', TRUE]]]),
      'contactEditOptions' => $contactEditOptions,
      'systemDefaultsEnabled' => array_intersect($contactEditOptions, $contactEditPrefs),
    ]);

    // For editing profile blocks
    CRM_UF_Page_ProfileEditor::registerProfileScripts();

    // Bootstrap Angular and set page name.
    $loader = new Civi\Angular\AngularLoader();
    $loader->setPageName('civicrm/admin/contactlayout');
    $loader->load();

    parent::run();
  }

}
