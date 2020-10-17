<?php
/****************************
 *  PUMP PACKAGE POST TYPE  *
 ****************************/
/**
 * Create pump packages
 * @param void
 * @return void
 * @since 2.0
 * @author tatthien
 */
if(!function_exists("qa_init_package")) {
    function qa_init_package() {
        register_post_type("pump_pack", array(
            'labels' => array(
                'name' => __('Pack', ET_DOMAIN) ,
                'singular_name' => __('Pack', ET_DOMAIN) ,
                'add_new' => __('Add New', ET_DOMAIN) ,
                'add_new_item' => __('Add New Pack', ET_DOMAIN) ,
                'edit_item' => __('Edit Pack', ET_DOMAIN) ,
                'new_item' => __('New Pack', ET_DOMAIN) ,
                'all_items' => __('All Packs', ET_DOMAIN) ,
                'view_item' => __('View Pack', ET_DOMAIN) ,
                'search_items' => __('Search Packs', ET_DOMAIN) ,
                'not_found' => __('No Pack found', ET_DOMAIN) ,
                'not_found_in_trash' => __('NoPacks found in Trash', ET_DOMAIN) ,
                'parent_item_colon' => '',
                'menu_name' => __('Packs', ET_DOMAIN)
            ) ,
            'public' => false,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => true,

            'capability_type' => 'post',
            'has_archive' => 'packs',
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array(
                'title',
                'editor',
                'author',
                'custom-fields'
            )
        ));

        $package = new AE_Pack('pump_pack',
            array(
                'sku',
                'et_price',
                'et_pump_number',
                'order'
            ),
            array(
                'backend_text' => array(
                    'text' => __('%s for %d pumps', ET_DOMAIN),
                    'data' => array(
                        'et_price',
                        'et_pump_number'
                    )
                )
            )
        );

        $pack_action = new AE_PackAction($package);

        global $ae_post_factory;
        $ae_post_factory->set('pump_pack', $package);
    }
}

add_action("init", "qa_init_package");