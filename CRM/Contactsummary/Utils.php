<?php

class CRM_Contactsummary_Utils {

  public static function getLayout($cid) {
    $mockLayout = [
      'columns' => [
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
            'tpl_file' => 'CRM/Contact/Page/Inline/CustomFieldSet.tpl',
            'block_id' => 1,
          ],
        ],
        [
          [
            'title' => ts('Phone'),
            'tpl_file' => 'CRM/Contact/Page/Inline/Phone.tpl',
          ],
          [
            'title' => ts('Im'),
            'tpl_file' => 'CRM/Contact/Page/Inline/IM.tpl',
          ],
          [
            'title' => ts('OpenID'),
            'tpl_file' => 'CRM/Contact/Page/Inline/OpenID.tpl',
            'collapsible' => TRUE,
            'collapsed' => TRUE,
          ],
        ],
      ],
    ];
    return $mockLayout;
  }

}