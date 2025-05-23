<?php

namespace Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib;

use Barn2\Plugin\Easy_Post_Types_Fields\Dependencies\Lib\Plugin\Plugin;
use WP_Error;
use WP_Filesystem_Base;
use function WP_Filesystem;
/**
 * Utility functions for Barn2 plugins.
 *
 * @package   Barn2\barn2-lib
 * @author    Barn2 Plugins <support@barn2.com>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 * @version   1.6
 */
class Util
{
    const BARN2_API_URL = 'https://api.barn2.com';
    const BARN2_URL = 'https://barn2.com';
    const EDD_STORE_URL = 'https://barn2.com';
    const KNOWLEDGE_BASE_URL = 'https://barn2.com';
    /**
     * Formats a HTML link to a path on the Barn2 site.
     *
     * @param string  $relative_path The path relative to https://barn2.com.
     * @param string  $link_text     The link text.
     * @param boolean $new_tab       Whether to open the link in a new tab.
     * @return string The hyperlink.
     */
    public static function barn2_link($relative_path, $link_text = '', $new_tab = \false)
    {
        if (empty($link_text)) {
            $link_text = __('Read more', 'barn2');
        }
        return self::format_link(self::barn2_url($relative_path), \esc_html($link_text), $new_tab);
    }
    public static function barn2_url($relative_path)
    {
        return \esc_url(\trailingslashit(self::BARN2_URL) . \ltrim($relative_path, '/'));
    }
    public static function barn2_api_url($relative_path)
    {
        return \esc_url(\trailingslashit(self::BARN2_API_URL) . \ltrim($relative_path, '/'));
    }
    public static function format_barn2_link_open($relative_path, $new_tab = \false)
    {
        return self::format_link_open(self::barn2_url($relative_path), $new_tab);
    }
    public static function format_link($url, $link_text, $new_tab = \false)
    {
        return \sprintf('%1$s%2$s</a>', self::format_link_open($url, $new_tab), $link_text);
    }
    public static function format_link_open($url, $new_tab = \false)
    {
        $target = $new_tab ? ' target="_blank"' : '';
        return \sprintf('<a href="%1$s"%2$s>', \esc_url($url), $target);
    }
    /**
     * Format a Barn2 store URL.
     *
     * @param string $relative_path The relative path
     */
    public static function store_url($relative_path)
    {
        return self::EDD_STORE_URL . '/' . \ltrim($relative_path, ' /');
    }
    public static function format_store_link($relative_path, $link_text, $new_tab = \true)
    {
        return self::format_link(self::store_url($relative_path), $link_text, $new_tab);
    }
    public static function format_store_link_open($relative_path, $new_tab = \true)
    {
        return self::format_link_open(self::store_url($relative_path), $new_tab);
    }
    public static function get_add_to_cart_url($download_id, $price_id = 0, $discount_code = '')
    {
        $args = ['edd_action' => 'add_to_cart', 'download_id' => (int) $download_id];
        if ($price_id) {
            $args['edd_options[price_id]'] = (int) $price_id;
        }
        if ($discount_code) {
            $args['discount'] = $discount_code;
        }
        return self::store_url('?' . \http_build_query($args));
    }
    /**
     * Returns true if the current request is a WP admin request.
     *
     * @return bool true if the current page is in WP admin
     */
    public static function is_admin()
    {
        return \is_admin();
    }
    /**
     * Returns true if the current request is a front-end request, e.g. viewing a page or post.
     *
     * @return bool true if the current page is front-end
     */
    public static function is_front_end()
    {
        return (!\is_admin() || \defined('DOING_AJAX')) && !\defined('DOING_CRON');
    }
    /**
     * Returns true if WooCommerce is active.
     *
     * @return bool true if active
     */
    public static function is_woocommerce_active()
    {
        return \class_exists('WooCommerce');
    }
    /**
     * Returns true if WooCommerce Product Addons is active.
     *
     * @return bool true if active
     */
    public static function is_product_addons_active()
    {
        return \class_exists('WC_Product_Addons');
    }
    /**
     * Returns true if EDD is active.
     *
     * @return bool true if active
     */
    public static function is_edd_active()
    {
        return \class_exists('Easy_Digital_Downloads');
    }
    /**
     * Returns true if Advanced Custom Fields or Advanced Custom Fields Pro is active.
     *
     * @return bool true if active
     */
    public static function is_acf_active()
    {
        return \class_exists('ACF');
    }
    /**
     * Returns true if the plugin instance returned by $function is an active Barn2 plugin.
     *
     * @param string $function The function that returns the plugin instance
     * @return bool true if active
     * @since 1.5.3
     */
    public static function is_barn2_plugin_active($function)
    {
        if (\function_exists($function)) {
            $instance = $function();
            return \method_exists((object) $instance, 'has_valid_license') && $instance->has_valid_license();
        }
        return \false;
    }
    /**
     * Returns true if WooCommerce Protected Categories is active and has a valid license.
     *
     * @return bool true if active
     * @deprecated 1.5.3 Use `is_barn2_plugin_active( '\Barn2\Plugin\WC_Protected_Categories\wpc' )` instead
     */
    public static function is_protected_categories_active()
    {
        \_deprecated_function(__FUNCTION__, '1.5.3', 'is_barn2_plugin_active');
        return self::is_barn2_plugin_active('\\Barn2\\Plugin\\WC_Protected_Categories\\wpc');
    }
    /**
     * Returns true if WooCommerce Product Table is active and has a valid license.
     *
     * @return bool true if active
     * @deprecated 1.5.3 Use `is_barn2_plugin_active( '\Barn2\Plugin\WC_Product_Table\wpt' )` instead
     */
    public static function is_product_table_active()
    {
        \_deprecated_function(__FUNCTION__, '1.5.3', 'is_barn2_plugin_active');
        return self::is_barn2_plugin_active('\\Barn2\\Plugin\\WC_Product_Table\\wpt');
    }
    /**
     * Returns true if WooCommerce Quick View Pro is active and has a valid license.
     *
     * @return bool true if active
     * @deprecated 1.5.3 Use `is_barn2_plugin_active( '\Barn2\Plugin\WC_Quick_View_Pro\wqv' )` instead
     */
    public static function is_quick_view_pro_active()
    {
        \_deprecated_function(__FUNCTION__, '1.5.3', 'is_barn2_plugin_active');
        return self::is_barn2_plugin_active('\\Barn2\\Plugin\\WC_Quick_View_Pro\\wqv');
    }
    /**
     * Returns true if WooCommerce Restaurant Ordering is active and has a valid license.
     *
     * @return bool true if active
     * @deprecated 1.5.3 Use `is_barn2_plugin_active( '\Barn2\Plugin\WC_Restaurant_Ordering\wro' )` instead
     */
    public static function is_restaurant_ordering_active()
    {
        \_deprecated_function(__FUNCTION__, '1.5.3', 'is_barn2_plugin_active');
        return self::is_barn2_plugin_active('\\Barn2\\Plugin\\WC_Restaurant_Ordering\\wro');
    }
    /**
     * Returns true if WooCommerce Fast Cart is active and has a valid license.
     *
     * @return bool true if active
     * @deprecated 1.5.3 Use `is_barn2_plugin_active( '\Barn2\Plugin\WC_Fast_Cart\wfc' )` instead
     */
    public static function is_fast_cart_active()
    {
        \_deprecated_function(__FUNCTION__, '1.5.3', 'is_barn2_plugin_active');
        return self::is_barn2_plugin_active('\\Barn2\\Plugin\\WC_Fast_Cart\\wfc');
    }
    /**
     * Get the script suffix used when registering/queuing JS and CSS, based on SCRIPT_DEBUG.
     *
     * @return string Returns '' or '.min'
     */
    public static function get_script_suffix()
    {
        return \defined('SCRIPT_DEBUG') && \SCRIPT_DEBUG ? '' : '.min';
    }
    /**
     * Register the Services in the given array.
     *
     * @param array $services The services to register
     */
    public static function register_services($services)
    {
        \array_map(function ($service) {
            if ($service instanceof Conditional && !$service->is_required()) {
                return;
            }
            if ($service instanceof Registerable) {
                $service->register();
            }
            if ($service instanceof Schedulable) {
                $service->schedule();
            }
        }, $services);
    }
    /**
     * Format a Barn2 store URL.
     *
     * @param string $relative_path The relative path
     * @deprecated 1.5 Renamed store_url
     */
    public static function format_store_url($relative_path)
    {
        return self::store_url($relative_path);
    }
    /**
     * Retrieves an array of internal WP dependencies for bundled JS files.
     *
     * @param Barn2\Lib\Plugin $plugin
     * @param string           $filename The filepath of the JS file relative to the plugin's 'js' directory. Also supports supplying the full path to the file relative to the plugin root.
     * @return array
     */
    public static function get_script_dependencies($plugin, $filename)
    {
        $script_dependencies_file = $plugin->get_dir_path() . 'assets/js/wp-dependencies.json';
        $script_dependencies = \file_exists($script_dependencies_file) ? \file_get_contents($script_dependencies_file) : \false;
        // bail if the wp-dependencies.json file doesn't exist
        if ($script_dependencies === \false) {
            return ['dependencies' => [], 'version' => ''];
        }
        $script_dependencies = \json_decode($script_dependencies, \true);
        // if the asset doesn't exist, and the path is relative to the 'js' directory then try a full path
        if (!isset($script_dependencies[$filename]) && \strpos($filename, './assets/js') === \false && isset($script_dependencies[\sprintf('./assets/js/%s', $filename)])) {
            $filename = \sprintf('./assets/js/%s', $filename);
        }
        if (!isset($script_dependencies[$filename])) {
            return ['dependencies' => [], 'version' => ''];
        }
        return $script_dependencies[$filename];
    }
    /**
     * Create a page and store the ID in an option. (adapted from WooCommerce)
     *
     * @param mixed  $slug         Slug for the new page.
     * @param string $option       Option name to store the page's ID.
     * @param string $page_title   (default: '') Title for the new page.
     * @param string $page_content (default: '') Content for the new page.
     * @param int    $post_parent  (default: 0) Parent for the new page.
     * @return int page ID.
     */
    public static function create_page($slug, $option = '', $page_title = '', $page_content = '', $post_parent = 0)
    {
        global $wpdb;
        $slug = \esc_sql($slug);
        $option_value = \get_option($option);
        if ($option_value > 0) {
            $page_object = \get_post($option_value);
            if ($page_object && 'page' === $page_object->post_type && !\in_array($page_object->post_status, ['pending', 'trash', 'future', 'auto-draft'], \true)) {
                // Valid page is already in place.
                return $page_object->ID;
            }
        }
        if (\strlen($page_content) > 0) {
            // Search for an existing page with the specified page content (typically a shortcode).
            $shortcode = \str_replace(['<!-- wp:shortcode -->', '<!-- /wp:shortcode -->'], '', $page_content);
            $valid_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' ) AND post_content LIKE %s LIMIT 1;", "%{$shortcode}%"));
        } else {
            // Search for an existing page with the specified page slug.
            $valid_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='page' AND post_status NOT IN ( 'pending', 'trash', 'future', 'auto-draft' )  AND post_name = %s LIMIT 1;", $slug));
        }
        if ($valid_page_found) {
            if ($option) {
                \update_option($option, $valid_page_found);
            }
            return $valid_page_found;
        }
        // Search for a matching valid trashed page.
        if (\strlen($page_content) > 0) {
            // Search for an existing page with the specified page content (typically a shortcode).
            $trashed_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='page' AND post_status = 'trash' AND post_content LIKE %s LIMIT 1;", "%{$page_content}%"));
        } else {
            // Search for an existing page with the specified page slug.
            $trashed_page_found = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type='page' AND post_status = 'trash' AND post_name = %s LIMIT 1;", $slug));
        }
        if ($trashed_page_found) {
            $page_id = $trashed_page_found;
            $page_data = ['ID' => $page_id, 'post_status' => 'publish'];
            \wp_update_post($page_data);
        } else {
            $page_data = ['post_status' => 'publish', 'post_type' => 'page', 'post_author' => 1, 'post_name' => $slug, 'post_title' => $page_title, 'post_content' => $page_content, 'post_parent' => $post_parent, 'comment_status' => 'closed'];
            $page_id = \wp_insert_post($page_data);
        }
        if ($option) {
            \update_option($option, $page_id);
        }
        return $page_id;
    }
    /**
     * Similar to wp_kses_post but with added support for <img> srcset and sizes attributes.
     *
     * @param string $string The string to sanitize.
     * @return string The sanitized string.
     * @see https://core.trac.wordpress.org/ticket/29807
     */
    public static function barn2_kses_post(string $string)
    {
        $allowed_html = \wp_kses_allowed_html('post');
        if (isset($allowed_html['img'])) {
            $allowed_html['img']['srcset'] = \true;
            $allowed_html['img']['sizes'] = \true;
        }
        return \wp_kses($string, $allowed_html);
    }
    /**
     * Get the plugin data from the plugin header
     *
     * @param Plugin $plugin
     * @return array The plugin data from the plugin header
     * @since      1.5.4
     * @deprecated 1.5.5 Use Simple_Plugin::get_plugin_data() instead
     */
    public static function get_plugin_data(Plugin $plugin)
    {
        if (!\function_exists('get_plugin_data')) {
            require_once \ABSPATH . 'wp-admin/includes/plugin.php';
        }
        return \get_plugin_data($plugin->get_file(), \false, \false);
    }
    /**
     * Loops through all active plugins on the user's website and returns ones that are authored by Barn2
     *
     * @param bool $include_inactive Whether to include inactive plugins in the search. Default is `false`.
     *
     * @return array List of plugin meta data and the ITEM_ID found in each Barn2 plugin
     */
    public static function get_installed_barn2_plugins($include_inactive = \false)
    {
        if (!\function_exists('get_plugins')) {
            require_once \ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugin_dir = \WP_PLUGIN_DIR;
        $current_plugins = \get_plugins();
        $barn2_installed = [];
        foreach ($current_plugins as $slug => $data) {
            if (\false !== \stripos($data['Author'], 'Barn2 Plugins')) {
                if ($include_inactive) {
                    $folder = \dirname($slug);
                    if (\is_readable("{$plugin_dir}/{$folder}/src/Plugin.php")) {
                        $plugin_contents = \file_get_contents("{$plugin_dir}/{$folder}/src/Plugin.php");
                        if (\preg_match('/const\\s+ITEM_ID\\s*=\\s*(\\d+);/', $plugin_contents, $item_id)) {
                            $data['ITEM_ID'] = \absint($item_id[1]);
                        }
                    }
                    $barn2_installed[] = $data;
                    continue;
                }
                if (\is_readable("{$plugin_dir}/{$slug}")) {
                    $plugin_contents = \file_get_contents("{$plugin_dir}/{$slug}");
                    if (\preg_match('/namespace ([0-9A-Za-z_\\\\]+);/', $plugin_contents, $namespace)) {
                        $classname = $namespace[1] . '\\Plugin';
                        if (\class_exists($classname) && \defined("{$classname}::ITEM_ID")) {
                            if ($id = $classname::ITEM_ID ?? null) {
                                $data['ITEM_ID'] = \absint($id);
                                $barn2_installed[] = $data;
                            }
                        }
                    }
                }
            }
        }
        return $barn2_installed;
    }
    /**
     * Check if a plugin is installed on the WordPress site (whether active or not).
     *
     * @param string $plugin_file The plugin file relative to the plugins directory. e.g. my-plugin/my-plugin.php
     * @return bool
     */
    public static function is_plugin_installed($plugin_file)
    {
        if (!\function_exists('get_plugins')) {
            require_once \ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $plugins = \get_plugins();
        if (\strpos($plugin_file, '/') !== \false) {
            return isset($plugins[$plugin_file]);
        }
        foreach ($plugins as $plugin_path => $plugin_data) {
            if (\basename($plugin_path) === $plugin_file) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Sanitize anything.
     *
     * @param mixed $var the thing to sanitize.
     * @return mixed
     */
    public static function clean($var)
    {
        if (\is_array($var)) {
            return \array_map([__CLASS__, 'clean'], $var);
        } else {
            return \is_scalar($var) ? \sanitize_text_field($var) : $var;
        }
    }
    /**
     * Declare compatibility with the High-Performance Order Storage
     * feature in WooCommerce.
     *
     * @param string $plugin_entry_file The main plugin file.
     * @param bool   $compatible        Whether the plugin is compatible with HPOS.
     * @return void
     * @deprecated 1.6.0 HPOS compatibility is handled automatically by Simple_Plugin so this function is not needed.
     */
    public static function declare_hpos_compatibility($plugin_entry_file, $compatible = \true)
    {
        \_deprecated_function(__METHOD__, '1.6.0', 'Handled automatically by Simple_Plugin');
        \add_action('before_woocommerce_init', function () use($plugin_entry_file, $compatible) {
            if (\class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', $plugin_entry_file, $compatible);
            }
        });
    }
    /**
     * Get the link to a plugin on wordpress.org.
     *
     * @param string $plugin_name
     * @param string $plugin_slug
     * @return string
     */
    public static function get_plugin_link($plugin_name, $plugin_slug)
    {
        return \sprintf('<a href="%1$s">%2$s</a>', 'https://wordpress.org/plugins/' . $plugin_slug, $plugin_name);
    }
    /**
     * Get the link to install, activate or upgrade a plugin.
     *
     * @param string $plugin_name     The name of the plugin.
     * @param string $plugin_slug     The slug of the plugin.
     * @param string $plugin_basename The basename of the plugin (e.g. 'woocommerce/woocommerce.php').
     * @param string $action          The action to perform on the plugin (install, activate or upgrade).
     * @return string
     */
    public static function get_plugin_install_activate_upgrade_link($plugin_name, $plugin_slug, $plugin_basename, $action = null)
    {
        if (\is_wp_error(\validate_plugin($plugin_basename)) || $action === 'install') {
            $action = 'install-plugin';
            $command = 'Install';
            $page = 'update.php';
            $file = $plugin_slug;
            $nonce_key = "{$action}_{$file}";
        } elseif (\is_plugin_inactive($plugin_basename) || $action === 'activate') {
            $action = 'activate';
            $command = 'Activate';
            $page = 'plugins.php';
            $file = $plugin_basename;
            $nonce_key = "{$action}-plugin_{$file}";
        } elseif ($action === 'upgrade') {
            $action = 'upgrade-plugin';
            $command = 'Upgrade';
            $page = 'update.php';
            $file = $plugin_basename;
            $nonce_key = "{$action}_{$file}";
        }
        // there is no `else` clause here because there shouldn't be other cases
        // if `$action` is still `null`, then the function will return an empty string
        if (\is_null($action)) {
            return '';
        }
        $plugin_install_activate_link = \wp_nonce_url(\add_query_arg(
            ['action' => $action, 'plugin' => $file],
            // on multisites, the installation of a plugin must happen on the network admin, hence the use of `self_admin_url()`
            $action === 'install' ? \self_admin_url($page) : \admin_url($page)
        ), $nonce_key);
        return \sprintf(' <a href="%1$s">%2$s</a>', $plugin_install_activate_link, "{$command} {$plugin_name}");
    }
    /**
     * Install the bonus plugin.
     *
     * @param array $bonus_plugins A list of bonus plugins to install.
     *                             Each plugin is an object with the following properties:
     * 						       - id:   The ID of the EDD download post for the plugin.
     * 						       - name: The name of the plugin.
     * 						       - url:  The URL of the plugin ZIP file.
     * 
     * @return array The results of the installation (either true or a WP_Error).
     */
    public static function install_bonus_plugins($bonus_plugins)
    {
        include_once \ABSPATH . 'wp-admin/includes/file.php';
        include_once \ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        include_once \ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once \ABSPATH . 'wp-admin/includes/plugin.php';
        $skin = new \WP_Ajax_Upgrader_Skin();
        $upgrader = new \Plugin_Upgrader($skin);
        $results = [];
        foreach ($bonus_plugins as $plugin) {
            $name = $plugin->name;
            $result = $upgrader->run(['package' => $plugin->url, 'destination' => \WP_PLUGIN_DIR]);
            if (\is_wp_error($result)) {
                $results[$name] = new WP_Error('bonus_download_install_failed', $result->get_error_message(), $result->get_error_data());
                continue;
            } else {
                if (\is_wp_error($skin->result)) {
                    $results[$name] = new WP_Error('bonus_download_install_failed', $skin->result->get_error_message(), $skin->result->get_error_data());
                    continue;
                } else {
                    if ($skin->get_errors()->get_error_code()) {
                        $results[$name] = new WP_Error('bonus_download_install_failed', $skin->get_error_messages(), $skin->get_errors()->get_error_data());
                        continue;
                    } else {
                        if (\is_null($result)) {
                            WP_Filesystem();
                            global $wp_filesystem;
                            $error_message = __('Unable to connect to the filesystem. Please confirm your credentials.', 'barn2-lib');
                            if ($wp_filesystem instanceof WP_Filesystem_Base && \is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
                                $error_message = \esc_html($wp_filesystem->errors->get_error_message());
                            }
                            $results[$name] = new WP_Error('bonus_download_install_failed', $error_message);
                            continue;
                        }
                    }
                }
            }
            if (isset($result['destination_name'])) {
                $plugin = "{$result['destination_name']}/{$result['destination_name']}.php";
            } else {
                $plugin = '';
            }
            if ($plugin && \current_user_can('activate_plugin', $plugin)) {
                $cache_plugins = \wp_cache_get('plugins', 'plugins');
                if (!empty($cache_plugins)) {
                    $new_plugin = \get_plugin_data(\WP_PLUGIN_DIR . '/' . $plugin, \false, \false);
                    $cache_plugins[''][$plugin] = $new_plugin;
                    \wp_cache_set('plugins', $cache_plugins, 'plugins');
                }
                $result = \activate_plugin($plugin);
                if (\is_wp_error($result)) {
                    $results[$name] = new WP_Error('bonus_download_activation_failed', $result->get_error_message(), $result->get_error_data());
                    continue;
                }
            } else {
                $results[$name] = new WP_Error('bonus_download_no_activation_permission', esc_html__('You don\'t have permission to activate the plugin.', 'barn2-lib'));
                continue;
            }
            $results[$name] = \true;
        }
        return $results;
    }
}
