# Contact Summary Editor

**Customize the contact summary screen.**

Have you ever wanted to rearrange the contact summary screen? Move the most important information to the top? Remove unnecessary stuff? Create a simplified layout for your volunteers and interns but a more robust layout for your fundraising team? This extension will let you do just that.

![Screenshot](/images/editor.png)

![Screenshot](/images/summary.png)

## Requirements

* CiviCRM 5.x
* [Api v4 extension](https://github.com/civicrm/org.civicrm.api4)
* [Shoreditch theme](https://github.com/civicrm/org.civicrm.shoreditch) (does not require the full theme override mode shown above)

## Contribute

Please contribute to the [CiviCRM.org Make-it-Happen campaign](https://civicrm.org/make-it-happen/contact-summary-editor) to support ongoing development of this extension.

## Usage

* Once installed, navigate to **Administer -> Customize Data and Screens -> Contact Summary Layouts** to open the editor.
* Create one or more layouts, dragging the desired blocks from the palette.
* Click the "New Block" button to create a block combining any contact fields you desire (including custom fields).
* New blocks can be added to one or more layouts. Editing a block in use by multiple layouts will affect them all.
* When viewing a contact, the system will display the first layout in the list that applies to a given contact type being displayed **if** the logged-in user belongs to the specified group(s).
* If no layout matches those criteria, the default system layout will be used.

## Integrates with
* [Shoreditch theme](https://github.com/civicrm/org.civicrm.shoreditch) - works with or without full theme override mode.
* [Relationship block extension](https://github.com/eileenmcnaughton/org.wikimedia.relationshipblock) - provides an additional block for your layouts.

## For developers

This extension provides `hook_civicrm_contactSummaryBlocks` to allow other extensions to supply blocks for the layout editor.
It also provides an api (v3 and v4 compatible) to facilitate managing summary layouts.

Hook example:

```php
/**
 * Implements hook_civicrm_contactSummaryBlocks().
 *
 * @link https://github.com/civicrm/org.civicrm.contactsummary
 */
function example_civicrm_contactSummaryBlocks(&$blocks) {
  // Provide our own group for this block to visually distinguish it on the contact summary editor.
  $blocks['example'] = CRM_Utils_Array::value('example', $blocks, [
    'title' => ts('Example Blocks'),
    'icon' => 'fa-rocket',
  ]);
  $blocks['example']['blocks']['example_block'] = [
    'title' => ts('Example Block'),
    'tpl_file' => 'CRM/Example/ExampleBlock.tpl',
    'sample' => [ts('Example field'), ts('Another example field')],
    'edit' => FALSE,
  ];
}
```

To successfully implement this hook your extension needs to provide a block for the contact summary screen that follows the code conventions used in core, namely:

* A page class, e.g. `CRM_Example_Page_Inline_Example`
* A form class, e.g. `CRM_Example_Form_Inline_Example`
* Smarty templates for both
* A pagerun hook to inject the necessary data onto the summary screen at runtime.

For a working example of how to do this, see the [relationship block extension](https://github.com/eileenmcnaughton/org.wikimedia.relationshipblock),
which works well on its own; the only addition needed to add its block to the editor palette was [implementing `hook_civicrm_contactSummaryBlocks`](https://github.com/eileenmcnaughton/org.wikimedia.relationshipblock/pull/14).

-----

This extension licensed under [AGPL-3.0](LICENSE.txt).