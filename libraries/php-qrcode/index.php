<?php
namespace Essentials\Libraries\PHPQRCode;

use QRcode;

if ( $_GET[ 'text' ] === '' ) {
	exit; // Exit if text argument is empty.
}

/**
 * Generate QR codes.
 * Usage: "/php-qrcode/?text=hello+world&size=25&margin=1".
 *
 * @param string text The URL encoded text.
 * @param int size QR code size.
 * @param int margin QR code border.
 *
 * @since 1.0.0
 */
header( 'Content-Type: image/png' );

include( 'source/qrlib.php' );

$qrcode_text   = ! empty( $_GET[ 'text' ] ) ? $_GET[ 'text' ] : 'hello+world';
$qrcode_size   = ! empty( $_GET[ 'size' ] ) ? $_GET[ 'size' ] : 25;
$qrcode_margin = ! empty( $_GET[ 'margin' ] ) ? $_GET[ 'margin' ] : '1';

QRcode::png(
	$qrcode_text,
	null,
	QR_ECLEVEL_H,
	$qrcode_size,
	$qrcode_margin
);
