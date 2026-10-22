<?php if (!defined('ABSPATH')) {exit;}
/**
 * Get unit for Simple Products 
 *
 * @package			YML for Yandex Market
 * @subpackage		
 * @since			0.1.0
 * 
 * @version			1.0.0 (25-05-2023)
 * @author			Maxim Glazunov
 * @link			https://icopydoc.ru/
 * @see				
 * 
 * @param		
 *
 * @return		
 *
 * @depends			classes:	YFYM_Get_Unit_Offer
 *					traits:		
 *					methods:	
 *					functions:	
 *					constants:	
 *					options:	
 *
 */

class YFYM_Get_Unit_Offer_Simple extends YFYM_Get_Unit_Offer {
	use YFYM_T_Common_Get_CatId;
	use YFYM_T_Common_Skips;

	use YFYM_T_Simple_Get_Age;
	use YFYM_T_Simple_Get_Amount;
	use YFYM_T_Simple_Get_Barcode;
	use YFYM_T_Simple_Get_Cargo_Types;
	use YFYM_T_Simple_Get_CategoryId;
	use YFYM_T_Simple_Get_Condition;
	use YFYM_T_Simple_Get_Count;
	use YFYM_T_Simple_Get_Country_Of_Orgin;
	use YFYM_T_Simple_Get_Credit_Template;
	use YFYM_T_Simple_Get_Currencyid;
	use YFYM_T_Simple_Get_Delivery_Options;
	use YFYM_T_Simple_Get_Delivery;
	use YFYM_T_Simple_Get_Description;
	use YFYM_T_Simple_Get_Dimensions;
	use YFYM_T_Simple_Get_Disabled;
	use YFYM_T_Simple_Get_Downloadable;
	use YFYM_T_Simple_Get_Enable_Auto_Discounts;
	use YFYM_T_Simple_Get_Expiry;
	use YFYM_T_Simple_Get_Id;
	use YFYM_T_Simple_Get_Instock;
	use YFYM_T_Simple_Get_Keywords;
	use YFYM_T_Simple_Get_Manufacturer_Warranty;
	use YFYM_T_Simple_Get_Manufacturer;
	use YFYM_T_Simple_Get_Market_Sku;
	use YFYM_T_Simple_Get_Min_Price;
	use YFYM_T_Simple_Get_Min_Quantity;
	use YFYM_T_Simple_Get_Model;
	use YFYM_T_Simple_Get_Name;
	use YFYM_T_Simple_Get_Offer_Tag;
	use YFYM_T_Simple_Get_Outlets;
	use YFYM_T_Simple_Get_Params;
	use YFYM_T_Simple_Get_Period_Of_Validity_Days;
	use YFYM_T_Simple_Get_Pickup_Options;
	use YFYM_T_Simple_Get_Pickup;
	use YFYM_T_Simple_Get_Picture;
	use YFYM_T_Simple_Get_Premium_Price;
	use YFYM_T_Simple_Get_Price;
	use YFYM_T_Simple_Get_Price_Rrp;
	use YFYM_T_Simple_Get_Recommend_Stock_Data;
	use YFYM_T_Simple_Get_Sales_Notes;
	use YFYM_T_Simple_Get_Shop_Sku;
	use YFYM_T_Simple_Get_Step_Quantity;
	use YFYM_T_Simple_Get_Store;
	use YFYM_T_Simple_Get_Supplier;
	use YFYM_T_Simple_Get_Tn_Ved_Codes;
	use YFYM_T_Simple_Get_Url;
	use YFYM_T_Simple_Get_Vat;
	use YFYM_T_Simple_Get_Vendor;
	use YFYM_T_Simple_Get_Vendorcode;
	use YFYM_T_Simple_Get_Weight;

	public function generation_product_xml($result_xml = '') {
		$this->feed_category_id = $this->get_catid();
		$this->get_skips();
		
		$yfym_yml_rules = yfym_optionGET('yfym_yml_rules', $this->feed_id, 'set_arr');
		switch ($yfym_yml_rules) {
			case "yandex_market": 
				$result_xml = $this->adv(); 
				break;
			case "yandex_webmaster": 
				$result_xml = $this->yandex_webmaster(); 
				break;
			case "products_and_offers": 
				$result_xml = $this->products_and_offers(); 
				break;			
			case "single_catalog": 
				$result_xml = $this->single_catalog(); 
				break;	
			case "dbs": 
				$result_xml = $this->dbs(); 
				break;
			case "sales_terms": 
				$result_xml = $this->sales_terms(); 
				break;
			case "beru": 
				$result_xml = $this->old(); 
				break;
			case "all_elements": 
				$result_xml = $this->all_elements(); 
				break;
			case "ozon": 
				$result_xml = $this->ozon(); 
				break;
			case "sbermegamarket": 
				$result_xml = $this->sbermegamarket(); 
				break;
			case "vk": 
				$result_xml = $this->vk(); 
				break;
			default: 
				$result_xml = $this->all_elements();
		}

		$result_xml = apply_filters('yfym_append_simple_offer_filter', $result_xml, $this->product, $this->feed_id);
		$result_xml = apply_filters(
			'y4ym_f_append_simple_offer',
			$result_xml,
			[
				'product' => $this->product,
				'feed_category_id' => $this->get_feed_category_id()
			],
			$this->feed_id
		);
		$result_xml .= '</offer>'. PHP_EOL;
		return $result_xml;
	}	

	private function adv($result_xml = '') {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_url();
		if (class_exists('WOOCS')) { 
			$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->feed_id, 'set_arr');
			if ($yfym_wooc_currencies !== '') {
				global $WOOCS;
				$WOOCS->set_currency($yfym_wooc_currencies);
			}
		}		
		$p = $this->get_price();
		if ($p !== '') {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}

		$result_xml .= $this->get_count();
		$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_weight();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_expiry();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_step_quantity();

		return $result_xml;
	}

	private function single_catalog($result_xml = '') {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_url();

		if (class_exists('WOOCS')) { 
			$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->feed_id, 'set_arr');
			if ($yfym_wooc_currencies !== '') {
				global $WOOCS;
				$WOOCS->set_currency($yfym_wooc_currencies);
			}
		}		
		$p = $this->get_price();
		if ($p !== '') {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}

		$result_xml .= $this->get_count();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_weight();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_expiry();
		$result_xml .= $this->get_period_of_validity_days();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_recommend_stock_data();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_step_quantity();

		return $result_xml;
	}

	private function dbs($result_xml = '') {
	//	https://yandex.ru/support/marketplace/assortment/files/index.html
	//	https://yandex.ru/support/marketplace/tools/elements/offer-general.html
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_url();

		if (class_exists('WOOCS')) { 
			$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->feed_id, 'set_arr');
			if ($yfym_wooc_currencies !== '') {
				global $WOOCS;
				$WOOCS->set_currency($yfym_wooc_currencies);
			}
		}		
		$p = $this->get_price();
		if ($p !== '') {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}
		
		$result_xml .= $this->get_count();
		$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_weight();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_expiry();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_cargo_types();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_step_quantity();

		return $result_xml;
	}

	private function sales_terms($result_xml = '') {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();

		$result_xml .= $this->get_url();
		if (class_exists('WOOCS')) { 
			$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->feed_id, 'set_arr');
			if ($yfym_wooc_currencies !== '') {
				global $WOOCS;
				$WOOCS->set_currency($yfym_wooc_currencies);
			}
		}		
		$p = $this->get_price();
		if ($p !== '') {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_count();

		return $result_xml;
	}

	private function old($result_xml = '') {
		$result_xml .= $this->all_elements();

		return $result_xml;
	}

	private function sbermegamarket($result_xml = '') {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_count();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_delivery_options('shipment-options', '', 'sbermegamarket');
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_expiry();
	//	$result_xml .= $this->get_id();
		$result_xml .= $this->get_instock();
		$result_xml .= $this->get_keywords();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_outlets('outlets', '', 'sbermegamarket');
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_period_of_validity_days();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_premium_price();

		if (class_exists('WOOCS')) { 
			$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->feed_id, 'set_arr');
			if ($yfym_wooc_currencies !== '') {
				global $WOOCS;
				$WOOCS->set_currency($yfym_wooc_currencies);
			}
		}		
		$p = $this->get_price();
		if ($p !== '') {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}

		$result_xml .= $this->get_recommend_stock_data();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_step_quantity();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_url();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_weight();

		return $result_xml;
	}

	private function vk($result_xml = '') {
	    $s_product = new WC_Product(str_replace("<id>", "", str_replace("</id>\n","",$this->get_id())));
	    
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_count();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_delivery();
		// $result_xml .= $this->get_description();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_expiry();
	    //	$result_xml .= $this->get_id();
		$result_xml .= $this->get_instock();
		$result_xml .= $this->get_keywords();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_model();
		$condition_text = ($s_product->get_attribute('sostoyanie') == 'новый') ? $s_product->get_attribute('sostoyanie') : 'б/у';
		$result_xml .= '<name>' . $s_product->get_name() . '</name>';
		$result_xml .= $this->get_outlets();
		$result_xml .= $this->get_params();

        // ////////
        // Статус товара для куфара

        $attribute_value = '1'; // значение атрибута

        // Product Status
        if ($s_product->get_attribute('sostoyanie') == 'новый') {
           $attribute_value = "2";
        }

        if ($s_product->get_attribute('sostoyanie') == '5' or $s_product->get_attribute('sostoyanie') == '4' or $s_product->get_attribute('sostoyanie') == '3' or $s_product->get_attribute('sostoyanie') == '4+') {
           $attribute_value = "1";
        }
        $result_xml .= '<param name="condition">'.$attribute_value.'</param>';

        // Описание
        $display_result = ''. $s_product->get_attribute('osnovnye-harakterist') . '';
        $result_xml .= "<description><![CDATA[" . $display_result . "]]></description>";


        // ////////

		$result_xml .= $this->get_period_of_validity_days();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_premium_price();

		if (class_exists('WOOCS')) { 
			$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->feed_id, 'set_arr');
			if ($yfym_wooc_currencies !== '') {
				global $WOOCS;
				$WOOCS->set_currency($yfym_wooc_currencies);
			}
		}		
		$p = $this->get_price();
		if ($p !== '') {
			$result_xml .= $p;
			$result_xml .= $this->get_price_rrp();
		}
		$result_xml .= $this->get_currencyid();
		if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}

		$result_xml .= $this->get_recommend_stock_data();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_step_quantity();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_url();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_cargo_types();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_weight();

		return $result_xml;
	}

	private function all_elements($result_xml = '') {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_count();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_delivery();
		// $result_xml .= $this->get_description();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_expiry();
	    //	$result_xml .= $this->get_id();
		$result_xml .= $this->get_instock();
		$result_xml .= $this->get_keywords();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_outlets();
		$result_xml .= $this->get_params();

        // ////////
        // Статус товара для куфара
        $s_product = new WC_Product(str_replace("<id>", "", str_replace("</id>\n","",$this->get_id())));

        $attribute_value = '1'; // значение атрибута

        // Product Status
        if ($s_product->get_attribute('sostoyanie') == 'новый') {
           $attribute_value = "2";
        }

        if ($s_product->get_attribute('sostoyanie') == '5' or $s_product->get_attribute('sostoyanie') == '4' or $s_product->get_attribute('sostoyanie') == '3' or $s_product->get_attribute('sostoyanie') == '4+') {
           $attribute_value = "1";
        }
        $result_xml .= '<param name="condition">'.$attribute_value.'</param>';

        // Описание
        $display_result = '';

        $display_result .= '&#183; Код товара: '.$s_product->get_sku().'&#10;';
        $display_result .= '&#183; Модель: '.$s_product->get_name().'&#10;';
        $display_result .= '&#183; Скидка до 60%. Акция действует только до конца месяца!&#10;';
        $display_result .= '&#183; Ещё больше предложений на сайте 100NOUT.BY&#10;';
        $display_result .= '&#183; При заказе через корзину сайта - подарок!&#10;';
        

        if ($s_product->get_attribute('osnovnye-harakterist')) {
            $display_result .= '&#183; Характеристики: ' . $s_product->get_attribute('osnovnye-harakterist') . '&#10;';
        }

        if ($s_product->get_attribute('vneshnij-vid')) {
            $display_result .= '&#183; Внешний вид: ' . $s_product->get_attribute('vneshnij-vid') . '&#10;';
        }

        if ($s_product->get_attribute('nedostatki')) {
            $display_result .= '&#183; Недостатки: ' . $s_product->get_attribute('nedostatki') . '&#10;';
        }

        if ($s_product->get_attribute('komplektacziya')) {
            $display_result .= '&#183; Комплектация: ' . $s_product->get_attribute('komplektacziya') . '&#10;';
        }

        $display_result .= "&#183; Официальная гарантия до 12 месяцев. 30 дней стандартная гарантия. 14 дней возврат/обмен.&#10;";
        $display_result .= "&#183; Доставка по Минску, РБ. Почта, Европочта, Курьер. &#10;";
        $display_result .= "&#183; Наличный и безналичный расчёт. Работаем с юридическими лицами и ИП (БЕЗНАЛ) &#10;";
        $display_result .= "&#183; Рассрочка, кредит: Магнит, Халва, Черепаха, Карта покупок, Карта FUN, SMART карта, Приорбанк. &#10;";

        $display_result .= "&#183; Принимем в зачёт (Трейд-ин/Trade in), выкупим (ВЫКУП) Ваш ноутбук, телефон, смартфон, монитор, компьютер. &#10;";
        $display_result .= "&#183; Мы находися: Минск, пр-т. Независимости, 94 (ст. метро Московская), ежедневно 9:00 до 21:00. &#10;";

        $result_xml .= "<description><![CDATA[" . $display_result . "]]></description>";


        // ////////

		$result_xml .= $this->get_period_of_validity_days();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_premium_price();

		if (class_exists('WOOCS')) { 
			$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->feed_id, 'set_arr');
			if ($yfym_wooc_currencies !== '') {
				global $WOOCS;
				$WOOCS->set_currency($yfym_wooc_currencies);
			}
		}		
		$p = $this->get_price();
		if ($p !== '') {
			$result_xml .= $p;
			$result_xml .= $this->get_price_rrp();
		}
		$result_xml .= $this->get_currencyid();
		if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}

		$result_xml .= $this->get_recommend_stock_data();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_step_quantity();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_url();
		$result_xml .= $this->get_vat();
		$result_xml .= $this->get_cargo_types();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_weight();

		return $result_xml;
	}

	private function ozon($result_xml = '') {
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_name();
		$result_xml .= $this->get_enable_auto_discounts();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_picture();
		$result_xml .= $this->get_url();

		if (class_exists('WOOCS')) { 
			$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->feed_id, 'set_arr');
			if ($yfym_wooc_currencies !== '') {
				global $WOOCS;
				$WOOCS->set_currency($yfym_wooc_currencies);
			}
		}		
		$p = $this->get_price();
		if ($p !== '') {
			$result_xml .= $p;
			$result_xml .= $this->get_currencyid();
		}
		if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}
		$result_xml .= $this->get_min_price();

		$result_xml .= $this->get_count();
		$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_weight();
		$result_xml .= $this->get_dimensions();
		$result_xml .= $this->get_expiry();
		$result_xml .= $this->get_age();
		$result_xml .= $this->get_downloadable();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_store();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_vat();
// $result_xml .= $this->get_cargo_types	();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_condition();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_step_quantity();

		$result_xml .= $this->get_premium_price();
		$result_xml .= $this->get_outlets();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_tn_ved_codes();

		return $result_xml;
	}

	private function yandex_webmaster($result_xml = '') {
		// https://yandex.ru/support/products/features.html
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
	//	$result_xml .= $this->get_age();
	//	$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_condition();
	//	$result_xml .= $this->get_count();
		$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_dimensions();
	//	$result_xml .= $this->get_downloadable();
	//	$result_xml .= $this->get_enable_auto_discounts();
	//	$result_xml .= $this->get_expiry();
	//	$result_xml .= $this->get_id();
		$result_xml .= $this->get_instock();
		$result_xml .= $this->get_keywords();
	//	$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_name();
	//	$result_xml .= $this->get_outlets();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_period_of_validity_days();
		$result_xml .= $this->get_pickup_options();
		$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_picture();
		// $result_xml .= $this->get_premium_price();

		if (class_exists('WOOCS')) { 
			$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->feed_id, 'set_arr');
			if ($yfym_wooc_currencies !== '') {
				global $WOOCS;
				$WOOCS->set_currency($yfym_wooc_currencies);
			}
		}
		$result_xml .= $this->get_price();
		/*$p = $this->get_price();
		if ($p !== '') {
			$result_xml .= $p;
			$result_xml .= $this->get_price_rrp();
		}*/
		$result_xml .= $this->get_currencyid();
		if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}

		$result_xml .= $this->get_recommend_stock_data();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_step_quantity();
	//	$result_xml .= $this->get_store();
	//	$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_url();
	//	$result_xml .= $this->get_vat();
		$result_xml .= $this->get_cargo_types();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_weight();

		return $result_xml;
	}

	private function products_and_offers($result_xml = '') {
		// https://yandex.ru/support/products/partners.html
		$result_xml .= $this->get_offer_tag();
		$result_xml .= $this->get_disabled();
	//	$result_xml .= $this->get_age();
	//	$result_xml .= $this->get_amount();
		$result_xml .= $this->get_barcode();
		$result_xml .= $this->get_categoryid();
		$result_xml .= $this->get_condition();
	//	$result_xml .= $this->get_count();
	//	$result_xml .= $this->get_country_of_origin();
		$result_xml .= $this->get_credit_template();
		$result_xml .= $this->get_delivery_options();
		$result_xml .= $this->get_delivery();
		$result_xml .= $this->get_description();
		$result_xml .= $this->get_dimensions();
	//	$result_xml .= $this->get_downloadable();
	//	$result_xml .= $this->get_enable_auto_discounts();
	//	$result_xml .= $this->get_expiry();
	//	$result_xml .= $this->get_group_id();
	//	$result_xml .= $this->get_id();
		$result_xml .= $this->get_instock();
		$result_xml .= $this->get_keywords();
	//	$result_xml .= $this->get_manufacturer_warranty();
		$result_xml .= $this->get_manufacturer();
		$result_xml .= $this->get_market_sku();
		$result_xml .= $this->get_min_quantity();
		$result_xml .= $this->get_model();
		$result_xml .= $this->get_name();
	//	$result_xml .= $this->get_outlets();
		$result_xml .= $this->get_params();
		$result_xml .= $this->get_period_of_validity_days();
	//	$result_xml .= $this->get_pickup_options();
	//	$result_xml .= $this->get_pickup();
		$result_xml .= $this->get_picture();
		// $result_xml .= $this->get_premium_price();

		if (class_exists('WOOCS')) { 
			$yfym_wooc_currencies = yfym_optionGET('yfym_wooc_currencies', $this->feed_id, 'set_arr');
			if ($yfym_wooc_currencies !== '') {
				global $WOOCS;
				$WOOCS->set_currency($yfym_wooc_currencies);
			}
		}
		$result_xml .= $this->get_price();
		/*$p = $this->get_price();
		if ($p !== '') {
			$result_xml .= $p;
			$result_xml .= $this->get_price_rrp();
		}*/
		$result_xml .= $this->get_currencyid();
		if (class_exists('WOOCS')) {global $WOOCS; $WOOCS->reset_currency();}

		$result_xml .= $this->get_recommend_stock_data();
		$result_xml .= $this->get_sales_notes();
		$result_xml .= $this->get_shop_sku();
		$result_xml .= $this->get_step_quantity();
	//	$result_xml .= $this->get_store();
	//	$result_xml .= $this->get_supplier();
		$result_xml .= $this->get_tn_ved_codes();
		$result_xml .= $this->get_url();
	//	$result_xml .= $this->get_vat();
		$result_xml .= $this->get_cargo_types();
		$result_xml .= $this->get_vendor();
		$result_xml .= $this->get_vendorcode();
		$result_xml .= $this->get_weight();

		return $result_xml;	
	}
}