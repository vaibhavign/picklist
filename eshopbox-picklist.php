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
      if($abc->status=='on-hold'){

       foreach($abc->get_items() as $key=>$item){
          $sku = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value  FROM $wpdb->postmeta WHERE meta_key='_sku' AND post_id='%d' LIMIT 1", $item['product_id'] ) ); 
           global $post;
$terms = get_the_terms( $item['product_id'], 'product_cat' );
foreach ($terms as $term) {
    $product_cat_name = $term->name;    
}
       //   echo '<pre>';
       //   print_r($item);
          
          $finalpicklist[$sku]['name']= $item['name'];
          $finalpicklist[$sku]['packagetofectch']= $finalpicklist[$sku]['packagetofectch']+$item['qty'];
          $finalpicklist[$sku]['orderid']= $val->ID.','.$finalpicklist[$sku]['orderid'];
          $finalpicklist[$sku]['catname']=  $product_cat_name;
         
        
        
          
          
         //  $pro[$item['name']][$item['pa_color']][$item['pa_size']] = $pro[$item['name']][$item['pa_color']][$item['pa_size']] + $item['qty']; 
//$pro[$val->ID][$sku][$item['pa_color']] = $item['pa_color']; 
//$pro[$val->ID][$sku][$item['pa_size']] = $pro[$sku][$item['pa_size']];
//$pro[$val->ID][$sku]['odate'] = $abc->order_date;
       }
      }

    }

  //echo '<pre>';
  //print_r($finalpicklist); exit;
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
<?php
if($_POST['processorder']!='yes'){
?>
<form name="manifestform" id="manifestform" style="margin:4px 15px 0 0;" method="post" action="" target="">                               
    <div id="manifesttable">
    <input type="submit" id="submit" class="button" name="submit" style="margin-bottom: 10px; margin-right:20px" value="Fetch" />
   
        <table width="100%" cellspacing="0" cellpadding="0" class="widefat">
            <thead>
                <tr>
        <th style="padding:7px 7px 8px; "><input type="checkbox" name="checkall" id="checkall" class="select-all" value="'.$theorder->id.'" /></th>
        <th style="padding:7px 7px 8px; ">SKU</th>
        <th style="padding:7px 7px 8px; ">Name</th>
        <th style=" padding:7px 7px 8px;">Category</th>
        <th style="padding:7px 7px 8px;">Order Status</th>
        <th style="padding:7px 7px 8px;">Package to fetch</th>
<!--
        <th style="padding:7px 7px 8px;">Payment Method</th>

        <th style="padding:7px 7px 8px;">Status</th>
        <th style="padding:7px 7px 8px;">Action</th>  -->
        </tr></thead>
            <tfoot>
                <tr>
        <th style="padding:7px 7px 8px; "><input type="checkbox" class="select-all" name="checkall" id="checkall" value="'.$theorder->id.'" /></th>
        <th style="padding:7px 7px 8px; ">SKU</th>
        <th style="padding:7px 7px 8px; ">Name</th>
        <th style=" padding:7px 7px 8px;">Category</th>
        <th style="padding:7px 7px 8px;">Order Status</th>
        <th style="padding:7px 7px 8px;">Package to fetch</th>
<!--
        <th style="padding:7px 7px 8px;">Payment Method</th>

        <th style="padding:7px 7px 8px;">Status</th>
        <th style="padding:7px 7px 8px;">Action</th>  -->
        </tr></tfoot>
       
   

    
    <tbody id="manifdetail">
        <?php
        foreach($finalpicklist as $key=>$val){
        ?>
        <tr id="'.$theorder->id.'tr">
             <td style="padding:7px 7px 8px; "><input style="margin:0 0 0 8px;" type="checkbox" name="check[]" value="<?php echo $val['orderid']; ?>" /></td>
             <td style="padding:7px 7px 8px; "><?php echo $key;  ?></td>
             <td style=" padding:7px 7px 8px; "><?php echo $val['name'];  ?></td>
             <td style="padding:7px 7px 8px; "><?php echo $val['catname'];  ?></td>
             <td style="padding:7px 7px 8px; ">on-hold</td>
             <td style="padding:7px 7px 8px; "><?php echo $val['packagetofectch'];  ?></td>

             </tr>
        <?php
        }
        ?>
        
        
    </tbody>
    </table>
</div>
<input type="hidden" id="processorder" name="processorder" value="yes" />
<input type="submit" id="submit" class="button" name="submit" style="margin-top: 10px; margin-right:20px" value="Fetch" />


</form>
 <?php
 $woocommerce->add_inline_js("
    jQuery(document).ready(function(){
    
    jQuery('.markasship').on('click',function(event){
   // event.preventDefault();
   var checkcheck = 0;
        $(':checkbox').each(function() {
           if(this.checked == true){
               // alert('checked');
                checkcheck = 1;
            }
        });   
      if(checkcheck==1){
    jQuery('#manifestform').attr('action','');
    jQuery('#manifestform').attr('target','');
    

     $('#manifestform').submit();
} else {
alert('Please select a shipment');
return false;
}
});

jQuery('.rem').live('click',function(event){
    event.preventDefault();

    jQuery('#'+jQuery(this).attr('rel')+'tr').remove();

});
    
    
$('.select-all').on('click',function(event) {   
    if(this.checked) {
        // Iterate each checkbox
        $(':checkbox').each(function() {
            this.checked = true;                        
        });
    }
});

          jQuery('#ordert').keyup(function(event){
          var tex = jQuery(this).val();
          var checkfl = 0;
              if(event.keyCode==13){
            //  alert(jQuery('#onlyshipments').val());
              orderString = jQuery('#onlyorders').val(); 
             // alert(orderString);
                var arrayOrders = orderString.split(',');
             //   alert(arrayOrders[0]);
              jQuery.each(arrayOrders,function(i,v){
              if(arrayOrders[i]==tex){
              checkfl = 1;
              }
               
                });
                
var arrayShipments = jQuery('#onlyshipments').val().split(',');
              jQuery.each(arrayShipments,function(i,v){
              if(arrayShipments[i]==tex){
              checkfl = 1;
              }
               
                });
                
if(checkfl==0){
alert('Invalid order/shipment id');
return false;
}
                
                  var textBoxText = jQuery(this).val();
                  jQuery(this).val('');
                  var orderData = {
                  action: 'my_orderaction',
                  orderId : textBoxText
               };

               jQuery.post(ajaxurl,orderData,function(response){
                      jQuery('#manifdetail').after(response);
               });

              }
          });
    });  

    jQuery('#selectprovider').bind('change',function(){
   var payType = jQuery('#paytype').val()
    jQuery('#shipprovider').val(jQuery(this).val());
    jQuery('#paymethod').val(payType);

      jQuery('#loadimg').show();
          var data = {
          action: 'my_actions',
          whatever: 1234,
          valselected : jQuery(this).val(),
          paytype : payType
  };

jQuery.post(ajaxurl, data, function(response) {

         splitResponse = response.split('$');
         if(splitResponse[0]==0){
             alert('No pending shipments');
             jQuery('#noshipments').html('0');
             jQuery('#noorders').html('Nil');
             jQuery('#ordert').val('');

         } else {
         jQuery('#noshipments').html(splitResponse[0]);
         jQuery('#noorders').html(splitResponse[1]);
         jQuery('#onlyorders').val(splitResponse[2]);
         jQuery('#onlyshipments').val(splitResponse[3]);
         
         jQuery('#ordert').val('');
     }
  });
});

"); 
} else {
    
    ?>
<form name="manifestform" id="manifestform" style="margin:4px 15px 0 0;" method="post" action="" target="">                               
    <div id="manifesttable">
    <input type="submit" id="submit" class="button" name="submit" style="margin-bottom: 10px; margin-right:20px" value="Fetch" />
   
        <table width="100%" cellspacing="0" cellpadding="0" class="widefat">
            <thead>
                <tr>
        <th style="padding:7px 7px 8px; "><input type="checkbox" name="checkall" id="checkall" class="select-all" value="'.$theorder->id.'" /></th>
        <th style="padding:7px 7px 8px; ">Ref.No</th>
        <th style="padding:7px 7px 8px; ">SKU</th>
        <th style=" padding:7px 7px 8px;">OrderDate</th>
        <th style="padding:7px 7px 8px;">AWB</th>
        <th style="padding:7px 7px 8px;">Product Name</th>
        <th style="padding:7px 7px 8px;">Category</th>
        <th style="padding:7px 7px 8px;">Shipping city</th>
        <th style="padding:7px 7px 8px;">Buyer Mobile</th>
        <th style="padding:7px 7px 8px;">Shipping provider</th>
        <th style="padding:7px 7px 8px;">Shipping method</th>
<!--
        <th style="padding:7px 7px 8px;">Payment Method</th>

        <th style="padding:7px 7px 8px;">Status</th>
        <th style="padding:7px 7px 8px;">Action</th>  -->
        </tr></thead>
            <tfoot>
                <tr>
        <th style="padding:7px 7px 8px; "><input type="checkbox" class="select-all" name="checkall" id="checkall" value="'.$theorder->id.'" /></th>
        <th style="padding:7px 7px 8px; ">Ref.No</th>
        <th style="padding:7px 7px 8px; ">SKU</th>
        <th style=" padding:7px 7px 8px;">OrderDate</th>
        <th style="padding:7px 7px 8px;">AWB</th>
        <th style="padding:7px 7px 8px;">Product Name</th>
        <th style="padding:7px 7px 8px;">Category</th>
        <th style="padding:7px 7px 8px;">Shipping city</th>
        <th style="padding:7px 7px 8px;">Buyer Mobile</th>
        <th style="padding:7px 7px 8px;">Shipping provider</th>
        <th style="padding:7px 7px 8px;">Shipping method</th>
<!--
        <th style="padding:7px 7px 8px;">Payment Method</th>

        <th style="padding:7px 7px 8px;">Status</th>
        <th style="padding:7px 7px 8px;">Action</th>  -->
        </tr></tfoot>
       
   

    
    <tbody id="manifdetail">
        <?php
            
    foreach($_POST['check'] as $key=>$val){
        $orderid .= $val;
    }
    $orderid = substr($orderid, 0,-1);
  //  echo $orderid;
    $my_query = explode(',',$orderid);
        foreach($my_query as $key=>$val){
      $abc = new WC_Order($val);
     // echo '<pre>';
    //  print_r($abc);
      if($abc->status=='on-hold'){

       foreach($abc->get_items() as $key=>$item){
          $sku = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value  FROM $wpdb->postmeta WHERE meta_key='_sku' AND post_id='%d' LIMIT 1", $item['product_id'] ) ); 
           global $post;
$terms = get_the_terms( $item['product_id'], 'product_cat' );
foreach ($terms as $term) {
    $product_cat_name = $term->name;    
}
       
        ?>
        <tr id="'.$theorder->id.'tr">
             <td style="padding:7px 7px 8px; "><input style="margin:0 0 0 8px;" type="checkbox" name="check[]" value="<?php echo $val['orderid']; ?>" /></td>
             <td style="padding:7px 7px 8px; "><?php echo $val;  ?></td>
             <td style=" padding:7px 7px 8px; "><?php echo $sku;  ?></td>
             <td style="padding:7px 7px 8px; "><?php echo $abc->order_date;  ?></td>
             <td style="padding:7px 7px 8px; "><?php echo $item['name'];  ?></td>
             <td style="padding:7px 7px 8px; "><?php echo $item['name'];  ?></td>
             <td style="padding:7px 7px 8px; "><?php echo $product_cat_name;  ?></td>
             <td style="padding:7px 7px 8px; "><?php echo $abc->shipping_city;  ?></td>
             <td style="padding:7px 7px 8px; "><?php echo $abc->billing_phone;  ?></td>
             <td style="padding:7px 7px 8px; ">xxxxxxxxxx</td>
             <td style="padding:7px 7px 8px; "><?php echo $abc->payment_method_title;  ?></td>

             </tr>
        <?php
        } } }
        ?>
        
        
    </tbody>
    </table>
</div>
<input type="hidden" id="processorder" name="processorder" value="yes" />
<input type="submit" id="submit" class="button" name="submit" style="margin-top: 10px; margin-right:20px" value="Fetch" />


</form>

<?php
}
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