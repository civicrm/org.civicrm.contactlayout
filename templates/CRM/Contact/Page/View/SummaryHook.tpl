{foreach from=$layoutBlocks item="row"}
  <div class="contact_panel crm-contact-summary-layout-row">
    {foreach from=$row item="column"}
      <div class="crm-contact-summary-layout-col">
        {foreach from=$column item='block'}
          <div class="{if $block.collapsible}crm-collapsible{if $block.collapsed} collapsed{/if}{/if}">
            {if (!empty($block.collapsible) || !empty($block.showTitle))}
              <div class="collapsible-title">
                {$block.title}
              </div>
            {/if}
            <div class="crm-summary-block">
              {include file=$block.tpl_file}
            </div>
          </div>
        {/foreach}
      </div>
    {/foreach}
  </div>
{/foreach}