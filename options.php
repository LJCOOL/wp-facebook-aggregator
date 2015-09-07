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

//register settings
add_action('admin_init','register_options');
//enqueue scripts
add_action( 'admin_enqueue_scripts', 'wpfa_scripts' );

//defines attributes to be saved
function register_options() {
    //sane quota for IDs
    register_setting('id-group','page-ID1');
    register_setting('id-group','page-ID2');
    register_setting('id-group','page-ID3');
    register_setting('id-group','page-ID4');
    register_setting('id-group','page-ID5');
    register_setting('id-group','page-ID6');
    register_setting('id-group','page-ID7');
    register_setting('id-group','page-ID8');
    register_setting('id-group','page-ID9');
    register_setting('id-group','page-ID10');
    register_setting('id-group','page-ID11');
    register_setting('id-group','page-ID12');
    register_setting('id-group','page-ID13');
    register_setting('id-group','page-ID14');
    register_setting('id-group','page-ID15');
    register_setting('id-group','page-ID16');
    register_setting('id-group','page-ID17');
    register_setting('id-group','page-ID18');
    register_setting('id-group','page-ID19');
    register_setting('id-group','page-ID20');
}

//add javascript file to be used by options page
function wpfa_scripts() {
    wp_enqueue_script( 'wpfa_javascript', plugin_dir_url( __FILE__ ) . 'wpfa_javascript.js' );
}

//HTML to generate page with forms, buttons etc.
function generate_page() { ?>
    <div class="wrap">
    <h2>WP Feed Aggregator Options</h2>
    <form method="post" action="options.php">
    <?php settings_fields( 'id-group' ); ?>
    <?php do_settings_sections( 'id-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Facebook Page ID 1</th>
        <td><input type="text" name="page-ID1" value="<?php echo esc_attr( get_option('page-ID1') ); ?>" />
        <i>eg. 123542974439976</i>  <a href="https://lookup-id.com/" target="_blank">Still don't know?</a></td>
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
    <button onclick="addField()">ID</button>
    <?php submit_button(); ?>
    </form>
    </div>
<?php } ?>
