<?php
function add_custom_pdf_attachment_meta_boxes() {

$saved_types = get_option('saved_post_types_for_pdf_attachment');
$saved_no_of_pdf_attachment = get_option('saved_no_of_pdf_attachment');

if(is_array($saved_types)){
	foreach ( $saved_types as $saved_type ) {
		for($i=1; $i<=$saved_no_of_pdf_attachment; $i++ ){
			add_meta_box(
				'cpt_pdf_attachment'.$i,
				'PDF Attachment '.$i,
				'cpt_pdf_attachment'.$i,
				$saved_type
			);
		}
	}
}

} // end add_custom_pdf_attachment_meta_boxes
add_action('add_meta_boxes', 'add_custom_pdf_attachment_meta_boxes');


$saved_no_of_pdf_attachment = get_option('saved_no_of_pdf_attachment');
for($i=1; $i<=$saved_no_of_pdf_attachment; $i++ ){
    eval('function cpt_pdf_attachment'.$i.'(){cpt_pdf_attachment_defined('.$i.');}');
}

function cpt_pdf_attachment_defined($i) {
	global $post,$wpdb;
	wp_nonce_field(plugin_basename(__FILE__), 'cpt_pdf_attachment_nonce'.$i);
	
	$html = '<table width="100%" border="0">
  <tr>
    <td><strong>Upload your PDF Here</strong></td>
    <td>&nbsp;</td>
	<td>&nbsp;</td>
  </tr>
  <tr>
    <td><input id="cpt_pdf_attachment" name="cpt_pdf_attachment'.$i.'" value="" size="25" type="file"></td>';
    $html .= '<td>';
		if(get_post_meta($post->ID, 'cpt_pdf_attachment'.$i, true)){
			$html .= '<a href="'.get_post_meta($post->ID, 'cpt_pdf_attachment'.$i, true).'">Download</a>';
		}
	$html .= '</td>';
	
	$html .= '<td>';
		if(get_post_meta($post->ID, 'cpt_pdf_attachment'.$i, true)){
			$html .= 'Check to remove&nbsp;<input type="checkbox" id="cpt_pdf_attachment_remove" name="cpt_pdf_attachment_remove'.$i.'" value="'.$i.'">';
		}
	$html .= '</td>';
	
  $html .= '</tr>
</table>';
	
	echo $html;
} 



function save_custom_pdf_attachment_meta_data($id) {

$saved_no_of_pdf_attachment = get_option('saved_no_of_pdf_attachment');

/* --- security verification --- */
for($i=1; $i<=$saved_no_of_pdf_attachment; $i++ ){
	if(!wp_verify_nonce($_POST['cpt_pdf_attachment_nonce'.$i], plugin_basename(__FILE__))) {
		return $id;
	} // end if
}

if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
return $id;
} // end if

if('page' == $_POST['post_type']) {
	if(!current_user_can('edit_page', $id)) {
		return $id;
	} // end if
} else {
	if(!current_user_can('edit_page', $id)) {
		return $id;
	} // end if
} // end if
/* - end security verification - */

// check for delete file //
for($i=1; $i<=$saved_no_of_pdf_attachment; $i++ ){
	if(!empty($_POST['cpt_pdf_attachment_remove'.$i])) {
		delete_post_meta($id, 'cpt_pdf_attachment'.$i);
	}
}


// check for delete file //

// Make sure the file array isn't empty
	for($i=1; $i<=$saved_no_of_pdf_attachment; $i++ ){
		if(!empty($_FILES['cpt_pdf_attachment'.$i]['name'])) {
		
		// Setup the array of supported file types. In this case, it's just PDF.
		$supported_types = array('application/pdf');
		
		// Get the file type of the upload
		$arr_file_type = wp_check_filetype(basename($_FILES['cpt_pdf_attachment'.$i]['name']));
		$uploaded_type = $arr_file_type['type'];
		
		// Check if the type is supported. If not, throw an error.
		if(in_array($uploaded_type, $supported_types)) {
		
		// Use the WordPress API to upload the file
		$upload = wp_upload_bits($_FILES['cpt_pdf_attachment'.$i]['name'], NULL, file_get_contents($_FILES['cpt_pdf_attachment'.$i]['tmp_name']));
		
		if(isset($upload['error']) && $upload['error'] != 0) {
			wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
		} else {
			add_post_meta($id, 'cpt_pdf_attachment'.$i, $upload['url']);
			update_post_meta($id, 'cpt_pdf_attachment'.$i, $upload['url']);
		} // end if/else
		
		} else {
			wp_die("The file type that you've uploaded is not a PDF.");
		} // end if/else
		
		} // end if
	}
} // end save_custom_pdf_attachment_meta_data


add_action('save_post', 'save_custom_pdf_attachment_meta_data');

function update_edit_form_for_custom_pdf_attachment() {
echo ' enctype="multipart/form-data"';
} // end update_edit_form
add_action('post_edit_form_tag', 'update_edit_form_for_custom_pdf_attachment');
?>