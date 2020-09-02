<?php

include dirname(__FILE__) . "/../vendor/autoload.php";
require_once dirname(__FILE__) . '/class-sqhr-setttings-page.php';

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Response\QrCodeResponse;
use Endroid\QrCode\QrCode;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://twitter.com/syedqarib
 * @since      1.0.0
 *
 * @package    Sqhr_Link_Qr_Code
 * @subpackage Sqhr_Link_Qr_Code/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Sqhr_Link_Qr_Code
 * @subpackage Sqhr_Link_Qr_Code/includes
 * @author     Qarib Haider <qaribhaider@gmail.com>
 */
class Sqhr_Link_Qr_Code
{

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Setting page object holder
     */
    protected $settings;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->version = (defined('SQHR_LINK_QR_CODE_VERSION')) ? SQHR_LINK_QR_CODE_VERSION : '1.0.0';
        $this->plugin_name = 'sqhr-link-qr-code';
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        // Do the magic
        $this->setup_settings_page();
        $this->hooks();
    }

    private function setup_settings_page(){
        $this->settings = new SQHR_QR_CODE_Settings_Setup();
        $this->settings->run();
    }

    private function hooks()
    {
        add_action('add_meta_boxes', [$this, 'metabox_qr_code_setup']);
    }

    public function metabox_qr_code_setup()
    {
        $options = $this->settings->get_options();
        $screens = (isset($options['sqhr_qrcode_post_types'])) ? $options['sqhr_qrcode_post_types'] : $this->get_registered_post_types();

        foreach ($screens as $screen) {
            add_meta_box(
                $this->get_plugin_name() . '-meta-qrcode', // Unique ID
                esc_html__('QR Code', $this->get_plugin_name()), // Box title
                [$this, 'metabox_qr_code_html'], // Content callback, must be of type callable
                $screen, // Post type
                'side',
                'core'
            );
        }
    }

    public function metabox_qr_code_html($post)
    {
        // Setup options
        $options = $this->settings->get_options();
        $qroptions = [];
        $qroptions['size'] = (isset($options['sqhr_qrcode_size'])) ? $options['sqhr_qrcode_size'] : 300;
        $qroptions['margin'] = (isset($options['sqhr_qrcode_margin'])) ? $options['sqhr_qrcode_margin'] : "20";
        $qroptions['image_format'] = (isset($options['sqhr_qrcode_image_format'])) ? $options['sqhr_qrcode_image_format'] : 'png';

        // Post permalink
        $link = get_permalink();

        // Create a basic QR code
        $qrCode = new QrCode($link);

        // Set options
        $qrCode->setSize($qroptions['size']);
        $qrCode->setWriterByName($qroptions['image_format']);
        $qrCode->setMargin($qroptions['margin']);
        $qrCode->setEncoding('UTF-8');
        $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::HIGH());
        $qrCode->setForegroundColor(['r' => 0, 'g' => 0, 'b' => 0, 'a' => 0]);
        $qrCode->setBackgroundColor(['r' => 255, 'g' => 255, 'b' => 255, 'a' => 0]);
        $qrCode->setValidateResult(false);

        $dataUri = $qrCode->writeDataUri();

        include_once dirname(__FILE__) . '/../views/metabox-qrcode.php';
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

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}
