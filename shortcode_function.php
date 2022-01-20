<?php
function custom_pdf_attachment_shortcode( $atts ) {
     global $post;
	 extract( shortcode_atts( array(
	      'file' => '',
	      'name' => ''
     ), $atts ) );
     
	 if(!$file){
	 	return;
	 }
	 
	 if(!get_post_meta($post->ID, 'cpt_pdf_attachment'.$file, true)){
	 	return;
	 }
	 
	 if($name){
	 	$ret = '<img src="'.plugins_url( 'custom-post-type-pdf-attachment/pdf.png', dirname(__FILE__) ).'">&nbsp;<a href="'.get_post_meta($post->ID, 'cpt_pdf_attachment'.$file, true).'">'.$name.'</a>';
	} else {
		$ret = '<img src="'.plugins_url( 'custom-post-type-pdf-attachment/pdf.png', dirname(__FILE__) ).'">&nbsp;<img src=""><a href="'.get_post_meta($post->ID, 'cpt_pdf_attachment'.$file, true).'">PDF Download</a>';
	}
	 return $ret;
}
add_shortcode( 'pdf_attachment', 'custom_pdf_attachment_shortcode' );

function pdf_attachment_file($file,$name){
	if(!$file){
		return;
	}
	return do_shortcode('[pdf_attachment file="'.$file.'" name="'.$name.'"]');
}
?>