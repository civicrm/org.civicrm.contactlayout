<?php

/**
 * Relationship Selection Form controller class
 */
class CRM_Contactlayout_Form_RelationshipSelection extends CRM_Core_Form {

  /**
   * Adds the relationship selection field and the save and cancel button.
   */
  public function buildQuickForm() {
    $relationshipOptions = $this->getRelationshipOptions();
    asort($relationshipOptions);

    $this->add(
      'select',
      'related_rel',
      ts('Relationship'),
      $relationshipOptions,
      FALSE,
      [
        'class' => 'crm-select2',
        'placeholder' => ts('- select - '),
      ]
    );

    $this->addButtons([
      [
        'type' => 'upload',
        'name' => ts('Save'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => ts('Cancel'),
      ],
    ]);

    parent::buildQuickForm();
  }

  /**
   * Attaches the selected relationship value to the AJAX response object of the form.
   */
  public function postProcess() {
    $values = $this->exportValues();
    $this->ajaxResponse = [
      'values' => [
        'related_rel' => $values['related_rel'],
      ],
    ];
  }

  /**
   * Accepts a default value for the relationship field.
   *
   * The value can come from a GET request parameter.
   */
  public function setDefaultValues() {
    $defaults = parent::setDefaultValues();

    $defaults['related_rel'] = CRM_Utils_Request::retrieve('related_rel', 'String');;

    return $defaults;
  }

  /**
   * Returns all defined relationships, including both directions.
   *
   * The relationships are returned in a format that can be accepted by select fields.
   * We include both directions of the relationship. Ex: 16_ab "Parent of" and 16_ba
   * "Child Of".
   *
   * @return array
   *   A list of relationship option values.
   */
  private function getRelationshipOptions() {
    $relationshipOptions = [];
    $relationships = civicrm_api3('RelationshipType', 'get', [
      'sequential' => 1,
      'options' => [
        'limit' => 0,
      ],
    ]);

    foreach($relationships['values'] as $relationship) {
      $relationshipOptions[$relationship['id'] . '_ab'] = $relationship['label_a_b'];
      $relationshipOptions[$relationship['id'] . '_ba'] = $relationship['label_b_a'];
    }

    return $relationshipOptions;
  }

}
