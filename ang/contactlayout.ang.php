<?php
// Declare contactlayout angular module

return [
  'js' => [
    'ang/contactlayout.js',
    'ang/contactlayout/*.js',
  ],
  'css' => [
    'ang/contactlayout.css',
  ],
  'partials' => [
    'ang/contactlayout',
  ],
  'bundles' => ['bootstrap3'],
  'basePages' => ['civicrm/admin/contactlayout'],
  'requires' => ['crmUi', 'crmUtil', 'ui.sortable', 'api4', 'dialogService', 'crmProfileUtils'],
];
