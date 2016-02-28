<?php

/**
 * The class holding all the logic for the 'Azure AD' settings page used to configure the plugin.
 *
 * Partially generated by the WordPress Option Page generator
 * at http://jeremyhixon.com/wp-tools/option-page/
 */
class AADSSO_Settings_Page {

	private $settings;
	
	/**
	 * The option page's hook_suffix returned from add_options_page
	 */ 
	private $options_page_id;

	public function __construct() {

		// Ensure jQuery is loaded.
		add_action( 'admin_enqueue_scripts', array( $this, 'maybe_include_jquery' ) );

		// Add the 'Azure AD' options page.
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );

		// Register the settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Reset settings if requested to.
		add_action( 'admin_init', array( $this, 'maybe_reset_settings' ) );

		// If settings were reset, show confirmation.
		add_action( 'all_admin_notices', array( $this, 'notify_if_reset_successful' ) );

		// Load stored configuration values (or defaults).
		$this->settings = get_option( 'aadsso_settings', AADSSO_Settings::get_defaults() );
	}

	/**
	 * Clears settings if $_GET['aadsso_nonce'] is set and if the nonce is valid.
	 */
	public function maybe_reset_settings()
	{
		$should_reset_settings = isset( $_GET['aadsso_nonce'] )
		                          && wp_verify_nonce( $_GET['aadsso_nonce'], 'aadsso_reset_settings' );
		if ( $should_reset_settings ) {
			delete_option( 'aadsso_settings' );
			wp_redirect( admin_url( 'options-general.php?page=aadsso_settings&aadsso_reset=success' ) );
		}
	}

	/**
	 * Notifies user if settings reset was successful.
	 */
	public function notify_if_reset_successful()
	{
		if ( isset( $_GET['aadsso_reset'] ) && 'success' === $_GET['aadsso_reset'] ) {
			echo '<div id="message" class="notice notice-warning"><p>'
				. __( 'Single Sign-on with Azure Active Directory settings have been reset to default.',
				      AADSSO )
				.'</p></div>';
		}
	}

	/**
	 * Adds the 'Azure AD' options page.
	 */
	public function add_options_page() {
		$this->options_page_id = add_options_page(
			__( 'Azure Active Directory Settings', AADSSO ), // page_title
			'Azure AD', // menu_title
			'manage_options', // capability
			'aadsso_settings', // menu_slug
			array( $this, 'render_admin_page' ) // function
		);
	}

	/**
	 * Renders the 'Azure AD' settings page.
	 */
	public function render_admin_page() {
		require_once( 'view/settings.php' );
	}

	/**
	 * Registers settings, sections and fields.
	 */
	public function register_settings() {

		register_setting(
			'aadsso_settings', // option_group
			'aadsso_settings', // option_name
			array( $this, 'sanitize_settings' ) // sanitize_callback
		);

		add_settings_section(
			'aadsso_settings_general', // id
			__( 'General', AADSSO ), // title
			array( $this, 'settings_general_info' ), // callback
			'aadsso_settings_page' // page
		);

		add_settings_field(
			'org_display_name', // id
			__( 'Display name', AADSSO ), // title
			array( $this, 'org_display_name_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);

		add_settings_field(
			'org_domain_hint', // id
			__( 'Domain hint', AADSSO ), // title
			array( $this, 'org_domain_hint_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);

		add_settings_field(
			'client_id', // id
			__( 'Client ID', AADSSO ), // title
			array( $this, 'client_id_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);

		add_settings_field(
			'client_secret', // id
			__( 'Client secret', AADSSO ), // title
			array( $this, 'client_secret_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);

		add_settings_field(
			'redirect_uri', // id
			__( 'Redirect URL', AADSSO ), // title
			array( $this, 'redirect_uri_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);

		add_settings_field(
			'logout_redirect_uri', // id
			__( 'Logout redirect URL', AADSSO ), // title
			array( $this, 'logout_redirect_uri_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);

		add_settings_field(
			'field_to_match_to_upn', // id
			__( 'Field to match to UPN', AADSSO ), // title
			array( $this, 'field_to_match_to_upn_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);

		add_settings_field(
			'enable_auto_provisioning', // id
			__( 'Enable auto-provisioning', AADSSO ), // title
			array( $this, 'enable_auto_provisioning_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);

		add_settings_field(
			'enable_auto_forward_to_aad', // id
			__( 'Enable auto-forward to Azure AD', AADSSO ), // title
			array( $this, 'enable_auto_forward_to_aad_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);

		add_settings_field(
			'enable_aad_group_to_wp_role', // id
			__( 'Enable Azure AD group to WP role association', AADSSO ), // title
			array( $this, 'enable_aad_group_to_wp_role_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);

		add_settings_field(
			'default_wp_role', // id
			__( 'Default WordPress role if not in Azure AD group', AADSSO ), // title
			array( $this, 'default_wp_role_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);

		add_settings_field(
			'role_map', // id
			__( 'WordPress role to Azure AD group map', AADSSO ), // title
			array( $this, 'role_map_callback' ), // callback
			'aadsso_settings_page', // page
			'aadsso_settings_general' // section
		);
	}

	/**
	 * Gets the array of roles determined by other plugins to be "editable".
	 */
	function get_editable_roles() {

		global $wp_roles;

		$all_roles = $wp_roles->roles;
		$editable_roles = apply_filters( 'editable_roles', $all_roles );

		return $editable_roles;
	}


	/**
	 * Cleans and validates form information before saving.
	 *
	 * @param array $input key-value information to be cleaned before saving.
	 *
	 * @return array The sanitized and valid data to be stored.
	 */
	public function sanitize_settings( $input ) {

		$sanitary_values = array();

		$text_fields = array(
			'org_display_name',
			'org_domain_hint',
			'client_id',
			'client_secret',
			'redirect_uri',
			'logout_redirect_uri',
		);

		foreach ($text_fields as $text_field) {
			if ( isset( $input[ $text_field ] ) ) {
				$sanitary_values[ $text_field ] = sanitize_text_field( $input[ $text_field ] );
			}
		}

		// Default field_to_match_to_upn is 'email'
		$sanitary_values['field_to_match_to_upn'] = 'email';
		if ( isset( $input['field_to_match_to_upn'] )
		      && in_array( $input['field_to_match_to_upn'], array( 'email', 'login' ) )
		) {
			$sanitary_values['field_to_match_to_upn'] = $input['field_to_match_to_upn'];
		}

		// Default role for user that is not member of any Azure AD group is null, which denies access.
		$sanitary_values['default_wp_role'] = null;
		if ( isset( $input['default_wp_role'] ) ) {
			$sanitary_values['default_wp_role'] = sanitize_text_field( $input['default_wp_role'] );
		}

		// Booleans: when key == value, this is considered true, otherwise false.
		$boolean_settings = array(
			'enable_auto_provisioning',
			'enable_auto_forward_to_aad',
			'enable_aad_group_to_wp_role',
		);
		foreach ( $boolean_settings as $boolean_setting )
		{
			if( isset( $input[ $boolean_setting ] ) ) {
				$sanitary_values[ $boolean_setting ] = ( $boolean_setting == $input[ $boolean_setting ] );
			} else {
				$sanitary_values[ $boolean_setting ] = false;
			}
		}

		/*
		 * Many of the roles in WordPress will not have Azure AD groups associated with them.
		 * Go over all roles, removing the mapping for empty ones.
		 */
		if ( isset( $input['role_map'] ) ) {
			foreach( $input['role_map'] as $role_slug => $group_object_id ) {
				if( empty( $group_object_id ) ) {
					unset( $input['role_map'][ $role_slug ] );
				}
			}
			$sanitary_values['role_map'] = $input['role_map'];
		}

		return $sanitary_values;
	}

	/**
	 * Renders details for the General settings section.
	 */
	public function settings_general_info() { }

	/**
	 * Renders the `role_map` picker control.
	 */
	function role_map_callback() {
		printf( '<p>%s</p>',
			__( 'Map WordPress roles to Azure Active Directory groups.', AADSSO )
		);
		echo '<table>';
		printf(
			'<thead><tr><th>%s</th><th>%s</th></tr></thead>',
			__( 'WordPress Role', AADSSO ),
			__( 'Azure AD Group Object ID', AADSSO )
		);
		echo '<tbody>';
		foreach( $this->get_editable_roles( ) as $role_slug => $role ) {
			echo '<tr>';
				echo '<td>' . htmlentities( $role['name'] ) . '</td>';
				echo '<td>';
					printf(
						'<input type="text" class="regular-text" name="aadsso_settings[role_map][%1$s]" '
						 . 'id="role_map_%1$s" value="%2$s" />',
						$role_slug,
						isset( $this->settings['role_map'][ $role_slug ] )
							? esc_attr( $this->settings['role_map'][ $role_slug ] )
							: ''
					);
				echo '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
	}

	/**
	 * Renders the `org_display_name` form control.
	 */
	public function org_display_name_callback()  {
		$this->render_text_field( 'org_display_name' );
		printf(
			'<p class="description">%s</p>',
			__( 'Display Name will be shown on the WordPress login screen.', AADSSO )
		);
	}

	/**
	 * Renders the `org_domain_hint` form control.
	 */
	public function org_domain_hint_callback()  {
		$this->render_text_field( 'org_domain_hint' );
		printf(
			'<p class="description">%s</p>',
			__( 'Provides a hint to Azure AD about the domain or tenant they will be logging in to. If '
			     . 'the domain is federated, the user will be automatically redirected to federation '
			     . 'endpoint.', AADSSO )
		);
	}

	/**
	 * Renders the `client_id` form control
	 */
	public function client_id_callback() {
		$this->render_text_field( 'client_id' );
		printf(
			'<p class="description">%s</p>',
			__( 'The client ID of the Azure AD application representing this blog.', AADSSO )
		);
	}

	/**
	 * Renders the `client_secret` form control
	 **/
	public function client_secret_callback() {
		$this->render_text_field( 'client_secret' );
		printf(
			'<p class="description">%s</p>',
			__( 'A secret key for the Azure AD application representing this blog.', AADSSO )
		);
	}

	/**
	 * Renders the `redirect_uri` form control
	 **/
	public function redirect_uri_callback() {
		$this->render_text_field( 'redirect_uri' );
		printf(
			' <a href="#" onclick="jQuery(\'#redirect_uri\').val(\'%s\'); return false;">%s</a>' 
			. '<p class="description">%s</p>',
			wp_login_url(),
			__( 'Set default' ),
			__( 'The URL where the user is redirected to after authenticating with Azure AD. '
			  . 'This URL must be registered in Azure AD as a valid redirect URL, and it must be a '
			  . 'page that invokes the "authenticate" filter. If you don\'t know what to set, leave '
			  . 'the default value (which is this blog\'s login page).', AADSSO )
		);
	}

	/**
	 * Renders the `logout_redirect_uri` form control
	 **/
	public function logout_redirect_uri_callback() {
		$this->render_text_field( 'logout_redirect_uri' );
		printf(
			' <a href="#" onclick="jQuery(\'#logout_redirect_uri\').val(\'%s\'); return false;">%s</a>'  
			. '<p class="description">%s</p>',
			wp_login_url(),
			__( 'Set default' ),
			__( 'The URL where the user is redirected to after signing out of Azure AD. '
			  . 'This URL must be registered in Azure AD as a valid redirect URL. (This does not affect '
			  . ' logging out of the blog, it is only used when logging out of Azure AD.)', AADSSO )
		);
	}

	/**
	 * Renders the `field_to_match_to_upn` form control.
	 */
	public function field_to_match_to_upn_callback() {
		$selected =
		 isset( $this->settings['field_to_match_to_upn'] )
		  ? $this->settings['field_to_match_to_upn']
			: '';
		?>
		<select name="aadsso_settings[field_to_match_to_upn]" id="field_to_match_to_upn">
			<option value="email"<?php echo $selected == 'email' ? ' selected="selected"' : ''; ?>>
				<?php echo __( 'Email Address', AADSSO ); ?>
			</option>
			<option value="login"<?php echo $selected == 'login' ? ' selected="selected"' : ''; ?>>
				<?php echo __( 'Login Name', AADSSO ); ?>
			</option>
		</select>
		<?php
		printf(
			'<p class="description">%s</p>',
			__('This specifies the WordPress user field which will be used to match to the Azure AD user\'s '
			 . 'UserPrincipalName. Email Address is fine for most instances.', AADSSO)
		);
	}

	/**
	 * Renders the `default_wp_role` control.
	 */
	public function default_wp_role_callback() {

		// Default configuration should be most-benign
		if( ! isset( $this->settings['default_wp_role'] ) ) {
			$this->settings['default_wp_role'] = '';
		}

		echo '<select name="aadsso_settings[default_wp_role]" id="default_wp_role">';
		printf( '<option value="%s">%s</option>', '', '(None, deny access)' );
		foreach( $this->get_editable_roles() as $role_slug => $role ) {
			$selected = $this->settings['default_wp_role'] === $role_slug ? ' selected="selected"' : '';
			printf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $role_slug ), $selected, htmlentities( $role['name'] )
			);
		}
		echo '</select>';
		printf(
			'<p class="description">%s</p>',
			__('This is the default role that users will be assigned to if matching Azure AD group to '
			 . 'WordPress roles is enabled.', AADSSO)
		);
	}

	/**
	 * Renders the `enable_auto_provisioning` checkbox control.
	 */
	public function enable_auto_provisioning_callback() {
		$this->render_checkbox_field(
			'enable_auto_provisioning',
			__( 'Automatically create WordPress users, if needed, for authenticated Azure AD users.',
			    AADSSO )
		);
	}

	/**
	 * Renders the `enable_auto_forward_to_aad` checkbox control.
	 */
	public function enable_auto_forward_to_aad_callback() {
		$this->render_checkbox_field(
			'enable_auto_forward_to_aad',
			__( 'Automatically forward users to the Azure AD to sign in, skipping the WordPress login screen.',
			    AADSSO)
		);
	}

	/**
	 * Renders the `enable_aad_group_to_wp_role` checkbox control.
	 */
	public function enable_aad_group_to_wp_role_callback() {
		$this->render_checkbox_field(
			'enable_aad_group_to_wp_role',
			__( 'Automatically assign WordPress user roles based on Azure AD group membership.',
			    AADSSO )
		);
	}

	/**
	 * Renders a simple text field and populates it with the setting value.
	 *
	 * @param string $name The setting name for the text input field.
	 */
	public function render_text_field( $name ) {
		$value = isset( $this->settings[ $name ] ) ? esc_attr( $this->settings[ $name ] ) : '';
		printf(
			'<input class="regular-text" type="text" '
			 . 'name="aadsso_settings[%1$s]" id="%1$s" value="%2$s" />',
			$name, $value
		);
	}

	/**
	 * Renders a simple checkbox field and populates it with the setting value.
	 *
	 * @param string $name The setting name for the checkbox input field.
	 * @param string $label The label to use for the checkbox.
	 */
	public function render_checkbox_field( $name, $label ) {
		printf(
			'<input type="checkbox" name="aadsso_settings[%1$s]" id="%1$s" value="%1$s"%2$s />'
			 . '<label for="%1$s">%3$s</label>',
			$name,
			isset( $this->settings[ $name ] ) && $this->settings[ $name ] ? 'checked' : '',
			$label
		);
	}

	/**
	 * Indicates if user is currently on this settings page.
	 */
	public function is_on_options_page() {
		$screen = get_current_screen();
		return $screen->id === $this->options_page_id;
	} 

	/**
	 * Ensures jQuery is loaded
	 */
	public function maybe_include_jquery() {
		if ( $this->is_on_options_page() ) {
			wp_enqueue_script( 'jquery' );
		}
	}
}
