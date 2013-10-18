<?php
/*
Plugin Name: WooCommerce picklist
Plugin URI: http://www.vaibhavign.com
Description: Product for all processing orders
Version: 1.0
Author: Vaibhav Sharma
Author Email: http://www.vaibhavign.com
*/

/**
 * Copyright (c) `date "+%Y"` Vaibhav Sharma. All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * **********************************************************************
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WC_Picklist{
    public function __construct(){
        add_action('admin_menu', array( &$this, 'woocommerce_manifest_admin_menu' )); 
       
    }
                            
function woocommerce_manifest_admin_menu() {
    add_menu_page(__('Picklist','wc-checkout-cod-pincodes'), __('Picklist','wc-checkout-cod-pincodes'), 'manage_options', 'eshopbox-picklist', array( &$this, 'eshopbox_picklist_page' ) );
     
 }
        
        /**
         * Create admin manifest page
         * @global type $woocommerce
         */

function eshopbox_picklist_page() {
    global $woocommerce;
    global $wpdb;

    $args = array(
             'post_type' => 'shop_order',
             'post_status' => 'publish',
            'posts_per_page' => -1  
    );

    $my_query=get_posts($args);
    $finalarray[]=array("Product name","color","size","quantity");
    foreach($my_query as $key=>$val){
      $abc = new WC_Order($val->ID);
    //  echo '<pre>';
     // print_r($abc);
      if($abc->status=='processing'){

       foreach($abc->get_items() as $key=>$item){
          $sku = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value  FROM $wpdb->postmeta WHERE meta_key='_sku' AND post_id='%d' LIMIT 1", $item['product_id'] ) ); 
           
          echo '<pre>';
          print_r($item);
           
         //  $pro[$item['name']][$item['pa_color']][$item['pa_size']] = $pro[$item['name']][$item['pa_color']][$item['pa_size']] + $item['qty']; 
$pro[$val->ID][$sku][$item['pa_color']] = $item['pa_color']; 
$pro[$val->ID][$sku][$item['pa_size']] = $pro[$sku][$item['pa_size']];
$pro[$val->ID][$sku]['odate'] = $abc->order_date;
       }
      }

    }

    echo '<pre>';
    print_r($pro);
    foreach($pro as $key=>$val){
       foreach($val as $key1=>$val1){
           foreach($val1 as $key2=>$val2){
             $finalarray[] = array($key,$key1,$key2,$val2); 
           }
       }
    }

if($_GET['d']=='true'){
    ob_clean();
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=picklist.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    $this->outputCSV($finalarray);
}
    ?>
    <div id="manifesttable">
        <table width="100%" cellspacing="0" cellpadding="0" class="widefat">
            <thead>
                <tr>
        <th style="padding:7px 7px 8px; "><?php if(count($finalarray)>1){  ?><a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=eshopbox-picklist&d=true">Download</a><?php } ?></th>            
        <th style="padding:7px 7px 8px; ">Name</th>
        <th style="padding:7px 7px 8px; ">Color</th>
        <th style=" padding:7px 7px 8px;">Size</th>
        <th style="padding:7px 7px 8px;">Quantity</th>
       </tr></thead>
            <tfoot>
                <tr>
                    <th style="padding:7px 7px 8px; "><?php if(count($finalarray)>1){  ?><a href="<?php echo $_SERVER['PHP_SELF'] ?>?page=eshopbox-picklist&d=true">Download</a><?php }  ?></th>
                <th style="padding:7px 7px 8px; ">Name</th>
        <th style="padding:7px 7px 8px; ">Color</th>
        <th style=" padding:7px 7px 8px;">Size</th>
        <th style="padding:7px 7px 8px;">Quantity</th>
        </tr></tfoot>

    <tbody id="manifdetail">
        <?php
    if(count($finalarray)>1){  
        unset($finalarray[0]);
        foreach($finalarray as $key=>$value){
       echo  '<tr>
                <th style="padding:7px 7px 8px; ">'.$value[0].'</th>
        <th style="padding:7px 7px 8px; ">'.$value[1].'</th>
        <th style=" padding:7px 7px 8px;">'.$value[2].'</th>
        <th style="padding:7px 7px 8px;">'.$value[3].'</th>
        </tr>';
    }} else {
        echo "No processing order";
    }
        ?>
 </tbody>
    </table>
</div>
 <?php
}     

public function outputCSV($finalarray){
	$outputBuffer = fopen("php://output", 'w');
	foreach($finalarray as $val) {
	    fputcsv($outputBuffer, $val);
	}
	fclose($outputBuffer);
        exit;
}

/**
     * Get the plugin url.
     *
     * @access public
     * @return string
     */
    public function plugin_url() {
        if ( $this->plugin_url ) return $this->plugin_url;
        return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
    }

    /**
     * Get the plugin path.
     *
     * @access public
     * @return string
     */
    public function plugin_path() {
        if ( $this->plugin_path ) return $this->plugin_path;
        return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
    }
  

}
new WC_Picklist();