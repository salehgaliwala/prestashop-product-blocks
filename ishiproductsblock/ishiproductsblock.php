<?php
/**
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
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Adapter\BestSales\BestSalesProductSearchProvider;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class IshiProductsBlock extends Module implements WidgetInterface
{
    private $templateFile;
    public function __construct()
    {
        $this->name = 'ishiproductsblock';
        $this->version = '1.0.0';
        $this->author = 'Ishi Technolabs';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array(
            'min' => '1.7.0.0',
            'max' => _PS_VERSION_,
        );
        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Ishi Products Block');
        $this->description = $this->l('Adds product tab category to your store.');
        $this->templateFile = 'module:ishiproductsblock/views/templates/hook/ishiproductsblock.tpl';
    }

    public function install()
    {
        $this->_clearCache('*');

           return parent::install()
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_NEW', 1)
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_FEATURED', 1)
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_BEST', 1)
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_SPECIAL', 0)
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_IMGCHANGE', 1)
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_NBR', 10)
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_ROWS', 2)
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_COLUMNSMOBILE', 2)
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_COLUMNSTABLET', 2)
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_COLUMNSLAPTOP', 3)
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_COLUMNSDESKTOP', 4)
            && Configuration::updateValue('ISHIPRODUCTSBLOCK_IMG', '')
            && $this->registerHook('actionProductAdd')
            && $this->registerHook('actionProductUpdate')
            && $this->registerHook('actionProductDelete')
            && $this->registerHook('categoryUpdate')
            && $this->registerHook('actionOrderStatusPostUpdate')
            && $this->registerHook('displayHomeTop')
            && $this->registerHook('displayHeader')
            && ProductSale::fillProductSales();
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        return parent::uninstall()
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_NEW')
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_FEATURED')
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_BEST')
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_SPECIAL')
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_IMG')
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_IMGCHANGE')
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_NBR')
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_ROWS')
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_COLUMNSMOBILE')
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_COLUMNSTABLET')
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_COLUMNSLAPTOP')
            && Configuration::deleteByName('ISHIPRODUCTSBLOCK_COLUMNSDESKTOP');

    }

    public function hookActionProductAdd($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionProductUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionProductDelete($params)
    {
        $this->_clearCache('*');
    }

    public function hookCategoryUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionOrderStatusPostUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }

    public function getContent()
    {
        $output = '';
        $errors = array();

        if (Tools::isSubmit('submitIshiProductsBlockModule')) {
            $nbr = (int)Tools::getValue('ISHIPRODUCTSBLOCK_NBR');
            $rownbr = (int)Tools::getValue('ISHIPRODUCTSBLOCK_ROWS');
            $colnbrmmobile = (int)Tools::getValue('ISHIPRODUCTSBLOCK_COLUMNSMOBILE');
            $colnbrtablet = (int)Tools::getValue('ISHIPRODUCTSBLOCK_COLUMNSTABLET');
            $colnbrlaptop = (int)Tools::getValue('ISHIPRODUCTSBLOCK_COLUMNSLAPTOP');
            $colnbrdesktop = (int)Tools::getValue('ISHIPRODUCTSBLOCK_COLUMNSDESKTOP');

            if (!$nbr || $nbr <= 0 || !Validate::isInt($nbr)) {
                $errors[] = $this->l('An invalid number of products has been specified.');
            }

            if (!$rownbr || $rownbr <= 0 || $rownbr > 3 || !Validate::isInt($rownbr)) {
                $errors[] = $this->l('An invalid number of rows has been specified.');
            }


            if (!$colnbrmmobile || $colnbrmmobile <= 0 || !Validate::isInt($colnbrmmobile)) {
                $errors[] = $this->l('An invalid number of columns has been specified for mobile.');
            }

            if (!$colnbrtablet || $colnbrtablet <= 0 || !Validate::isInt($colnbrtablet)) {
                $errors[] = $this->l('An invalid number of columns has been specified for tablet.');
            }

            if (!$colnbrlaptop || $colnbrlaptop <= 0 || !Validate::isInt($colnbrlaptop)) {
                $errors[] = $this->l('An invalid number of columns has been specified for laptop.');
            }

            if (!$colnbrdesktop || $colnbrdesktop <= 0 || !Validate::isInt($colnbrdesktop)) {
                $errors[] = $this->l('An invalid number of columns has been specified for desktop.');
            }

            if (isset($errors) && count($errors)) {
                $output = $this->displayError(implode('<br />', $errors));
            } else {
                Configuration::updateValue('ISHIPRODUCTSBLOCK_NEW', (int)(Tools::getValue('ISHIPRODUCTSBLOCK_NEW')));
                Configuration::updateValue('ISHIPRODUCTSBLOCK_FEATURED', (int)(Tools::getValue('ISHIPRODUCTSBLOCK_FEATURED')));
                Configuration::updateValue('ISHIPRODUCTSBLOCK_BEST', (int)(Tools::getValue('ISHIPRODUCTSBLOCK_BEST')));
                Configuration::updateValue('ISHIPRODUCTSBLOCK_SPECIAL', (int)(Tools::getValue('ISHIPRODUCTSBLOCK_SPECIAL')));
                Configuration::updateValue('ISHIPRODUCTSBLOCK_IMGCHANGE', (int)(Tools::getValue('ISHIPRODUCTSBLOCK_IMGCHANGE')));
                Configuration::updateValue('ISHIPRODUCTSBLOCK_NBR', (int)($nbr));
                Configuration::updateValue('ISHIPRODUCTSBLOCK_ROWS', (int)($rownbr));
                Configuration::updateValue('ISHIPRODUCTSBLOCK_COLUMNSMOBILE', (int)($colnbrmmobile));
                Configuration::updateValue('ISHIPRODUCTSBLOCK_COLUMNSTABLET', (int)($colnbrtablet));
                Configuration::updateValue('ISHIPRODUCTSBLOCK_COLUMNSLAPTOP', (int)($colnbrlaptop));
                Configuration::updateValue('ISHIPRODUCTSBLOCK_COLUMNSDESKTOP', (int)($colnbrdesktop));
                $this->_clearCache('*');
                $output .= $this->displayConfirmation($this->l('Your settings have been updated.'));
            }
        } elseif (((bool)Tools::isSubmit('submitSpecialProductsImage')) == true) {

                if (isset($_FILES['ISHIPRODUCTSBLOCK_IMG']) && isset($_FILES['ISHIPRODUCTSBLOCK_IMG']['tmp_name']) && !empty($_FILES['ISHIPRODUCTSBLOCK_IMG']['tmp_name'])) {
                    if ($error = ImageManager::validateUpload($_FILES['ISHIPRODUCTSBLOCK_IMG'], Tools::convertBytes(ini_get('upload_max_filesize')))) {
                        $errors[] = $error;
                    } else {
                        $id_shop = $this->context->shop->id;
                        $imageName = explode('.', $_FILES['ISHIPRODUCTSBLOCK_IMG']['name']);
                        $imageExt = $imageName[1];
                        $imageName = $imageName[0];
                        $bannerImageName = $imageName . '-' . $id_shop . '.' . $imageExt;

                        if (!move_uploaded_file($_FILES['ISHIPRODUCTSBLOCK_IMG']['tmp_name'], _PS_MODULE_DIR_ . $this->name . '/views/img/' . $bannerImageName)) {
                            $errors[] = $this->l('File upload error.');
                        } else {
                            Configuration::updateValue('ISHIPRODUCTSBLOCK_IMG', $bannerImageName);
                        }
                    }
                }

            $output .= $this->displayConfirmation($this->l('Your settings have been updated.'));
        } elseif (Tools::isSubmit('deleteConfig')) {
            $config = Tools::getValue('deleteConfig');
            $configValue = Configuration::get($config);

            if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $configValue)) {
                unlink(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $configValue);
                $output .= $this->displayConfirmation($this->l('Image Has been deleted'));
            }

            Configuration::updateValue($config, null);
        }

        return  $output.$this->renderImageForm().$this->renderForm();
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show New Products'),
                        'name' => 'ISHIPRODUCTSBLOCK_NEW',
                        'class' => 'fixed-width-xs',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Featured Products'),
                        'name' => 'ISHIPRODUCTSBLOCK_FEATURED',
                        'class' => 'fixed-width-xs',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Best Seller Products'),
                        'name' => 'ISHIPRODUCTSBLOCK_BEST',
                        'class' => 'fixed-width-xs',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Special Products'),
                        'name' => 'ISHIPRODUCTSBLOCK_SPECIAL',
                        'class' => 'fixed-width-xs',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Change product image on hover'),
                        'name' => 'ISHIPRODUCTSBLOCK_IMGCHANGE',
                        'class' => 'fixed-width-xs',
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled'),
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled'),
                            ),
                        ),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Max Products to display'),
                        'name' => 'ISHIPRODUCTSBLOCK_NBR',
                        'class' => 'fixed-width-xs',
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Max Products rows'),
                        'name' => 'ISHIPRODUCTSBLOCK_ROWS',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->l('Maximum 3 rows can be added to the block'),
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Max Products columns for Mobile View'),
                        'name' => 'ISHIPRODUCTSBLOCK_COLUMNSMOBILE',
                        'class' => 'fixed-width-xs',
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Max Products columns for Tablet View'),
                        'name' => 'ISHIPRODUCTSBLOCK_COLUMNSTABLET',
                        'class' => 'fixed-width-xs',
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Max Products columns for Laptop View'),
                        'name' => 'ISHIPRODUCTSBLOCK_COLUMNSLAPTOP',
                        'class' => 'fixed-width-xs',
                    ),
                    array(
                        'col' => 2,
                        'type' => 'text',
                        'label' => $this->l('Max Products columns for Desktop View'),
                        'name' => 'ISHIPRODUCTSBLOCK_COLUMNSDESKTOP',
                        'class' => 'fixed-width-xs',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save')
                ),
            ),
        );
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitIshiProductsBlockModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($fields_form));
    }

    private function renderImageForm()
    {
        $fields_form = array(
            'banner1' => array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Products Block Banner'),
                        'icon' => 'icon-picture-o'
                    ),
                    'input' => array(
                        array(
                            'type' => 'file',
                            'name' => 'ISHIPRODUCTSBLOCK_IMG',
                            'label' => $this->l('Image'),
                            'size' => 20,
                            'required' => false,
                            'lang' => false,
                            'desc' => 'Recommended Size : 600 x 600 px',
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Save'),
                    ),
                )
            ),
        );

        $languages = $this->context->language->getLanguages();

        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitSpecialProductsImage';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        
        $helper->fields_value['ISHIPRODUCTSBLOCK_IMG'] = Configuration::get('ISHIPRODUCTSBLOCK_IMG');

        $helper->tpl_vars = array(
            'languages' => $this->context->controller->getLanguages(),
            'imagePath' => _MODULE_DIR_ . $this->name . '/views/img/',
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm($fields_form);
    }
    public function getConfigFieldsValues()
    {
        $result =  array(
            'ISHIPRODUCTSBLOCK_NEW' => Tools::getValue('ISHIPRODUCTSBLOCK_NEW', Configuration::get('ISHIPRODUCTSBLOCK_NEW')),
            'ISHIPRODUCTSBLOCK_FEATURED' => Tools::getValue('ISHIPRODUCTSBLOCK_FEATURED', Configuration::get('ISHIPRODUCTSBLOCK_FEATURED')),
            'ISHIPRODUCTSBLOCK_BEST' => Tools::getValue('ISHIPRODUCTSBLOCK_BEST', Configuration::get('ISHIPRODUCTSBLOCK_BEST')),
            'ISHIPRODUCTSBLOCK_SPECIAL' => Tools::getValue('ISHIPRODUCTSBLOCK_SPECIAL', Configuration::get('ISHIPRODUCTSBLOCK_SPECIAL')),
            'ISHIPRODUCTSBLOCK_IMGCHANGE' => Tools::getValue('ISHIPRODUCTSBLOCK_IMGCHANGE', Configuration::get('ISHIPRODUCTSBLOCK_IMGCHANGE')),
            'ISHIPRODUCTSBLOCK_NBR' => Tools::getValue('ISHIPRODUCTSBLOCK_NBR', Configuration::get('ISHIPRODUCTSBLOCK_NBR')),
            'ISHIPRODUCTSBLOCK_ROWS' => Tools::getValue('ISHIPRODUCTSBLOCK_ROWS', Configuration::get('ISHIPRODUCTSBLOCK_ROWS')),
            'ISHIPRODUCTSBLOCK_COLUMNSMOBILE' => Tools::getValue('ISHIPRODUCTSBLOCK_COLUMNSMOBILE', Configuration::get('ISHIPRODUCTSBLOCK_COLUMNSMOBILE')),
            'ISHIPRODUCTSBLOCK_COLUMNSTABLET' => Tools::getValue('ISHIPRODUCTSBLOCK_COLUMNSTABLET', Configuration::get('ISHIPRODUCTSBLOCK_COLUMNSTABLET')),
            'ISHIPRODUCTSBLOCK_COLUMNSLAPTOP' => Tools::getValue('ISHIPRODUCTSBLOCK_COLUMNSLAPTOP', Configuration::get('ISHIPRODUCTSBLOCK_COLUMNSLAPTOP')),
            'ISHIPRODUCTSBLOCK_COLUMNSDESKTOP' => Tools::getValue('ISHIPRODUCTSBLOCK_COLUMNSDESKTOP', Configuration::get('ISHIPRODUCTSBLOCK_COLUMNSDESKTOP')),
        );

        return $result;
    }

    public function renderWidget($hookName, array $configuration)
    {
        if ($this->context->controller->php_self == 'index') {
            if (!$this->isCached($this->templateFile, $this->getCacheId(''))) {
                $variables = $this->getWidgetVariables($hookName, $configuration);
                if (empty($variables)) {
                    return false;
                }
                $this->smarty->assign($variables);
            }
            return $this->fetch($this->templateFile, $this->getCacheId(''));
        }
    }

    public function hookdisplayHeader($params)
    {
        $this->context->controller->registerJavascript('modules-ishiproductsblock', 'modules/'.$this->name.'/views/js/ishiproductsblock.js', array('position' => 'bottom', 'priority' => 141));
        Media::addJsDef(array('ishiproductsblock' => array(
            'columnsmobile' => Configuration::get('ISHIPRODUCTSBLOCK_COLUMNSMOBILE'),
            'columnstablet' => Configuration::get('ISHIPRODUCTSBLOCK_COLUMNSTABLET'),
            'columnslaptop' => Configuration::get('ISHIPRODUCTSBLOCK_COLUMNSLAPTOP'),
            'columnsdesktop' => Configuration::get('ISHIPRODUCTSBLOCK_COLUMNSDESKTOP')
        )));
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        $newProducts = $this->getNewProducts();
        $specialProducts = $this->getSpecialProducts();
        $bestSeller = $this->getBestSellers();
        $featureProduct = $this->getFeaturedProducts();

        if(!$newProducts) $newProducts = null;
        if(!$bestSeller) $bestSeller = null;
        if(!$specialProducts) $specialProducts = null;
        
        $ishiproductblock = array();
        if(Configuration::get('ISHIPRODUCTSBLOCK_FEATURED')) {
            $ishiproductblock[] = array('id' => 'featured-products', 'name' => $this->l('Featured Product'), 'productInfo' => $featureProduct);
        }
        if(Configuration::get('ISHIPRODUCTSBLOCK_NEW')) {
            $ishiproductblock[] = array('id' => 'new-products', 'name' => $this->l('New Product'), 'productInfo' => $newProducts);
        }
        if(Configuration::get('ISHIPRODUCTSBLOCK_SPECIAL')) {
            $ishiproductblock[] = array('id' => 'special-products', 'name' => $this->l('On Sale'), 'productInfo' => $specialProducts);
        }
        if(Configuration::get('ISHIPRODUCTSBLOCK_BEST')) {
            $ishiproductblock[] = array('id' => 'bestseller-products', 'name' => $this->l('Latest Product'), 'productInfo' => $bestSeller);
        }

        return array(
            'ishiproductblock' => $ishiproductblock,
            'no_prod' => (int)Configuration::get('ISHIPRODUCTSBLOCK_NBR'),
            'imagePath' => _MODULE_DIR_ . $this->name . '/views/img/',
            'bannerimg' => Configuration::get('ISHIPRODUCTSBLOCK_IMG'),
            'productrows' => Configuration::get('ISHIPRODUCTSBLOCK_ROWS'),
            'imgchange' => Configuration::get('ISHIPRODUCTSBLOCK_IMGCHANGE'),
        );
    }

    protected function getFeaturedProducts()
    {
        $category = new Category(Context::getContext()->shop->getCategory(), (int)Context::getContext()->language->id);
        $nb = (int)Configuration::get('ISHIPRODUCTSBLOCK_NBR');
        $result = $category->getProducts((int) $this->context->language->id, 1, ($nb ? $nb : 8), 'position');

        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );
        $products_for_template = array();
        foreach ($result as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }
        return $products_for_template;
    }

    protected function getNewProducts()
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return false;
        }

        $newProducts = false;
        $nb = Configuration::get('ISHIPRODUCTSBLOCK_NBR');
        $newProducts = Product::getNewProducts((int) $this->context->language->id, 0, ($nb ? $nb : 8));

        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );
        $products_for_template = array();
        if (is_array($newProducts)) {
            foreach ($newProducts as $rawProduct) {
                $products_for_template[] = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($rawProduct),
                    $this->context->language
                );
            }
        }
        return $products_for_template;
    }

    protected function getBestSellers()
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return false;
        }

        $searchProvider = new BestSalesProductSearchProvider(
            $this->context->getTranslator()
        );

        $context = new ProductSearchContext($this->context);

        $query = new ProductSearchQuery();

        $nProducts = (int) Configuration::get('ISHIPRODUCTSBLOCK_NBR');

        $query
            ->setResultsPerPage($nProducts)
            ->setPage(1)
        ;

        $query->setSortOrder(SortOrder::random());

        $result = $searchProvider->runQuery(
            $context,
            $query
        );

        $assembler = new ProductAssembler($this->context);

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        $products_for_template = array();

        foreach ($result->getProducts() as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        return $products_for_template;
    }

    private function getSpecialProducts()
    {
        $nb = Configuration::get('ISHIPRODUCTSBLOCK_NBR');
        $products = Product::getPricesDrop((int)Context::getContext()->language->id, 0, ($nb ? $nb : 8));
        $assembler = new ProductAssembler($this->context);
        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );
        $products_for_template = array();
        if (is_array($products)) {
            foreach ($products as $rawProduct) {
                $products_for_template[] = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($rawProduct),
                    $this->context->language
                );
            }
        }
        return $products_for_template;
    }
}