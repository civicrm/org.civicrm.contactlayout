<?php

/**
 * Class CRM_Contactlayout_Helper_ProfileRelatedContact.
 */
class CRM_Contactlayout_Helper_ProfileRelatedContact {

  /**
   * Returns the contact that has the related relationship with the passed contact.
   *
   * The first related contact is simply returned even if there are more than
   * one contact that meets the relationship criteria with the passed in contact.
   *
   * @param int $contactId
   *   Contact to get related contact for.
   * @param string $relatedRelationship
   *   String defining relationship to check for with passed contact
   *   e.g `16_ab` means relationship type id 16 and relationship direction is ab.
   *
   * @return int|null
   *   The first contact matching the criteria.
   */
  public static function get($contactId, $relatedRelationship) {
    list($relationshipTypeId, $direction) = explode('_', $relatedRelationship);
    if (empty($relationshipTypeId) || empty($direction)) {
      return NULL;
    }

    $isAToB = $direction == 'ab';
    $relationshipTable = CRM_Contact_BAO_Relationship::getTableName();
    $relationshipTypeTable = CRM_Contact_BAO_RelationshipType::getTableName();
    $contactTable = CRM_Contact_BAO_Contact::getTableName();
    $relationshipJoinCondition = $isAToB ? 'ON r.contact_id_a = c.id' : 'ON r.contact_id_b = c.id';
    $contactCondition = $isAToB ? 'AND r.contact_id_b = %1' : 'AND r.contact_id_a = %1';

    $query = "
      SELECT c.id
      FROM {$contactTable } c
      INNER JOIN {$relationshipTable} r
       {$relationshipJoinCondition}
      INNER JOIN {$relationshipTypeTable} rt
        ON rt.id = r.relationship_type_id
      WHERE r.is_active = 1 AND rt.is_active = 1
      AND rt.id = %2
      {$contactCondition}
      AND (r.start_date IS NULL OR r.start_date <= %3)
      AND (r.end_date IS NULL OR r.end_date >= %3)
      LIMIT 1;
    ";

    $params = [
      1 => [$contactId, 'Integer'],
      2 => [$relationshipTypeId, 'Integer'],
      3 => [date('Y-m-d'), 'String'],
    ];

    $result = CRM_Core_DAO::executeQuery($query, $params);
    $contact = [];

    while ($result->fetch()) {
      $contact = [
        'id' => $result->id,
      ];
    }

    return !empty($contact['id']) ? $contact['id'] : NULL;
  }

}
