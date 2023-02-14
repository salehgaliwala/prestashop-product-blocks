{*
*  @author    IshiTechnolabs
*  @copyright 2015-2017 IshiTechnolabs. All Rights Reserved.
*  @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*}
{extends file="helpers/form/form.tpl"}

{block name="input"}

    {if $input.name == "ISHIPRODUCTSBLOCK_IMG"}

        {if isset($fields_value[$input.name]) && $fields_value[$input.name] != ''}
            <img src="{$imagePath}{$fields_value[$input.name]}" style="width:100%;max-width:100px;"/><br /><br />
            <a href="{$current}&{$identifier}={$form_id}&token={$token}&deleteConfig={$input.name}">
                <i class="icon icon-trash"></i> {l s='Delete' mod='ishiproductsblock'}
            </a>
            <small>{l s='Do not forget to save the options after delete the image!' mod='ishiproductsblock'}</small>
            <br /><br />
        {/if}

        {$smarty.block.parent}

    {else}

        {$smarty.block.parent}

    {/if}

{/block}