<?php

include('header.php');

$paquet = new Paquet();
$paquet -> send_actions();

echo 
'<div class="panel panel-danger">'.
	'<div class="panel-heading">'.
		'<h3 class="panel-title">'._('Information').'</h3>'.
	'</div>'.
	'<div class="panel-body">'.
		'<div class="row">'.
			'<div class="col-sm-6">'._('Server name').'</div>'.
			'<div class="col-sm-6">'.
				'<input id="name" type="text" class="form-control" placeholder="'._('Server name').'">'.
			'</div>'.
		'</div>'.
		'<div class="row">'.
			'<div class="col-sm-6">'._('IP or Address').'</div>'.
			'<div class="col-sm-6">'.
				'<input id="address" type="text" class="form-control" placeholder="'._('IP or Address').'">'.
			'</div>'.
		'</div>'.
		'<div class="row">'.
			'<div class="col-sm-6">'._('Port').'</div>'.
			'<div class="col-sm-6">'.
				'<input id="port" type="text" class="form-control" placeholder="'._('Port').'">'.
			'</div>'.
		'</div>'.
		'<div class="row">'.
			'<div class="col-sm-6">'._('Security key').'</div>'.
			'<div class="col-sm-6">'.
				'<input id="key" type="password" class="form-control" placeholder="'._('key').'">'.
			'</div>'.
		'</div>'.
		'<div class="row">'.
			'<div class="col-sm-6">'._('Description').'</div>'.
			'<div class="col-sm-6">'.
				'<input id="description" type="text" class="form-control" placeholder="'._('Description').'">'.
			'</div>'.
		'</div>'.
	'</div>'.
'</div>'.
'<div class="center">
	<button onclick="popupclose()" type="button" class="btn btn-danger" data-toggle="dropdown">'._('Cancel').'</button> '.
	'<button onclick="formServerAdd()" type="button" class="btn btn-success" data-toggle="dropdown">'._('Confirm').'</button>
</div>';

echo '<script type="text/javascript">'.
		'$("#popupTitle").html("'._('Add a new server to VCA').'");'.
	'</script>';
?>