<?php
/**
 * Plugin Name: Latepoint Dev Updater
 * Description: Simple GitHub release updater for Latepoint.dev plugins.
 * Version: 1.0.0
 * Author: latepoint.dev
 * License: GPL-2.0+
 *
 * Usage Example:
 * require_once plugin_dir_path(__FILE__) . 'lpdu-pluginname.php';
 * new LPDU_Updater(__FILE__, 'github-username', 'github-repo');
 */

if (!class_exists('LPDU_Updater')) {

    class LPDU_Updater {
        private $plugin_file;
        private $github_user;
        private $github_repo;
        private $current_version;
        private $cache_key;

        public function __construct($plugin_file, $github_user, $github_repo) {
            $this->plugin_file     = $plugin_file;
            $this->github_user     = $github_user;
            $this->github_repo     = $github_repo;
            $this->current_version = $this->get_plugin_version();
            $this->cache_key       = 'lpdu_' . md5($this->github_user . $this->github_repo);

            add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
            add_filter('plugins_api', [$this, 'plugins_api_handler'], 10, 3);
        }

        /**
         * Get plugin version from header.
         */
        private function get_plugin_version() {
            $plugin_data = get_file_data($this->plugin_file, ['Version' => 'Version'], false);
            return $plugin_data['Version'];
        }

        /**
         * Fetch GitHub release info (cached for 6 hours).
         */
        private function get_github_data() {
            $cached = get_transient($this->cache_key);
            if ($cached) return $cached;

            $url = "https://api.github.com/repos/{$this->github_user}/{$this->github_repo}/releases/latest";
            $response = wp_remote_get($url, [
                'timeout' => 15,
                'headers' => ['Accept' => 'application/vnd.github.v3+json', 'User-Agent' => 'WordPress-LPDU']
            ]);

            if (is_wp_error($response)) return false;

            $data = json_decode(wp_remote_retrieve_body($response));
            if (!empty($data->tag_name)) {
                set_transient($this->cache_key, $data, 6 * HOUR_IN_SECONDS);
            }

            return $data;
        }

        /**
         * Check for updates.
         */
        public function check_for_update($transient) {
            if (empty($transient->checked)) return $transient;

            $data = $this->get_github_data();
            if (!$data) return $transient;

            $latest_version = ltrim($data->tag_name, 'v');
            if (version_compare($this->current_version, $latest_version, '<')) {
                $plugin_slug = plugin_basename($this->plugin_file);

                $obj = new stdClass();
                $obj->slug        = dirname($plugin_slug);
                $obj->new_version = $latest_version;
                $obj->url         = $data->html_url;
                $obj->package     = $data->zipball_url;

                $transient->response[$plugin_slug] = $obj;
            }

            return $transient;
        }

        /**
         * Plugin details in the WP update modal.
         */
        public function plugins_api_handler($false, $action, $response) {
            if ($action !== 'plugin_information') return $false;

            $data = $this->get_github_data();
            if (!$data) return $false;

            $info = new stdClass();
            $info->name          = $this->github_repo;
            $info->slug          = $response->slug ?? $this->github_repo;
            $info->version       = ltrim($data->tag_name, 'v');
            $info->author        = '<a href="https://latepoint.dev">latepoint.dev</a>';
            $info->homepage      = $data->html_url;
            $info->download_link = $data->zipball_url;
            $info->sections      = ['description' => $data->body ?? 'GitHub plugin update.'];

            return $info;
        }
    }
}
