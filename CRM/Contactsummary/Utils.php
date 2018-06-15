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
            'tpl_file' => 'CRM/Contactsummary/Page/Inline/CustomFieldSet.tpl',
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
            'title' => ts('Summary Profile 1'),
            'tpl_file' => 'CRM/Contactsummary/Page/Inline/Profile.tpl',
            'block_id' => 16,
          ],
        ],
      ],
    ];
    return $mockLayout;
  }

}
