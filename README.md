# Contact Summary Layout Editor

**Customize the contact summary screen.**

Have you ever wanted to rearrange the contact summary screen? Move the most important information to the top? Remove unnecessary stuff? Create a simplified layout for your volunteers and interns but a more robust layout for your fundraising team? This extension will let you do just that.

![Screenshot](/images/editor.png)

![Screenshot](/images/summary.png)

## Requirements

* CiviCRM 5.x
* [Api v4 extension](https://github.com/civicrm/org.civicrm.api4)
* [Angular Profiles extension](https://github.com/ginkgostreet/org.civicrm.angularprofiles)
* (Optional) Looks best with a bootstrap-based admin theme or the [Shoreditch exension](https://github.com/civicrm/org.civicrm.shoreditch).

## Contribute

Please contribute to the [CiviCRM.org Make-it-Happen campaign](https://civicrm.org/make-it-happen/contact-summary-editor) to support ongoing development of this extension.

## Usage

* Once installed, navigate to **Administer -> Customize Data and Screens -> Contact Summary Layouts** to open the editor.
* Create one or more layouts, dragging the desired blocks from the palette.
* Click the "New Block" button to create a block combining any contact fields you desire (including custom fields).
* Your blocks can be added to one or more layouts. **Note:** Editing the contents of a block will affect all layouts the block appears in.
* The "Show" option can specify a contact type, e.g. if a layout is specifically designed for _Organization_ contacts rather than _Individuals_.
* The "To" option can restrict a layout to be visible to only certain logged-in users (e.g. show one layout to your volunteers and another to your staff).
* When a user views a contact, the first layout in the list which meets the "Show" and "To" criteria will be shown.
* If no layout matches the criteria, the default system layout will be used.

## Integrates with
* [Shoreditch theme](https://github.com/civicrm/org.civicrm.shoreditch) - works with or without full theme override mode.
* [Relationship block extension](https://github.com/eileenmcnaughton/org.wikimedia.relationshipblock) - provides a block for displaying important relationships.
* [Extended Reports extension](https://civicrm.org/extensions/extended-reports) - allows reports to be displayed as blocks on the summary screen.

## For developers

This extension provides `hook_civicrm_contactSummaryBlocks` to allow other extensions to supply blocks for the layout editor.
It also provides an api (v3 and v4 compatible) to facilitate managing summary layouts.

Hook example:

```php
/**
 * Implements hook_civicrm_contactSummaryBlocks().
 *
 * @link https://github.com/civicrm/org.civicrm.contactlayout
 */
function example_civicrm_contactSummaryBlocks(&$blocks) {
  // Register our block with the layout editor.
  $blocks['core']['blocks']['example_block'] = [
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
* Optionally a region hook to inject the template onto the summary screen if you want your block to work even without this extension.

For a working example of how to do this, see the [relationship block extension](https://github.com/eileenmcnaughton/org.wikimedia.relationshipblock),
which works well on its own; the only addition needed to add its block to the editor palette was [implementing `hook_civicrm_contactSummaryBlocks`](https://github.com/eileenmcnaughton/org.wikimedia.relationshipblock/pull/14).

-----

This extension licensed under [AGPL-3.0](LICENSE.txt).
