{foreach from=$layoutBlocks item="row"}
  <div class="contact_panel">
    {foreach from=$row item="column" key="columnNo"}
      <div {if count($row) > 1}class="contactCard{if $columnNo}Right{else}Left{/if}"{/if}>
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