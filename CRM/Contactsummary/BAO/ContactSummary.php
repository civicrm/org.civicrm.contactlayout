<?php
use CRM_Contactsummary_ExtensionUtil as E;

class CRM_Contactsummary_BAO_ContactSummary extends CRM_Contactsummary_DAO_ContactSummary {

  /**
   * @param int $cid
   *   Id of contact being displayed.
   * @param int $uid
   *   Contact id of current user.
   *
   * @return array
   */
  public static function getLayout($cid, $uid = NULL) {
    // Mock output for development purposes. WIP.
    $mockLayout = [
      [
        [
          'title' => ts('Email'),
          'tpl_file' => 'CRM/Contact/Page/Inline/Email.tpl',
        ],
        [
          'title' => ts('Website'),
          'tpl_file' => 'CRM/Contact/Page/Inline/Website.tpl',
        ],
        [
          'title' => ts('Constituent Info'),
          'tpl_file' => 'CRM/Contactsummary/Page/Inline/CustomFieldSet.tpl',
          'custom_group_id' => 1,
        ],
      ],
      [
        [
          'title' => ts('Phone'),
          'tpl_file' => 'CRM/Contact/Page/Inline/Phone.tpl',
        ],
        [
          'title' => ts('Address'),
          'tpl_file' => 'CRM/Contactsummary/Page/Inline/AddressBlocks.tpl',
        ],
        [
          'title' => ts('Name and Address'),
          'tpl_file' => 'CRM/Contactsummary/Page/Inline/Profile.tpl',
          'profile_id' => 1,
        ],
      ],
    ];
    return $mockLayout;
  }

  /**
   * @return array
   */
  public static function getAllBlocks() {
    $blocks = [
      'core' => [
        'title' => ts('Predefined Blocks'),
        'blocks' => [],
      ],
      'custom' => [
        'title' => ts('Custom Field Sets'),
        'blocks' => [],
      ],
      'profile' => [
        'title' => ts('Profiles'),
        'blocks' => [],
      ],
    ];

    // Core blocks are not editable
    $blocks['core']['blocks']['ContactInfo'] = [
      'title' => ts('Contact Info'),
      'tpl_file' => 'CRM/Contact/Page/Inline/ContactInfo.tpl',
      'sample' => [ts('Employer'), ts('Job Title'), ts('Nickame'), ts('Source')],
      'edit' => FALSE,
    ];
    $blocks['core']['blocks']['Demographics'] = [
      'title' => ts('Demographics'),
      'tpl_file' => 'CRM/Contact/Page/Inline/Demographics.tpl',
      'sample' => [ts('Gender'), ts('Date of Birth'), ts('Age')],
      'edit' => FALSE,
    ];
    $blocks['core']['blocks']['CommunicationPreferences'] = [
      'title' => ts('Communication Preferences'),
      'tpl_file' => 'CRM/Contact/Page/Inline/CommunicationPreferences.tpl',
      'sample' => [ts('Privacy'), ts('Preferred Method(s)'), ts('Email Format'), ts('Communication Style'), ts('Email Greeting'), ts('Postal Greeting'), ts('Addressee')],
      'edit' => FALSE,
    ];
    $blocks['core']['blocks']['Address'] = [
      'title' => ts('Address'),
      'tpl_file' => 'CRM/Contactsummary/Page/Inline/AddressBlocks.tpl',
      'sample' => [ts('Home Address'), ts('City'), ts('State/Province'), ts('Postal Code')],
      'multiple' => TRUE,
      'edit' => FALSE,
    ];
    $blocks['core']['blocks']['Phone'] = [
      'title' => ts('Phone'),
      'tpl_file' => 'CRM/Contact/Page/Inline/Phone.tpl',
      'sample' => [ts('Home Phone'), ts('Work Phone')],
      'edit' => FALSE,
    ];
    $blocks['core']['blocks']['Email'] = [
      'title' => ts('Email'),
      'tpl_file' => 'CRM/Contact/Page/Inline/Email.tpl',
      'sample' => [ts('Home Email'), ts('Work Email')],
      'edit' => FALSE,
    ];
    $blocks['core']['blocks']['IM'] = [
      'title' => ts('Instant Messenger'),
      'tpl_file' => 'CRM/Contact/Page/Inline/IM.tpl',
      'sample' => [ts('Yahoo'), ts('Skype')],
      'edit' => FALSE,
    ];
    $blocks['core']['blocks']['OpenID'] = [
      'title' => ts('Open ID'),
      'tpl_file' => 'CRM/Contact/Page/Inline/OpenID.tpl',
      'sample' => [ts('User')],
      'edit' => FALSE,
    ];
    $blocks['core']['blocks']['Website'] = [
      'title' => ts('Website'),
      'tpl_file' => 'CRM/Contact/Page/Inline/Website.tpl',
      'sample' => [ts('Facebook'), ts('Linkedin')],
      'edit' => FALSE,
    ];

    $customGroups = civicrm_api3('CustomGroup', 'get', [
      'extends' => ['IN' => ['Contact', 'Individual', 'Household', 'Organization']],
      'style' => 'Inline',
      'is_active' => 1,
      'options' => ['limit' => 0, 'sort' => 'weight'],
      'api.CustomField.get' => [
        'return' => ['label'],
        'is_active' => 1,
        'options' => ['limit' => 10, 'sort' => 'weight'],
      ],
    ]);
    foreach ($customGroups['values'] as $groupId => $group) {
      $blocks['custom']['blocks'][$group['name']] = [
        'title' => $group['title'],
        'tpl_file' => 'CRM/Contactsummary/Page/Inline/CustomFieldSet.tpl',
        'custom_group_id' => $groupId,
        'sample' => CRM_Utils_Array::collect('label', $group['api.CustomField.get']['values']),
        'multiple' => !empty($group['is_multiple']),
        'edit' => 'FIXME',
      ];
    }

    $profiles = civicrm_api3('UFJoin', 'get', [
      'return' => ['uf_group_id.title', 'uf_group_id.name', 'uf_group_id'],
      'options' => ['limit' => 0],
      'module' => 'Contact Summary',
      'api.UFField.get' => [
        'return' => 'label',
        'is_active' => 1,
        'uf_group_id' => '$value.uf_group_id',
        'options' => ['limit' => 10, 'sort' => 'weight'],
      ],
    ]);
    foreach ($profiles['values'] as $profile) {
      $blocks['profile']['blocks'][$profile['uf_group_id.name']] = [
        'title' => $profile['uf_group_id.title'],
        'tpl_file' => 'CRM/Contactsummary/Page/Inline/Profile.tpl',
        'profile_id' => $profile['uf_group_id'],
        'sample' => CRM_Utils_Array::collect('label', $profile['api.UFField.get']['values']),
        'edit' => 'FIXME',
      ];
    }

    $null = NULL;
    CRM_Utils_Hook::singleton()->invoke(['blocks'], $blocks,
      $null, $null, $null, $null, $null, 'civicrm_contactSummaryBlocks'
    );

    return $blocks;
  }

}
