<?php
class afo_pdf_settings {

	static $title = 'Settings';
	static $title1 = 'Select Post Types Where You Want Custom PDF Attachments';
	
	function __construct() {
		$this->load_settings();
	}
	
	function custom_pdf_attachment_post_data(){
		if(isset($_POST['option']) and $_POST['option'] == "save_custom_pdf_attachment_settings"){
			if(isset($_POST['attachment_post_types'])){
				update_option( 'saved_post_types_for_pdf_attachment', $_POST['attachment_post_types'] );
			}
			update_option( 'saved_no_of_pdf_attachment', $_POST['no_of_pdf_attachment'] );
		}
	}
	
	function post_types_selected($saved_types=''){
		$args = array(
		'public'   => true,
		);
		$post_types = get_post_types( $args, 'names' ); 
		$post_types = array_diff($post_types, array('attachment'));
		foreach ( $post_types as $post_type ) {
			if(is_array($saved_types) and in_array($post_type,$saved_types)){
				echo '<p><input type="checkbox" name="attachment_post_types[]" value="'.$post_type.'" checked="checked" />&nbsp;'.$post_type.'</p>';
			} else{
				echo '<p><input type="checkbox" name="attachment_post_types[]" value="'.$post_type.'" />&nbsp;'.$post_type.'</p>';
			}
		}
	}
	
	function get_no_of_pdf_files_selected($saved_no_of_pdf_attachment=''){
		$ret .= '<option value="">--</option>';
		for($i=1; $i<=10;$i++){
			if($saved_no_of_pdf_attachment == $i){
				$ret .= '<option value="'.$i.'" selected="selected">'.$i.'</option>';
			} else {
				$ret .= '<option value="'.$i.'">'.$i.'</option>';
			}
		}
		return $ret;
	}
	
	function help_info($saved_no_of_pdf_attachment=''){
		if($saved_no_of_pdf_attachment > 0 and $saved_no_of_pdf_attachment <= 10){
			echo '<table width="100%" border="0">';
			for($i=1; $i<=$saved_no_of_pdf_attachment;$i++){
				echo '<tr>
					<td><strong>Shortcode :</strong> <span style="color:#FF0000">[pdf_attachment file="'.$i.'" name="optional file_name"]</span></td>
					<td><strong>Custom function :</strong> <span style="color:#FF0000">&lt;?php echo pdf_attachment_file('.$i.',"optional file_name");?&gt;</span></td>
				  </tr>';
			}
			echo '</table>';
		}
	}
	
	function  custom_pdf_attachment_options () {
		global $wpdb;
		
		$saved_types = get_option('saved_post_types_for_pdf_attachment');
		$saved_no_of_pdf_attachment = get_option('saved_no_of_pdf_attachment');
		$this->donate_form_pdf();
		?>
		<form name="f" method="post" action="">
		<input type="hidden" name="option" value="save_custom_pdf_attachment_settings" />
		<table width="100%" border="0">
		  <tr>
			<td><h1><?php echo self::$title;?></h1></td>
		  </tr>
		  <tr>
			<td><strong><?php echo self::$title1;?></strong></td>
		  </tr>
		  <tr>
			<td><?php $this->post_types_selected($saved_types); ?></td>
		  </tr>
		  <tr>
			<td><strong>Select Number of Attachment Files</strong></td>
		  </tr>
		  <tr>
			<td><select name="no_of_pdf_attachment">
				<?php echo $this->get_no_of_pdf_files_selected($saved_no_of_pdf_attachment); ?>
			</select></td>
		  </tr>
		  
		  <tr>
			<td><?php $this->help_info($saved_no_of_pdf_attachment); ?></td>
		  </tr>
		  
		  <tr>
			<td><input type="submit" name="submit" value="Save" class="button button-primary button-large" /></td>
		  </tr>
		</table>
		</form>
		<?php 
	}
	
	function custom_pdf_attachment_plugin_menu () {
		add_options_page( 'Custom Pdf Attachment', 'Custom PDF Attachment', 'activate_plugins', 'custom_pdf_attachment',  array( $this,'custom_pdf_attachment_options' ) );
	}
	
	function load_settings(){
		add_action('admin_menu', array( $this, 'custom_pdf_attachment_plugin_menu' ) );
		add_action( 'admin_init', array( $this, 'custom_pdf_attachment_post_data' ) );
		register_activation_hook(__FILE__, array( $this, 'plug_install_custom_pdf_attachment' ) );
		register_deactivation_hook(__FILE__, array( $this, 'plug_unins_custom_pdf_attachment' ) );
	}
	
	function plug_install_custom_pdf_attachment(){}
	
	function plug_unins_custom_pdf_attachment(){}
	
	function donate_form_pdf(){?>
		<table width="98%" border="0" style="background-color:#FFFFD2; border:1px solid #E6DB55;">
		 <tr>
		 <td align="right"><h3>Even $0.60 Can Make A Difference</h3></td>
			<td><form name="_xclick" action="https://www.paypal.com/cgi-bin/webscr" method="post">
				  <input type="hidden" name="cmd" value="_xclick">
				  <input type="hidden" name="business" value="avifoujdar@gmail.com">
				  <input type="hidden" name="item_name" value="Donation for plugins (PDF Attachment)">
				  <input type="hidden" name="currency_code" value="USD">
				  <input type="hidden" name="amount" value="0.60">
				  <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="Make a donation with PayPal">
				</form></td>
		  </tr>
		</table>
	<?php 
	} 
	
}
new afo_pdf_settings;