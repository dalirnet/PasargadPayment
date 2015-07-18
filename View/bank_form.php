<?php

	$html = NULL;
	$html .= '<!DOCTYPE html><html><head><title>Connecting</title>';
	$html .= '<script type="text/javascript">function send_http(){document.forms["http_send"].submit();}</script>';
	$html .= '</head><body onload="send_http()">';
	$html .= '<form method="post" name="http_send" id="http_send"';
	$html .= 'action="https://pep.shaparak.ir/gateway.aspx">';
	$html .= '<input type="hidden" name="invoiceNumber" value="'.$data['orderId'].'" />';
	$html .= '<input type="hidden" name="invoiceDate" value="'.$data['localDate'].'" />';
	$html .= '<input type="hidden" name="amount" value="'.$data['amount'].'" />';
	$html .= '<input type="hidden" name="terminalCode" value="'.$data['terminal'].'" />';
	$html .= '<input type="hidden" name="merchantCode" value="'.$data['user'].'" />';
	$html .= '<input type="hidden" name="redirectAddress" value="'.$data['callBackUrl'].'" />';
	$html .= '<input type="hidden" name="timeStamp" value="'.$data['localTime'].'" />';
	$html .= '<input type="hidden" name="action" value="'.$data['payerId'].'" />';
	$html .= '<input type="hidden" name="sign" value="'.$data['sign'].'" />';
	$html .= '</form></body></html>';
	
	echo $html;