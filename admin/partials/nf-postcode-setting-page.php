<?php


?>
<div class="wrap">
    <h2>Ninja Forms - Postcode.nl API connection</h2>

    <form method="post" action="options.php">
        <?php settings_fields( 'nf-plugin-settings-group' ); ?>
        <?php do_settings_sections( 'nf-plugin-settings-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Postcode.nl - API Key</th>
                <td><input type="text" name="nf_api_key" value="<?php echo esc_attr( get_option('nf_api_key') ); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Postcode.nl - API Secret</th>
                <td><input type="text" name="nf_api_secret" value="<?php echo esc_attr( get_option('nf_api_secret') ); ?>" /></td>
            </tr>

            <tr valign="top">
                <th scope="row">Webreact - License Key</th>
                <td><input type="text" name="nf_webreact_license" value="<?php echo esc_attr( get_option('nf_webreact_license') ); ?>" /></td>
            </tr>
        </table>

        <?php submit_button(); ?>

    </form>
</div>