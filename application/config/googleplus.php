<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$CI = get_instance();
if($CI->uri->segment(2)== 'signup'){
	$redirect_uri = base_url().'auth/signup';
}else{
	$redirect_uri = base_url().'auth';
}

$config['googleplus']['application_name'] = 'Project Mangement PTPN V';
$config['googleplus']['client_id']        = '715404691367-negvktg43o3cqdmjvf8au7ftocdaqu64.apps.googleusercontent.com';
$config['googleplus']['client_secret']    = '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';
$config['googleplus']['redirect_uri']     = $redirect_uri;
$config['googleplus']['api_key']          = 'AIzaSyBFJc4D6LirBZeIYr2E5_j_O8SeBsH28_c';
$config['googleplus']['scopes']           = array(
												"https://www.googleapis.com/auth/plus.login",
												"https://www.googleapis.com/auth/plus.me",
												"https://www.googleapis.com/auth/userinfo.email",
												"https://www.googleapis.com/auth/userinfo.profile"
												);

