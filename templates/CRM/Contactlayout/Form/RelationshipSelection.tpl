<div class="crm-container">
  <div class="crm-section">
    <div class="label">{$form.related_rel.label}</div>
    <div class="content">
      {$form.related_rel.html}
      {$form.related_rel.description}
      <p>If a relationship is selected, we display the information for the contact's
      relation instead.</p>
    </div>
    <div class="clear"></div>
  </div>
  <div class="crm-submit-buttons">
   {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
