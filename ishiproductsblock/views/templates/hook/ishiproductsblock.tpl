{*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2017 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div id="ishiproductsblock" class="container clearfix">
	<div class="ishiproductsblock-container">
		<p class="sub-title">{l s='Something About' mod='ishiproductsblock'}</p>
		<h2 class="home-title">{l s='Trending Products' mod='ishiproductsblock'}</h2>
		
		<ul id="ishiproductstab" class="nav nav-tabs clearfix">
			{$count=0}
			{foreach from=$ishiproductblock item=tab name=Tab}
				<li class="nav-item {if $smarty.foreach.Tab.first}first_item{elseif $smarty.foreach.Tab.last}last_item{else}{/if}">
					<a class="nav-link {if $smarty.foreach.Tab.first}active{/if}" href="#{$tab.id}-block" data-toggle="tab">{$tab.name}</a>
				</li>
				{$count= $count+1}
			{/foreach}
		</ul>
		{if !empty($bannerimg)}
			<div class="title-block col-xl-6 col-lg-12">
				<a href="#"><img src="{$imagePath}{$bannerimg}"></a>
			</div>
		{/if}
		<div class="product_content {if !empty($bannerimg)}col-xl-6 col-lg-12{/if}">
			<div class="tab-content">
				{foreach from=$ishiproductblock item=tab name=Tab}
					<div id="{$tab.id}-block" class="tab-pane {if $smarty.foreach.Tab.first}active{/if}">
						{if isset($tab.productInfo) && $tab.productInfo}
							<div class="block_content row">
								<div id="ishi-{$tab.id}" class="owl-carousel">
									{$counter = 1}
									{$lastproduct = {count($tab.productInfo)}}
									{foreach from=$tab.productInfo item=product name=Tab key="position"}
										{if $counter%$productrows ==1 && $productrows != 1} 
											<div class="multilevel-item">
										{/if}
										<div class="item" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
											{include file="catalog/_partials/miniatures/product-slider.tpl" product=$product imgchange=$imgchange position=$position}
										</div>
										{if ($counter%$productrows == 0 || $counter == $lastproduct) && $productrows != 1} 
											</div>
										{/if}
										{$counter = $counter+1}
									{/foreach}
								</div>
							</div>
						{else}
							<div class="alert alert-info">{l s='No relavent products found at this time.Please check again later!!' mod='ishiproductsblock'}
							</div>
						{/if}
					</div>
				{/foreach}
			</div>
		</div>
	</div>
</div>