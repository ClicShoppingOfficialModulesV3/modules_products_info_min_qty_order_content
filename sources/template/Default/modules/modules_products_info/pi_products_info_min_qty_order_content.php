<?php
/**
 * pi_products_info_min_qty_order.php 
 * @copyright Copyright 2008 - http://www.innov-concept.com
 * @Brand : ClicShopping(Tm) at Inpi all right Reserved
 * @license GPL 2 License & MIT Licence

 */

  use ClicShopping\OM\HTML;
  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;

  class pi_products_info_min_qty_order_content {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_products_info_min_qty_order');
      $this->description = CLICSHOPPING::getDef('module_products_info_min_qty_order_description');

      if (defined('MODULE_PRODUCTS_INFO_MIN_QTY_ORDER_STATUS')) {
        $this->sort_order = MODULE_PRODUCTS_INFO_MIN_QTY_ORDER_SORT_ORDER;
        $this->enabled = (MODULE_PRODUCTS_INFO_MIN_QTY_ORDER_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_ProductsCommon = Registry::get('ProductsCommon');
      $CLICSHOPPING_Template = Registry::get('Template');

      if ($CLICSHOPPING_ProductsCommon->getID()) {

        $content_width = (int)MODULE_PRODUCTS_INFO_MIN_QTY_ORDER_CONTENT_WIDTH;

        if (MODULE_PRODUCTS_INFO_PRICE_SORT_ORDER != 0) {

         if (MAX_MIN_IN_CART > 1 || $CLICSHOPPING_ProductsCommon->getProductsMinimumQuantity($CLICSHOPPING_ProductsCommon->getID()) > 1) {
           $products_min_qty_order_content = '<!-- Start products_info_min_qty_order -->' . "\n";

           ob_start();
           require($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/products_info_min_qty_order_content'));
           $products_min_qty_order_content .= ob_get_clean();

           $products_min_qty_order_content .= '<!-- end products_info_min_qty_order -->' . "\n";

           $CLICSHOPPING_Template->addBlock($products_min_qty_order_content, $this->group);
         }
        }
      }
    } // public function execute

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_PRODUCTS_INFO_MIN_QTY_ORDER_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Souhaitez-vous activer ce module ?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_MIN_QTY_ORDER_STATUS',
          'configuration_value' => 'True',
          'configuration_description' => 'Souhaitez vous activer ce module à votre boutique ?',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Veuillez selectionner la largeur de l\'affichage?',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_MIN_QTY_ORDER_CONTENT_WIDTH',
          'configuration_value' => '12',
          'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
          'configuration_group_id' => '6',
          'sort_order' => '1',
          'set_function' => 'clic_cfg_set_content_module_width_pull_down',
          'date_added' => 'now()'
        ]
      );

      $CLICSHOPPING_Db->save('configuration', [
          'configuration_title' => 'Ordre de tri d\'affichage',
          'configuration_key' => 'MODULE_PRODUCTS_INFO_MIN_QTY_ORDER_SORT_ORDER',
          'configuration_value' => '100',
          'configuration_description' => 'Ordre de tri pour l\'affichage (Le plus petit nombre est montré en premier)',
          'configuration_group_id' => '6',
          'sort_order' => '3',
          'set_function' => '',
          'date_added' => 'now()'
        ]
      );

      return $CLICSHOPPING_Db->save('configuration', ['configuration_value' => '1'],
                                              ['configuration_key' => 'WEBSITE_MODULE_INSTALLED']
                            );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return array (
        'MODULE_PRODUCTS_INFO_MIN_QTY_ORDER_STATUS',
        'MODULE_PRODUCTS_INFO_MIN_QTY_ORDER_CONTENT_WIDTH',
        'MODULE_PRODUCTS_INFO_MIN_QTY_ORDER_SORT_ORDER'
      );
    }
  }