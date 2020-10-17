<?php
/****************************
 *  PUMPING FEATURED INIT   *
 ****************************/

/**
 * Include necessary files
 */
require get_template_directory() . '/includes/pumping/pump_actions.php';
require get_template_directory() . '/includes/pumping/pump_settings.php';
require get_template_directory() . '/includes/pumping/pump_posttype.php';

/**
 * Init pump actions
 */
$qa_pump_actions = QA_Pump_Actions::get_instance();
$qa_pump_actions->init();

define('DEFAULT_FREE_PUMP_TIME', 3600);
define('DEFAULT_PREMIUM_PUMP_TIME', 120);

// Define time delay
$free_time_delay = (int)ae_get_option('free_time_delay', DEFAULT_FREE_PUMP_TIME);
$premium_time_delay = (int)ae_get_option('premium_time_delay', DEFAULT_PREMIUM_PUMP_TIME);
define('FREE_TIME_DELAY',$free_time_delay);
define('PREMIUM_TIME_DELAY', $premium_time_delay);

// Define time format
$time_delay_format = ae_get_option('time_delay_format', 'second');
define('TIME_DELAY_FORMAT', $time_delay_format);

// Setup page
//$setup_pump_link = et_get_page_link('setup-pump');
$process_payment_link = et_get_page_link('process-payment');

/**
 * Add pump scripts
 * @param void
 * @return void
 * @since 2.0
 * @author tatthien
 */
if(!function_exists('qa_init_pump_scripts')) {
    function qa_init_pump_scripts() {
        wp_enqueue_script('pumping', get_template_directory_uri() . '/js/pumping.js', array(
            'jquery',
            'underscore',
            'backbone',
            'appengine',
            'site-front'
        ), '1.0', true);

        // Include jquery.countdown
        wp_enqueue_script('jquery-plugin', get_template_directory_uri() . '/js/libs/jquery-countdown/jquery.plugin.min.js', array(), '1.0', true);

        wp_enqueue_script('jquery-countdown', get_template_directory_uri() . '/js/libs/jquery-countdown/jquery.countdown.min.js', array(), '1.0', true);
    }

    add_action('wp_enqueue_scripts', 'qa_init_pump_scripts');
}

if(!function_exists('qa_pump_run_setup_database')) {
    /**
     * Add pump meta data for questions on old version
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    function qa_pump_enqueue_admin_scripts() {
        if(isset($_GET['page']) && $_GET['page'] == 'et-pump') {
            // Script for setting up pump database
            wp_enqueue_script('pumping-setup', get_template_directory_uri() . '/js/pumping-setup.js', array(
                'jquery',
                'underscore',
                'backbone',
                'appengine',
            ), '1.0', true);
        }
    }

    add_action('admin_enqueue_scripts', 'qa_pump_enqueue_admin_scripts');
}

if(!function_exists('qa_pump_style_setup_database')) {
    function qa_pump_style_setup_database() {
        if(isset($_GET['page']) && $_GET['page'] == 'et-pump') {
            ?>
            <style>
                .pump_setup_data .group-desc {
                        border: 1px solid #FBBD08;
                        padding: 10px;
                        border-radius: 3px;
                        background: rgba(251, 189, 8, 0.22);
                        color: black;
                        margin-bottom: 10px;
                }

                .pump_setup_data button {
                    padding-right: 10px;
                }
            </style>
            <?php
                if(get_option('qa_pump_is_update')){
                ?>
                <style type="text/css">
                    .pump_setup_data{display: none;}
                </style>
                <?php
            }
        }
    }

    add_action('admin_footer', 'qa_pump_style_setup_database');
}


if(!function_exists('qa_get_package_by_sku')) {
    /**
     * Get package data by sku
     * @param string $sku
     * @return object $pack
     * @since 2.0
     * @author tatthien
     */
    function qa_get_package_by_sku($sku) {
        global $ae_post_factory;
        $post = $ae_post_factory->get('pump_pack');
        $packs = $post->fetch();
        foreach($packs as $pack) {
            if($pack->sku == $sku) {
               return $pack;
            }
        }
    }
}

/**
 * Convert time delay to second and countdown format
 * @param int $time_delay
 * @return array $data
 * @since 2.0
 * @author tatthien
 */
function qa_pump_data_convert($time_delay) {
    $second_convert = "";
    $countdown_format = "";
    switch(TIME_DELAY_FORMAT) {
        case 'second':
            $second_convert = $time_delay;
            $countdown_format = 'i:s';
            break;

        case 'minute':
            $second_convert = $time_delay * 60;
            $countdown_format = 'i:s';
            break;

        case 'hour':
            $second_convert = $time_delay * 3600;
            $countdown_format = 'H:i:s';
            break;

        case 'day':
            $second_convert = $time_delay * 86400;
            $countdown_format = 'd:H:i:s';
            break;
    }

    $data['second_convert'] = $second_convert;
    $data['countdown_format'] = $countdown_format;
    return $data;
}

function qa_render_pump_button($question) {
    // Show pump button on author page, poll single and question single
    if(ae_get_option('pump_action') !=="0" && (is_author() || is_singular('poll') || is_singular('question')) ){
        global $current_user;
        $author = $question->post_author;
        $pump_number = get_user_meta($author, 'et_pump_number', true);
        $timestamp = time();
        $item_pump_time = get_post_meta($question->ID, 'et_pump_time', true);
        //$item_pump_type = get_post_meta($question->ID, 'et_pump_type', true);
        $is_new_post = get_post_meta($question->ID, 'et_new_post', true);
        $time_delay = !empty($pump_number) ? PREMIUM_TIME_DELAY : FREE_TIME_DELAY;

        // Convert pump data
        $data = qa_pump_data_convert($time_delay);

        $item_pump_time = $item_pump_time + $data['second_convert'];
        $remain_time = abs($item_pump_time - $timestamp); //second
        if($author == $current_user->ID) {
            if(empty($item_pump_time) || $timestamp >= $item_pump_time || $is_new_post) {
                echo '<div class="pump-active">';
                echo '<span class="btn-pump" data-id="'. $question->ID .'"><i class="fa fa-arrow-up"></i>'. __('Pump', ET_DOMAIN) .'</span>';
                echo '<span class="btn-time hide"><i class="fa fa-clock-o"></i><span class="btn-time-content">00:00</span></span>';
                echo '</div>';
            } else {
                $time = gmdate($data['countdown_format'], $remain_time);
                echo '<div class="pump-deactive">';
                echo '<span class="btn-pump hide" data-id="'. $question->ID .'"><i class="fa fa-arrow-up"></i>'. __('Pump', ET_DOMAIN) .'</span>';
                echo '<span class="btn-time" data-countdown-time="'. $remain_time .'" data-countdown-format="'. $data['countdown_format'] .'"><i class="fa fa-clock-o"></i><span class="btn-time-content">00:00</span></span>';
                echo '</div>';
            }
        }
    }
}

/**
 * Class QA_Payment
 */
class QA_Payment extends AE_Payment
{
    function __construct() {
        $this->no_priv_ajax = array();
        $this->priv_ajax = array(
            'et-setup-payment'
        );
        $this->init_ajax();
    }

    public function get_plans() {
        global $ae_post_factory;
        $packageType = 'pump_pack';
        if( isset( $_POST['packageType'] ) && $_POST['packageType'] != '' ){
            $packageType = $_POST['packageType'];
        }
        $pack = $ae_post_factory->get( $packageType );
        return $pack->fetch();
    }
}

new QA_Payment();

/**
 * Admin update database notices
 */
 if(!function_exists('qa_pump_admin_notices')) {
    function qa_pump_admin_notices() {
        $is_updated = get_option('qa_pump_is_update');

        if(!$is_updated) {
            ?>
            <div id="qa_pump_setup_notice" class="update-nag notice">
                <?php
                    $setup_link = admin_url('admin.php?page=et-pump');
                    printf(__('The are new features are available on QAEngine version 2.0. Please <a href="%s">run update</a> only one time.', ET_DOMAIN), $setup_link);
                ?>
            </div>
            <?php
        }
    }

    add_action('admin_notices', 'qa_pump_admin_notices');
 }