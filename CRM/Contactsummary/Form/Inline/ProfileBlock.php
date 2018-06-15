<?php

use CRM_Contactsummary_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Contactsummary_Form_Inline_ProfileBlock extends CRM_Contact_Form_Inline {

  /**
   * Form for editing key profiles
   */
  public function buildQuickForm() {

    parent::buildQuickForm();
  }

  /**
   * Save profiles
   *
   * @throws \CiviCRM_API3_Exception
   */
  public function postProcess() {
    $values = $this->exportValues();

    $this->log();
    $this->response();
  }

  /**
   * Get existing profiles for form
   */
  public function setDefaultValues() {
    $defaults = [];
    return $defaults;
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }

}
