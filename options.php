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
    $plugin_page=add_menu_page('WP Feed Aggregator Options',
                  'Feed Aggregator Options',
                  'manage_options',
                  'wpfa-options',
                  'generate_page');
    //hook to check if settings have changed and act accordingly
    add_action('admin_footer-'. $plugin_page, 'wpfa_checkOptions');
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

    //settings for links, videos, images etc
    register_setting('wpfa-settings','wpfa-images');
    register_setting('wpfa-settings','wpfa-links');
    register_setting('wpfa-settings','wpfa-videos');
}

//add javascript file to be used by options page
// function wpfa_scripts() {
//     wp_register_script('wpfa_javascript', plugins_url('js/wpfa_javascript.js', __FILE__));
//     wp_enqueue_script('wpfa_javascript');
// }

//register settings
add_action('admin_init','register_options');
//enqueue scripts
//add_action('admin_enqueue_scripts', 'wpfa_scripts');

//HTML to generate page with forms, buttons etc.
function generate_page() { ?>
<div class="wrap">
    <h2>WP Feed Aggregator Options</h2>
    <hr>
    <div style="display:inline-block">
        <h3>Facebook IDs</h3>
    </div>
    <div style="display:inline-block;padding-top:4px;padding-left:10px">
        <em>(Leave blank to remove)</em>
    </div>
    <div>
        <em>eg. 123542974439976 </em><a href="http://findmyfbid.com/" target="_blank">Find out the ID here</a>
    </div>
    <form method="post" action="options.php">
        <?php settings_fields( 'wpfa-settings' ); ?>
        <?php do_settings_sections( 'wpfa-settings' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Facebook Page ID 1</th>
                <td><input type="text" name="page-ID1" value="<?php echo esc_attr( get_option('page-ID1') ); ?>" />
                <th scope="row">Facebook Page ID 2</th>
                <td><input type="text" name="page-ID2" value="<?php echo esc_attr( get_option('page-ID2') ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Facebook Page ID 3</th>
                <td><input type="text" name="page-ID3" value="<?php echo esc_attr( get_option('page-ID3') ); ?>" /></td>
                <th scope="row">Facebook Page ID 4</th>
                <td><input type="text" name="page-ID4" value="<?php echo esc_attr( get_option('page-ID4') ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Facebook Page ID 5</th>
                <td><input type="text" name="page-ID5" value="<?php echo esc_attr( get_option('page-ID5') ); ?>" /></td>
                <th scope="row">Facebook Page ID 6</th>
                <td><input type="text" name="page-ID6" value="<?php echo esc_attr( get_option('page-ID6') ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Facebook Page ID 7</th>
                <td><input type="text" name="page-ID7" value="<?php echo esc_attr( get_option('page-ID7') ); ?>" /></td>
                <th scope="row">Facebook Page ID 8</th>
                <td><input type="text" name="page-ID8" value="<?php echo esc_attr( get_option('page-ID8') ); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Facebook Page ID 9</th>
                <td><input type="text" name="page-ID9" value="<?php echo esc_attr( get_option('page-ID9') ); ?>" /></td>
                <th scope="row">Facebook Page ID 10</th>
                <td><input type="text" name="page-ID10" value="<?php echo esc_attr( get_option('page-ID10') ); ?>" /></td>
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
        </table>
        <?php submit_button(); ?>
    </form>
</div>
<!-- <div class="input_fields_wrap">
    <button class="add_field_button">Add More Fields</button>
    <div><input type="text" name="mytext[]"></div>
</div> -->
<?php } ?>
