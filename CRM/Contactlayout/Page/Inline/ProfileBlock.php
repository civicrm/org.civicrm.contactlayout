<?php
use CRM_Contactlayout_ExtensionUtil as E;

class CRM_Contactlayout_Page_Inline_ProfileBlock extends CRM_Core_Page {

  public function run() {
    $contactId = CRM_Utils_Request::retrieveValue('cid', 'Positive', NULL, TRUE);
    $profileId = CRM_Utils_Request::retrieveValue('gid', 'Positive', NULL, TRUE);

    $this->assign('contactId', $contactId);
    $this->assign('profileBlock', self::getProfileBlock($profileId, $contactId));
    $this->assign('block', ['profile_id' => $profileId]);

    CRM_Contact_Page_View::checkUserPermission($this, $contactId);
    parent::run();
  }

  /**
   * @param int $profileId
   * @param int $contactId
   * @return array
   */
  public static function getProfileBlock($profileId, $contactId) {
    $values = [];
    $fields = CRM_Core_BAO_UFGroup::getFields($profileId, FALSE, CRM_Core_Action::VIEW, NULL, NULL, FALSE, NULL, TRUE);
    foreach ($fields as $name => $field) {
      // eliminate all formatting fields
      if (CRM_Utils_Array::value('field_type', $field) == 'Formatting') {
        unset($fields[$name]);
      }
    }
    CRM_Core_BAO_UFGroup::getValues($contactId, $fields, $values, FALSE);
    $result = [];
    foreach ($fields as $name => $field) {
      // Special handling for employer field
      if ($name == 'current_employer') {
        $employers = [];
        foreach (CRM_Contactlayout_Form_Inline_ProfileBlock::getEmployers($contactId) as $employer) {
          $org = $employer['display_name'];
          if (CRM_Contact_BAO_Contact_Permission::allow($employer['contact_id'])) {
            $org = '<a href="' . CRM_Utils_System::url('civicrm/contact/view', ['reset' => 1, 'cid' => $employer['contact_id']]) . '" title="' . E::ts('view employer') . '">' . $org . '</a>';
          }
          $employers[] = $org;
        }
        $values[$field['title']] = implode(', ', $employers);
      }
      $result[] = [
        'name' => $name,
        'value' => CRM_Utils_Array::value($field['title'], $values),
        'label' => $field['title'],
      ];
    }
    return $result;
  }

}
