<?php
/**
 * Posttype for Poll Questions
 */
class QA_Poll_Questions_Posttype extends AE_Posts
{
    public static $instance;

    /**
     * Get instance
     * @param void
     * @return object $instance
     * @since 2.0
     * @author tatthien
     */
    public static function get_instance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct($taxs = array(), $meta_data = array(), $localize = array()) {
        $post_type = 'poll';
        parent::__construct($post_type, $taxs, $meta_data, $localize);

        $this->taxs = array(
            'qa_tag',
            'question_category'
        );
        $this->meta_data = array(
            'poll_end_date',
            'poll_multi_time',
            'poll_multi_choice'
        );
        $this->convert = array(
            'poll_end_date',
            'poll_multi_time',
            'poll_multi_choice'
        );
    }

    /**
     * Init method for class QA_Poll_Questions_Posttype
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    public function init() {
        $this->qa_register_poll_questions_posttype();
    }

    /**
     * Register new poll posttype
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    public function qa_register_poll_questions_posttype() {
        $labels = array(
            'name' => __('Poll Questions', ET_DOMAIN) ,
            'singular_name' => __('Poll Question', ET_DOMAIN) ,
            'add_new' => __('Add new', ET_DOMAIN) ,
            'add_new_item' => __('Add new', ET_DOMAIN) ,
            'edit_item' => __('Edit poll question', ET_DOMAIN) ,
            'new_item' => __('New poll question', ET_DOMAIN) ,
            'view_item' => __('View poll question', ET_DOMAIN) ,
            'search_items' => __('Search poll questions', ET_DOMAIN) ,
            'not_found' => __('No poll questions found', ET_DOMAIN) ,
            'not_found_in_trash' => __('No poll questions found in Trash', ET_DOMAIN) ,
            'parent_item_colon' => __('Parent poll question:', ET_DOMAIN) ,
            'menu_name' => __('Poll Questions', ET_DOMAIN) ,
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'show_in_nav_menus' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'has_archive' => 'polls',
            'query_var' => true,
            'can_export' => true,
            'rewrite' => array(
                'slug' => 'poll'
            ) ,
            'capability_type' => 'post',
            'supports' => array(
                'title',
                'editor',
                'author',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'trackbacks',
                'comments',
                'revisions',
                'page-attributes',
                'post-formats'
            ),
            'menu_icon' => 'dashicons-chart-pie'
        );
        register_post_type('poll', $args);

        // Add Question taxonomies to Poll Question
        register_taxonomy_for_object_type('qa_tag', 'poll');
        register_taxonomy_for_object_type('question_category', 'poll');


        global $ae_post_factory;
        $ae_post_factory->set('poll', new AE_Posts('poll', $this->taxs, $this->meta_data));
    }
}

/**
 * Posttype for Poll Answers
 */
class QA_Poll_Answers_Posttype extends AE_Posts
{
    public static $instance;

    /**
     * Get instance
     * @param void
     * @return object $instance
     * @since 2.0
     * @author tatthien
     */
    public static function get_instance() {
        if(!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct($taxs = array(), $meta_data = array(), $localize = array()) {
        $post_type = 'poll_answer';
        parent::__construct($post_type, $taxs, $meta_data, $localize);

        $this->taxs = array();
        $this->meta_data = array(
            'poll_answer_color',
            'poll_answer_vote'
        );
        $this->convert = array(
            'poll_answer_color',
            'poll_answer_vote'
        );
    }

    /**
     * Init method for class QA_Poll_Questions_Posttype
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    public function init() {
        $this->qa_register_poll_answers_posttype();
    }

    /**
     * Register new poll_question posttype
     * @param void
     * @return void
     * @since 2.0
     * @author tatthien
     */
    public function qa_register_poll_answers_posttype() {
        $labels = array(
            'name' => __('Poll Answers', ET_DOMAIN) ,
            'singular_name' => __('Poll Answer', ET_DOMAIN) ,
            'add_new' => __('Add new', ET_DOMAIN) ,
            'add_new_item' => __('Add new', ET_DOMAIN) ,
            'edit_item' => __('Edit poll answer', ET_DOMAIN) ,
            'new_item' => __('New poll answer', ET_DOMAIN) ,
            'view_item' => __('View poll answer', ET_DOMAIN) ,
            'search_items' => __('Search poll answers', ET_DOMAIN) ,
            'not_found' => __('No poll answers found', ET_DOMAIN) ,
            'not_found_in_trash' => __('No poll answers found in Trash', ET_DOMAIN) ,
            'parent_item_colon' => __('Parent poll answer:', ET_DOMAIN) ,
            'menu_name' => __('Poll Answers', ET_DOMAIN) ,
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_admin_bar' => true,
            'menu_position' => 5,
            'show_in_nav_menus' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'has_archive' => 'poll-answer',
            'query_var' => true,
            'can_export' => true,
            'rewrite' => array(
                'slug' => 'poll-answer'
            ) ,
            'capability_type' => 'post',
            'supports' => array(
                'title',
                'editor',
                'author',
                'thumbnail',
                'excerpt',
                'custom-fields',
                'trackbacks',
                'comments',
                'revisions',
                'page-attributes',
                'post-formats'
            ),
            'menu_icon' => 'dashicons-chart-pie'
        );
        register_post_type('poll_answer', $args);


        global $ae_post_factory;
        $ae_post_factory->set('poll_answer', new AE_Posts('poll_answer', $this->taxs, $this->meta_data));
    }
}