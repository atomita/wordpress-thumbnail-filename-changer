<?php
/*
Plugin Name: ThumbName Changer
Plugin URI: http://creatorish.com/lab/4612
Description: サムネイルのファイル名を{$width}x{$height}.jpgではなく-thumbnail.jpg等にするプラグイン。add_image_size($name,$w,$h,$c);で追加した画像は-{$name}.jpgになります。
Version: 0.1
Author: yuu@creatorish
Author URI:  http://creatorish.com
*/

add_filter( 'intermediate_image_sizes_advanced', 'hack_intermediate_image_sizes_advanced' );
add_filter( 'wp_generate_attachment_metadata', 'hack_wp_generate_attachment_metadata', 10, 2 );

function hack_intermediate_image_sizes_advanced( $sizes ) {
	return array();
}
function hack_wp_generate_attachment_metadata( $metadata, $attachment_id ) {
	$attachment = get_post( $attachment_id );
	$uploadPath = wp_upload_dir();
	$file = path_join($uploadPath['basedir'], $metadata['file']);
	$metadata = array();
	
	if ( preg_match('!^image/!', get_post_mime_type( $attachment )) && file_is_displayable_image($file) ) {
		$imagesize = getimagesize( $file );
		
		$metadata['width'] = $imagesize[0];
		$metadata['height'] = $imagesize[1];
		list($uwidth, $uheight) = wp_constrain_dimensions($metadata['width'], $metadata['height'], 128, 96);
		$metadata['hwstring_small'] = "height='$uheight' width='$uwidth'";
		
		// Make the file path relative to the upload dir
		$metadata['file'] = _wp_relative_upload_path($file);
		
		// make thumbnails and other intermediate sizes
		global $_wp_additional_image_sizes;
		
		foreach ( get_intermediate_image_sizes() as $s ) {
			$sizes[$s] = array( 'width' => '', 'height' => '', 'crop' => FALSE );
			if ( isset( $_wp_additional_image_sizes[$s]['width'] ) )
				$sizes[$s]['width'] = intval( $_wp_additional_image_sizes[$s]['width'] ); // For theme-added sizes
			else
				$sizes[$s]['width'] = get_option( "{$s}_size_w" ); // For default sizes set in options
			if ( isset( $_wp_additional_image_sizes[$s]['height'] ) )
				$sizes[$s]['height'] = intval( $_wp_additional_image_sizes[$s]['height'] ); // For theme-added sizes
			else
				$sizes[$s]['height'] = get_option( "{$s}_size_h" ); // For default sizes set in options
			if ( isset( $_wp_additional_image_sizes[$s]['crop'] ) )
				$sizes[$s]['crop'] = intval( $_wp_additional_image_sizes[$s]['crop'] ); // For theme-added sizes
			else
				$sizes[$s]['crop'] = get_option( "{$s}_crop" ); // For default sizes set in options
		}
		foreach ($sizes as $size => $size_data ) {
			$resized = hack_image_make_intermediate_size( $file, $size_data['width'], $size_data['height'], $size_data['crop'], $size );
			
			if ( $resized )
				$metadata['sizes'][$size] = $resized;
		}
		// fetch additional metadata from exif/iptc
		$image_meta = wp_read_image_metadata( $file );
		if ( $image_meta )
			$metadata['image_meta'] = $image_meta;

	}
	return $metadata;
}
function hack_image_make_intermediate_size( $file, $width, $height, $crop = false, $size = "" ) {
	if ( $width || $height ) {
		if ($size == "thumbnail" || $size == "medium" || $size == "large") {
			$suffix = $size;
		} else {
			global $_wp_additional_image_sizes;
			if (isset($_wp_additional_image_sizes[$size])) {
				$suffix = $size;
			} else {
				$suffix = null;
			}
		}
		//コアファイルを触らずにサムネイル(jpg)のクオリティ値を変えられます。デフォルトは90。
		$image = wp_get_image_editor( $file ); // Return an implementation that extends <tt>WP_Image_Editor</tt>
		if ( ! is_wp_error( $image ) ) {
			if ( empty($suffix) ){
				$suffix = "{$width}x{$height}";
			}
			$pathinfo = pathinfo($file);
			$dir = $pathinfo['dirname'];
			$ext = $pathinfo['extension'];
			$name = basename($file, ".{$ext}");
			$resized_file = "{$dir}/{$name}-{$suffix}.{$ext}";
			
			$image->rotate( 90 );
			$image->resize( $width, $height, $crop );
			$image->save( $resized_file );
			if ($info = getimagesize( $resized_file )){
				$resized_file = apply_filters('image_make_intermediate_size', $resized_file);
				return array(
					'file' => wp_basename( $resized_file ),
					'width' => $info[0],
					'height' => $info[1],
					'size' => $size
				);
			}
		}
	}
	return false;
}
?>
