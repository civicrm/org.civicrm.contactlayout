<?php

use CRM_Contactlayout_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Contactlayout_Form_Inline_ProfileBlock extends CRM_Profile_Form_Edit {

  /**
   * Form for editing profile blocks
   */
  public function preProcess() {
    if (!empty($_GET['cid'])) {
      $this->set('id', $_GET['cid']);
    }
    parent::preProcess();
    // Suppress profile status messages like the double-opt-in warning
    CRM_Core_Session::singleton()->getStatus(TRUE);
  }

  public function buildQuickForm() {
    parent::buildQuickForm();
    $buttons = array(
      array(
        'type' => 'upload',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ),
      array(
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ),
    );
    $this->addButtons($buttons);
    $this->assign('help_pre', CRM_Utils_Array::value('help_pre', $this->_ufGroup));
    $this->assign('help_post', CRM_Utils_Array::value('help_post', $this->_ufGroup));

    // Special handling for contact id element
    if ($this->elementExists('id')) {
      $cidElement = $this->getElement('id');
      $cidElement->freeze();
      $cidElement->setValue($this->_id);
    }

    // Special handling for employer
    if ($this->elementExists('current_employer')) {
      $employerField = $this->getElement('current_employer');
      $employerField = $this->addEntityRef('current_employer', $employerField->getLabel(), [
        'create' => TRUE,
        'multiple' => TRUE,
        'api' => ['params' => ['contact_type' => 'Organization']],
      ]);
      $employers = self::getEmployers($this->_id);
      $employerField->setValue(array_column($employers, 'contact_id'));
    }
  }

  /**
   * Save profiles
   *
   * @throws CiviCRM_API3_Exception
   */
  public function postProcess() {
    $values = $this->exportValues();
    // Ignore value from contact id field
    unset($values['id']);
    $values['contact_id'] = $cid = $this->_id;
    $values['profile_id'] = $this->_gid;
    $this->processEmployer($values);
    $result = civicrm_api3('Profile', 'submit', $values);

    // These are normally performed by CRM_Contact_Form_Inline postprocessing but this form doesn't inherit from that class.
    CRM_Core_BAO_Log::register($cid,
      'civicrm_contact',
      $cid
    );
    $this->ajaxResponse = array_merge(
      CRM_Contact_Form_Inline::renderFooter($cid),
      $this->ajaxResponse,
      CRM_Contact_Form_Inline_Lock::getResponse($cid)
    );
    // Refresh tabs affected by this profile
    foreach (['tag', 'group', 'note'] as $tab) {
      if (isset($values[$tab])) {
        $this->ajaxResponse['updateTabs']["#tab_$tab"] = CRM_Contact_BAO_Contact::getCountComponent($tab, $this->_id);
      }
    }
  }

  /**
   * @param int $cid
   * @return array
   * @throws \API_Exception
   */
  public static function getEmployers($cid) {
    $relationships = Civi\Api4\Relationship::get()
      ->setSelect(['contact_id_b', 'contact_b.display_name'])
      ->setCheckPermissions(FALSE)
      ->addWhere('is_active', '=', '1')
      ->addWhere('contact_id_a', '=', $cid)
      ->addWhere('relationship_type.name_a_b', '=', 'Employee of')
      ->addClause('OR', ['start_date', 'IS NULL'], ['start_date', '<=', 'now'])
      ->addClause('OR', ['end_date', 'IS NULL'], ['end_date', '>', 'now'])
      ->execute();
    $results = [];
    foreach ($relationships as $relationship) {
      $results[] = [
        'id' => $relationship['id'],
        'contact_id' => $relationship['contact_id_b'],
        'display_name' => $relationship['contact_b']['display_name'],
      ];
    }
    return $results;
  }

  /**
   * Handles setting one or more employers for a contact.
   *
   * @param $values
   * @throws \API_Exception
   */
  public function processEmployer(&$values) {
    if (isset($values['current_employer'])) {
      if (is_string($values['current_employer'])) {
        $values['current_employer'] = $values['current_employer'] ? explode(',', $values['current_employer']) : [];
      }
      $existingEmployers = array_column(self::getEmployers($this->_id), 'contact_id', 'id');
      foreach ($existingEmployers as $id => $employer) {
        if (!in_array($employer, $values['current_employer'])) {
          Civi\Api4\Relationship::update()
            ->addWhere('id', '=', $id)
            ->setCheckPermissions(FALSE)
            ->addValue('is_active', '0')
            ->execute();
        }
      }
      $employerRelationshipType = Civi\Api4\RelationshipType::get()
        ->setSelect(["id"])
        ->addWhere("name_a_b", "=", "Employee of")
        ->execute()
        ->first()['id'];
      foreach (array_values($values['current_employer']) as $i => $employer) {
        if (!in_array($employer, $existingEmployers)) {
          Civi\Api4\Relationship::create()
            ->setCheckPermissions(FALSE)
            ->addValue('relationship_type_id', $employerRelationshipType)
            ->addValue('contact_id_a', $this->_id)
            ->addValue('contact_id_b', $employer)
            ->execute();
        }
        // Set first org as "current employer" since CiviCRM only allows one
        if (!$i) {
          CRM_Contact_BAO_Contact_Utils::setCurrentEmployer([$this->_id => $employer]);
        }
      }
      // Refresh relationship tab
      $this->ajaxResponse['updateTabs']['#tab_rel'] = CRM_Contact_BAO_Contact::getCountComponent('rel', $this->_id);
      unset($values['current_employer']);
    }
  }

}
