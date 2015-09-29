<?php
/* Options vs Settings
Becase Wordpress' APIs use both terms,
For our sake we can define them as follows:
OPTIONS refer to the whole page somewhat like an entity in itself.
SETTINGS are the individual settings with the options page.
*/

//create custom plugin options menu
add_action('admin_menu', 'feed_options');

function feed_options() {
    //creates a top level menu
    add_menu_page('WP Feed Aggregator Options',
                  'Feed Aggregator Options',
                  'manage_options',
                  'wpfa-options',
                  'generate_page');
}

//defines attributes to be saved
function register_options() {
    //sane quota for IDs
    register_setting('wpfa-settings','page-ID1');
    register_setting('wpfa-settings','page-ID2');
    register_setting('wpfa-settings','page-ID3');
    register_setting('wpfa-settings','page-ID4');
    register_setting('wpfa-settings','page-ID5');
    register_setting('wpfa-settings','page-ID6');
    register_setting('wpfa-settings','page-ID7');
    register_setting('wpfa-settings','page-ID8');
    register_setting('wpfa-settings','page-ID9');
    register_setting('wpfa-settings','page-ID10');
    register_setting('wpfa-settings','page-ID11');
    register_setting('wpfa-settings','page-ID12');
    register_setting('wpfa-settings','page-ID13');
    register_setting('wpfa-settings','page-ID14');
    register_setting('wpfa-settings','page-ID15');
    register_setting('wpfa-settings','page-ID16');
    register_setting('wpfa-settings','page-ID17');
    register_setting('wpfa-settings','page-ID18');
    register_setting('wpfa-settings','page-ID19');
    register_setting('wpfa-settings','page-ID20');

    //settings for links, videos, images etc
    register_setting('wpfa-settings','wpfa-images');
    register_setting('wpfa-settings','wpfa-links');
    register_setting('wpfa-settings','wpfa-videos');
    register_setting('wpfa-settings','wpfa-redirect');
}

//add javascript file to be used by options page
function wpfa_scripts() {
    wp_register_script('wpfa_javascript', plugins_url('js/wpfa_javascript.js', __FILE__));
    wp_enqueue_script('wpfa_javascript');
}

//register settings
add_action('admin_init','register_options');
//enqueue scripts
add_action('admin_enqueue_scripts', 'wpfa_scripts');

//HTML to generate page with forms, buttons etc.
function generate_page() { ?>
    <div class="wrap">
    <h2>WP Feed Aggregator Options</h2>
    <hr>
    <h3 style="float:left">Facebook IDs</h3>
    <p style="padding-top:4px;padding-left:10px;float:left"><em>(Leave blank to remove)</em></p>
    <form method="post" action="options.php">
    <?php settings_fields( 'wpfa-settings' ); ?>
    <?php do_settings_sections( 'wpfa-settings' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Facebook Page ID 1</th>
        <td><input type="text" name="page-ID1" value="<?php echo esc_attr( get_option('page-ID1') ); ?>" />
        <em style="padding-left:5px">eg. 123542974439976 </em><a href="https://lookup-id.com/" target="_blank">Don't understand?</a></td>
        </tr>
        <tr valign="top">
        <th scope="row">Facebook Page ID 2</th>
        <td><input type="text" name="page-ID2" value="<?php echo esc_attr( get_option('page-ID2') ); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Facebook Page ID 3</th>
        <td><input type="text" name="page-ID3" value="<?php echo esc_attr( get_option('page-ID3') ); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Facebook Page ID 4</th>
        <td><input type="text" name="page-ID4" value="<?php echo esc_attr( get_option('page-ID4') ); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Facebook Page ID 5</th>
        <td><input type="text" name="page-ID5" value="<?php echo esc_attr( get_option('page-ID5') ); ?>" /></td>
        </tr>
    </table>
    <hr>
    <h3>Other options</h3>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Show images</th>
        <td><input type="checkbox" name="wpfa-images" disabled="disabled" checked="checked" value="1" <?php checked( '1', get_option('wpfa-images') ); ?> /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Get posts that are links</th>
        <td><input type="checkbox" name="wpfa-links" disabled="disabled" checked="checked" value="1" <?php checked( '1', get_option('wpfa-links') ); ?> /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Get posts that have videos</th>
        <td><input type="checkbox" name="wpfa-videos" disabled="disabled" checked="checked" value="1" <?php checked( '1', get_option('wpfa-videos') ); ?> /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Redirect featured link to category</th>
        <td><input type="checkbox" name="wpfa-redirect" disabled="disabled" checked="checked" value="1" <?php checked( '1', get_option('wpfa-redirect') ); ?> /></td>
        </tr>
    </table>
    <?php submit_button(); ?>
    </form>
    </div>
    <!-- <div class="input_fields_wrap">
        <button class="add_field_button">Add More Fields</button>
        <div><input type="text" name="mytext[]"></div>
    </div> -->
<?php } ?>
