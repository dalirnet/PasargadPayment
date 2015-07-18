<?php
class controller
{		
	// ----------------------
	//	view
	// ----------------------
	public function view($file,$var = array())
	{
		if(!empty($var))
		{
			extract($var);
		}
		ob_start();
		require_once BASEPATH.'views/'.$file.'.php';
		$var = ob_get_clean();
		return $var;		
	}	
}
