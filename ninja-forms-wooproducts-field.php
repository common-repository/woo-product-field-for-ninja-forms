<?php
/**
 * Plugin Name: Woo Product Field for Ninja Forms
 * Plugin URI: https://codeboxr.com/product/ninja-forms-woocommerce-product-dropdown-field/
 * Description: This plugin add woocommerce orders of loggedin users in dropdown
 * Version: 1.0.1
 * Author: Codeboxr
 * Author URI: http://www.codeboxr.com
 * License:  GPLv2 or later
 */

class CBXNinjaformWooProductsField{
    public function __construct()
    {
        //load the language
        load_plugin_textdomain('woo-product-field-for-ninja-forms', false, basename(dirname(__FILE__)) . '/languages/');

        add_action( 'init', array($this, 'ninja_forms_register_field_wooproducts') );

    }

    /**
     * Register new Ninja Forms field
     */
    public function ninja_forms_register_field_wooproducts()
    {


        if(function_exists('ninja_forms_register_field')){

            $args = array(
                'name'               =>  esc_html__( 'Woo Products', 'ninjaformwooproductsfields' ),
                'sidebar'            =>  'template_fields',
                'edit_function'      =>  array($this, 'ninja_forms_field_wooproducts_edit'),
                'display_function'   =>  array($this, 'ninja_forms_field_wooproducts_display'),
                'save_function'      =>  '',
                'group'              =>  'standard_fields',
                'edit_label'         =>  true,
                'edit_label_pos'     =>  true,
                'edit_req'           =>  true,
                'edit_custom_class'  =>  true,
                'edit_help'          =>  true,
                'edit_desc'          =>  true,
                'edit_meta'          =>  false,
                'edit_conditional'   =>  true
            );

            ninja_forms_register_field( '_wooproducts', $args );
        }
    }//end method ninja_forms_register_field_wooproducts

    /**
     * Edit field in admin
     */
    public function ninja_forms_field_wooproducts_edit( $field_id, $data )
    {
        $plugin_settings = nf_get_settings();



        $custom = '';

        // Default Value
        if( isset( $data['default_value'] ) ) {
            $default_value = $data['default_value'];
        } else {
            $default_value = '';
        }
        if( $default_value == 'none' ) {
            $default_value = '';
        }

        ?>
        <div class="description description-thin">

            <label for="" id="default_value_label_<?php echo $field_id;?>" style="<?php if( $custom == 'no' ) { echo 'display:none;'; } ?>">
			<span class="field-option">
			<?php esc_html_e( 'Default Value' , 'ninjaformwooproductsfields' ); ?><br />
			<input type="text" class="widefat code" name="ninja_forms_field_<?php echo $field_id; ?>[default_value]" id="ninja_forms_field_<?php echo $field_id; ?>_default_value" value="<?php echo $default_value; ?>" />
			</span>
            </label>

        </div>

        <?php
    }//end method ninja_forms_field_wooproducts_edit


    /**
     * Display field on front-end
     */
    public function ninja_forms_field_wooproducts_display( $field_id, $data )
    {
        global $current_user;
        $field_class = ninja_forms_get_field_class( $field_id );

        if( isset( $data['default_value'] ) ) {
            $default_value = $data['default_value'];
        } else {
            $default_value = '';
        }

        if( isset( $data['label_pos'] ) ) {
            $label_pos = $data['label_pos'];
        } else {
            $label_pos = "left";
        }

        if( isset( $data['label'] ) ) {
            $label = $data['label'];
        } else {
            $label = '';
        }





        $values     = array();
        $labels     = array();



        $i = 0;

        $args =  array(
            'post_type'      => array('product'),
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        );

        $allproducts = get_posts($args);

        foreach ( $allproducts as $post ) : setup_postdata( $post );
            $theid   = $post->ID;

            $product = new WC_Product($theid);

            $labels[$i] = $product->get_title();
            $values[$i] = '' . $theid;

            $i++;

        endforeach;
        wp_reset_postdata();


        ?>
        <select name="ninja_forms_field_<?php echo $field_id;?>" id="ninja_forms_field_<?php echo $field_id;?>" class="<?php echo $field_class;?>" rel="<?php echo $field_id;?>">
            <?php

            ?>
            <option value=""><?php esc_html_e('Select Product','ninjaformwooproductsfields');?></option>
            <?php


            foreach($labels as $k => $label){

                $value  = $values[$k];

                $value = htmlspecialchars( $value, ENT_QUOTES );
                $label = htmlspecialchars( $label, ENT_QUOTES );
                $label = stripslashes( $label );
                $label = str_replace( '&amp;', '&', $label );

                ?>
                <option value="<?php echo $value;?>"  >  <?php echo $label;?> </option>
                <?php
            }
            ?>
        </select>
        <?php
    }//end method ninja_forms_field_wooproducts_display
}

function ninjaform_wooproducts_loader()
{
    if(class_exists('Ninja_Forms') && function_exists('wc')){
        new CBXNinjaformWooProductsField();
    }
}//end function ninjaform_wooproducts_loader

add_action('plugins_loaded', 'ninjaform_wooproducts_loader');