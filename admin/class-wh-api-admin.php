<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://whello.id/
 * @since      1.0.0
 *
 * @package    Wh_Api
 * @subpackage Wh_Api/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wh_Api
 * @subpackage Wh_Api/admin
 * @author     Whello Indonesia <dev@whello.id>
 */
class Wh_Api_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/* ---------------------------- Declare variable ---------------------------- */
	private $adminfields;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		/* ----------------------------- Assign Variable ---------------------------- */
		$this->adminfields = $this->admin_fields();


		add_action('wp_ajax_wh_api_update_json', [$this, 'wh_api_ajax_update_json']);

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wh_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wh_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wh-api-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wh_Api_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wh_Api_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wh-api-admin.js', array( 'jquery' ), $this->version, false );

	}

	/* -------------------------------------------------------------------------- */
	/*                              Lists all fields                              */
	/* -------------------------------------------------------------------------- */
	public function admin_fields(){
		$fields = array(
			array(
				'id'	=> 'email',
				'type'	=> 'email',
				'label'	=> __('Email', 'whello'),
				'class' => 'code',
			),
			array(
				'id'	=> 'password',
				'type'	=> 'password',
				'label'	=> __('Password', 'whello'),
				'class' => 'code',

			),
			array(
				'id'	=> 'authUrl',
				'type'	=> 'text',
				'label'	=> __('authURL', 'whello'),
				'class' => 'code',
			),
			array(
				'id'	=> 'apiKey',
				'type'	=> 'text',
				'label'	=> __('apiKey', 'whello'),
				'class' => 'code',

			),
		);

		return apply_filters( 'wh_api_setting_fields', $fields );
	}
	
	/* -------------------------------------------------------------------------- */
	/*                              Create Admin Menu                             */
	/* -------------------------------------------------------------------------- */
	public function wh_api_admin_menu() { 

		/* -------------------------------- Main Menu ------------------------------- */
		add_menu_page( 
			'WH API', 
			'WH API', 
			'edit_posts', 
			'wh_api', 
			[$this, 'wh_api_admin_menu_content'], 
			'dashicons-media-spreadsheet' 

		   );
		
		/* --------------------------------- Submenu -------------------------------- */
		add_submenu_page(
			'wh_api',
			__( 'Mapping Data', 'whello' ),
			__( 'Mapping Data', 'whello' ),
			'manage_options',
			'wh_api_mapping',
			[$this, 'wh_api_mapping_data_callback']
		);
	  }

	  /* ------------------ Callback Function Admin Menu Content ------------------ */
	  public function wh_api_admin_menu_content(){
		?>
		<h1> <?php esc_html_e( 'Whello API.', 'whello' ); ?> </h1>
			 <form method="POST" action="options.php">
				<?php settings_fields( 'wh-api-settings' );
					do_settings_sections( 'wh-api-settings' ); ?>
					<h3><?php echo __('API Environtment', 'whello') ?></h3>
					<table id="wh-api-settings" class="form-table">
					<?php foreach ($this->adminfields as $key => $field) {
						echo '<tr>'.$this->generate_field($field['type'], $field).'</tr>';
					} ?>
					</table>
		
				<h3><?php echo __('API Settings', 'whello') ?></h3>
				<table class="form-table">
					<tr>
						<th class="row"><?php echo __('First update time', 'whello') ?></th>
						<td>
							<input type="text" name="wh_api_first_update" class="regular-text code" placeholder="6:00" value="<?php echo get_option('wh_api_first_update') ?>">
							<p class="description">Enter the 24-hours time format eg. <?php echo current_time('H:i'); ?></p>
						</td>
					</tr>
					<tr>
					<tr>
						<th class="row"><?php echo __('Update period (hours)', 'whello') ?></th>
						<td> 
							<input type="number" name="wh_api_update_period" class="regular-text code" placeholder="6" value="<?php echo get_option('wh_api_update_period') ?>"> 
						</td>
	    			</tr>
					<tr>
						<th class="row"><?php echo __('Manual Update JSON File', 'whello') ?></th>
						<td>
							<?php 
								$uploads = wp_upload_dir();
								$upload_url = $uploads['baseurl'];
								$upload_path = $uploads['basedir'];

								$json_url = $upload_url .'/wh-api.json';
							?>
							<div style="display:flex;">
								<button class="button" id="wh_api_update_json" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wh-api-json' )); ?>"><?php echo __('Update JSON', 'whello') ?></button>
							</div>
							<p class="description">
								<?php 
									if (file_exists($upload_path .'/wh-api.json')) {
										echo 'JSON file: <a href="'.$json_url.'">'.$json_url.'</a>';
									} 
								?>
							</p>
						</td>
	    			</tr>						
				</table>
		 <?php submit_button(); ?>
		</form>
		<?php
	}

	/* --------------------- Callback function Mapping Data --------------------- */
	public function wh_api_mapping_data_callback(){
		?>
		<h1> <?php esc_html_e( 'Mapping Data.', 'whello' ); ?> </h1>
			<form method="POST" action="options.php">
			<?php settings_fields( 'wh-api-settings' );
					do_settings_sections( 'wh-api-settings' ); ?>
					<table id="wh-api-mapping" class="form-table">
					</table>
					<?php submit_button(); ?>
			</form>
		<?php
	}

	/* -------------------------------------------------------------------------- */
	/*                              Register Setings                              */
	/* -------------------------------------------------------------------------- */
	public function wh_api_settings_init() {

		foreach ($this->adminfields as $key => $field) {
	    	register_setting( 'wh-api-settings', 'wh_api_'.$field['id'] );
	    }
		register_setting( 'wh-api-settings', 'wh_api_first_update' );
    	register_setting( 'wh-api-settings', 'wh_api_update_period' );
    	
	}

	/* -------------------------------------------------------------------------- */
	/*                               Generate Fields                              */
	/* -------------------------------------------------------------------------- */
	public function generate_field($type = 'text', $field){
		$value = get_option('wh_api_'.$field['id']);
		$class = isset($field['class']) ? $field['class'] : '';
		$text = array('text', 'email', 'phone', 'password');
		if (in_array($type, $text)) {
			$input ='<th scope="row"><label for="wh_api_'.$field['id'].'">'.$field['label'].'</label></th>';
			$input .= '<td><input type="'.$type.'" name="wh_api_'.$field['id'].'" id="wh_api_'.$field['id'].'" class="regular-text '.$class.'" value="'.$value.'"/></td>';
		} else if ($text == 'select') {
			$input ='<th scope="row"><label for="wh_api_'.$field['id'].'">'.$field['label'].'</label></th>';
			$input .= '<td><select name="wh_api_'.$field['id'].'">';
			if (!empty($field['options'])) {
				foreach ($field['options'] as $key => $option) {
					$selected = ($value == $key) ? 'selected' : '';
					$input .= '<option value="'.$key.'" '.$selected.'>'.$option.'</option>';
				}
			}
			$input .= '</select></td>';
		} else if ($type == 'page') {
			$input ='<th scope="row"><label for="wh_api_'.$field['id'].'">'.$field['label'].'</label></th>';
			$input = '<td>'.wp_dropdown_pages( array( 'name' => 'wh_api_'.$field['id'], 'id' => 'wh_api_'.$field['id'], 'selected' => get_option('wh_api_'.$field['id']) ) ).'</td>';
		} else if ($type == 'checkbox') {
			 $selected = ($value) ? 'checked' : '';
			 $input ='<th scope="row"><label for="wh_api_'.$field['id'].'">'.$field['label'].'</label></th>';

		}

		return $input;
	}

	public function wh_api_ajax_update_json(){
		
		$apikey = get_option('wh_api_apiKey');
		$email = get_option('wh_api_email');
		$password = get_option('wh_api_password');
		$authurl = get_option('wh_api_authUrl');

		$results = array();

		$client = new Client([
		    'base_uri' => $authurl,
		    'timeout'  => 2.0,
		]);

		try {
			$response = $client->request('POST', $authurl, [
				    'headers' => [
				        'Content-Type' => 'application/json'
				    ]
			    ]
			);

			$body = $response->getBody();
			$arr_body = json_decode($body);

			$results['success'] = true;

		} catch (RequestException $e) {
			$results['success'] = false;
			
		}

		echo wp_send_json($results);
		
		wp_die();
	}
	
	

}
