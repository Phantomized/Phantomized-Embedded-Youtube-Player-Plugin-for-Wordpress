<?php
if (! class_exists('EmbeddedYTPlayer')) :

class Embedded_YT_Player
{
    protected static $_instance;

    protected const SETTINGS_DEFAULT = array(
      'width' => '100%',
      'height' => '100%',
      'disable_controls' => 'false',
      'autoplay' => 'false',
      'loop' => 'false'
    );

    protected $players = array(); // Players - array(id, args)
    protected function __construct()
    {
        add_action('wp_enqueue_scripts', array( $this, 'load_scripts' ));
        add_action('wp_footer', array($this, 'load_localized_scripts'));
    }

    /**
     * Main EmbeddedYTPlayer Instance
     * Ensures only one instance of EmbeddedYTPlayer is loaded or can be loaded.
     * @static
     * @return EmbeddedYTPlayer - Main instance
     */
    public static function instance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function load_scripts()
    {
        wp_enqueue_style('EYTP-style', EYTP_URL . '/assets/css/common.css', array(), EYTP_VERSION);
        wp_enqueue_script('EYTP-script', EYTP_URL . '/assets/js/frontend.js', array(), EYTP_VERSION, true);
    }

    public function load_localized_scripts()
    {
        //Localize the player array
        wp_localize_script('EYTP-script', 'eytPlayerData', $this->players);
    }

    /**
    * Returns the player html content and assigns it to be set up by the
    * the frontend.
    * @param array $settings - Array of player settings
    * @return string player arguments in JSON format
    **/
    public function render_player($settings)
    {
        $settings = array_merge(self::SETTINGS_DEFAULT, $settings);
        $player_arguments = $this->get_player_arguments_json($settings);
        if (null === $player_arguments) {
            return;
        } //If no video, abort

        $player_event_settings = $this->get_player_event_settings_json($settings);

        //Get current player count and use it as ID
        $player_count = count($this->players);

        //Create player array
        $this->players[] = array(
            'id' => count($this->players),
            'args' => $player_arguments,
            'eventSettings' => $player_event_settings
        );

        //Return the player html
        return <<<EOT
            <div class="eytp-wrapper">
                <div id="ytplayer_$player_count"></div>
            </div>
EOT;
    }
    /**
    * Returns a JSON string holding the arguments for the youtube API,
    * based on settings given
    * @param array $settings - Array of player settings
    * @return string player arguments in JSON format
     */
    private function get_player_arguments_json($settings)
    {
        if (!$settings['video_id']) {
            return;
        } //Return if no video id given.
        $arg_array = array(
            'videoId' => $settings['video_id'],
            'height' => $settings['height'],
            'width' => $settings['width']
        );
        //** Player vars **
        $var_array = array(
          'modestBranding' => '1',
          'origin' => $_SERVER['HTTP_ORIGIN']
        );

        //If controls disabled
        if ('true' == $settings['disable_controls']) {
            $var_array['controls'] = '0';
            $var_array['disablekb'] = '1';
        }

        $arg_array['playerVars'] = $var_array;

        $arg_array['events'] = array(
            'onReady' => 'eytpOnPlayerReady'
        );

        return json_encode($arg_array);
    }
    /**
    * Returns a JSON string holding a list of settings for events
    * and functionality not supported by youtube's native parameters
    * themselves.
    * @param array $settings - Array of player settings
    * @return string player arguments in JSON format
    */
    private function get_player_event_settings_json($settings)
    {
        $event_array = array();

        $event_array['autoplay'] = ('true' == $settings['autoplay']);

        $event_array['disable_controls'] = ('true' == $settings['disable_controls']);

        $event_array['loop'] = ('true' == $settings['loop']);
        return json_encode($event_array);
    }
}
function EmbeddedYTPlayer()
{
    return Embedded_YT_Player::instance();
}
endif;
