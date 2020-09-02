<?php

include_once dirname(__FILE__) . "/../vendor/autoload.php";

class SQHR_QR_CODE_Settings_Setup
{
    private $page_title;
    private $prefix;

    public function __construct()
    {
        $this->page_title = "QR Code Settings";
        $this->prefix = "sqhrqrcsp";
    }

    public function get_options()
    {
        return get_option($this->prefix . '_settings', true);
    }

    public function run()
    {
        $panel = new \TDP\OptionsKit($this->prefix);
        $panel->set_page_title(__($this->page_title));

        $this->hooks();
    }

    private function hooks()
    {
        add_filter('sqhrqrcsp_menu', [$this, 'sqhrqrcsp_setup_menu']);
        add_filter('sqhrqrcsp_settings_tabs', [$this, 'sqhrqrcsp_register_settings_tabs']);
        //add_filter('sqhrqrcsp_registered_settings_sections', [$this, 'sqhrqrcsp_register_settings_subsections']);
        add_filter('sqhrqrcsp_registered_settings', [$this, 'sqhrqrcsp_register_settings']);
    }

    /**
     * Setup the menu for the options panel.
     *
     * @param array $menu
     *
     * @return array
     */
    public function sqhrqrcsp_setup_menu($menu)
    {
        // These defaults can be customized
        // $menu['parent'] = 'options-general.php';
        $menu['page_title'] = __($this->page_title);
        $menu['menu_title'] = $menu['page_title'];
        // $menu['capability'] = 'manage_options';

        return $menu;
    }

    /**
     * Register settings tabs.
     *
     * @param array $tabs
     *
     * @return array
     */
    public function sqhrqrcsp_register_settings_tabs($tabs)
    {
        return array(
            'general' => __('General'),
        );
    }

    /**
     * Register settings subsections (optional)
     *
     * @param array $subsections
     *
     * @return array
     */
    public function sqhrqrcsp_register_settings_subsections($subsections)
    {
        return $subsections;
    }

    /**
     * Register settings fields for the options panel.
     *
     * @param array $settings
     *
     * @return array
     */
    public function sqhrqrcsp_register_settings($settings)
    {
        $post_types = $this->get_registered_post_types();
        $settings = array(
            'general' => array(
                array(
                    'id' => 'sqhr_qrcode_size',
                    'name' => __('Size'),
                    'desc' => __('Set QR Code size in pixels (note that margin will be added on this) - Default 300'),
                    'type' => 'text',
                    'std' => 300,
                ),
                array(
                    'id' => 'sqhr_qrcode_margin',
                    'name' => __('Margin'),
                    'desc' => __('Set QR Code margin in pixels - Default 20'),
                    'type' => 'text',
                    'std' => 20,
                ),
                array(
                    'id' => 'sqhr_qrcode_image_format',
                    'name' => __('Image Type'),
                    'desc' => __('Select the image file type - Default PNG'),
                    'type' => 'select',
                    'options' => [
                        "png" => "PNG",
                        "svg" => "SVG",
                    ],
                    'std' => "png",
                ),
                array(
                    'id' => 'sqhr_qrcode_post_types',
                    'name' => __('Post Types'),
                    'desc' => __('Select which post types to add the QR Code on'),
                    'type' => 'multicheckbox',
                    'options' => $post_types,
                    'std' => implode(",", $post_types),
                ),
            ),
        );

        return $settings;
    }

    private function get_registered_post_types()
    {
        $get_cpt_args = array(
            'public' => true,
            '_builtin' => true,
        );
        $post_types = get_post_types($get_cpt_args, 'names');

        if (isset($post_types['attachment'])) {
            unset($post_types['attachment']);
        }

        return $post_types;
    }

}
