<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: x-requested-with");
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('config.php');
include('functions.php');

include('libs/Db.class.php');
include('libs/Socket.class.php');
include('libs/Vps.class.php');
include('libs/Server.class.php');
include('libs/Guest.class.php');
include('libs/User.class.php');
include('libs/Admin.class.php');

$answer = array('user', 'answer');

if(empty($_POST['token'])) {
	$token = '';
}
else {
	$token = protect($_POST['token']);
}

if(empty($_POST['actions'])) {
	$actions = '';
}
else {
	$actions = $_POST['actions'];
}

if(!empty($actions['connect'])) {
	$var = $actions['connect'];
	if(!empty($var[0]) && !empty($var[1])) {
		$id = Guest::connect($var[0], $var[1]);
		
		if(!empty($id)) {
			$token = Guest::newToken($id);
		}			
	}
	
	unset($actions['connect']);
}

if(!empty($token)) {
	$user = Guest::loadUser($token);
}

if(!empty($user)) {
	if(!empty($actions)) {
		foreach ($actions as $action => $var) {
			$res = '';
			
			switch ($action) {
				/*** VCA ***/
				case 'vcaStats':
					$res = $user->vcaStats();
				break;
				
				/*** Users ***/
				case 'userProfile':
					$res = $user->userProfile();
				break;
				
				case 'userList':
					$res = $user->userList();
				break;
				
				case 'userUpdate':
					if(!empty($var[1]) && !empty($var[2])) {
						if(empty($var[0])) {
							$var[0] = 0;
						}
						if(empty($var[3])) {
							$var[3] = '';
						}
						$res = $user->userUpdate($var[0], trim(ucfirst($var[1])), trim(strtolower($var[2])), $var[3]);
					}
					else {
						$res = 4;
					}
				break;
				
				case 'userNew':
					if(!empty($var[0]) && !empty($var[1])) {
						$res = $user->userNew(trim(ucfirst($var[0])), trim(strtolower($var[1])));
					}
					else {
						$res = 1;
					}
				break;
				
				case 'userDelete':
					if(!empty($var[0])) {
						$res = $user->userDelete($var[0]);
					}
					else {
						$res = 6;
					}
				break;
				
				case 'userVps':
					if(!empty($var[0])) {
						$res = $user->userVps($var[0]);
					}
					else {
						$res = 6;
					}
				break;
				
				case 'userLanguage':
					if(!empty($var[0])) {
						$res = $user->userLanguage($var[0]);
					}
				break;
				
				/*** Language ***/
				case 'languageList':
					$res = User::languageList();
				break;
				
				/*** Requests ***/
				case 'requestList':
					$res = $user->requestList();
				break;
				
				case 'requestNew':
					if(!empty($var[0]) && !empty($var[1])) {
						$user->requestNew($var[0], $var[1]);
					}
				break;
				
				case 'requestAnswer':
					if(!empty($var[0]) && !empty($var[1])) {
						$user->requestAnswer($var[0], $var[1]);
					}
				break;
				
				case 'requestInfo':
					if(!empty($var[0])) {
						$res = $user->requestInfo($var[0]);
					}
				break;
				
				case 'requestClose':
					if(!empty($var[0])) {
						$requestList = $user->requestList();
						
						if(!empty($requestList[$var[0]]) && empty($requestList[$var[0]]['resolved'])) {
							$user->requestClose($var[0]);
						}
					}
				break;
				
				/*** IP ***/
				
				case 'ipFree':
					$res = $user->ipFree();
				break;
				
				case 'ipList':
					$res = $user->ipList();
				break;
				
				case 'ipNew':
					if(!empty($var[0])) {
						$user->ipNew($var[0]);
					}
				break;
				
				case 'ipDelete':
					if(!empty($var[0])) {
						$user->ipDelete($var[0]);
					}
				break;
				
				/*** Servers ***/
				
				case 'serverList':
					$res['list'] = $user->serverList();
					$res['nb']   = $user->vpsNb();
				break;
				
				case 'serverAdd':
					if(!empty($var[0]) && !empty($var[1]) && !empty($var[2]) && !empty($var[3])) {
						$user->serverAdd($var[0],$var[1],$var[2],$var[3]);
					}
				break;
				
				case 'serverDelete':
					if(!empty($var[0])) {
						$res = $user->serverDelete($var[0]);
					}
				break;
				
				case 'serverUpdate':
					if(!empty($var[0])) {
						$res = $user->serverUpdate($var[0], $var[1]);
					}
					else {
						$res = null;
					}
				break;
				
				case 'serverReload':
					if(!empty($var[0])) {
						$user->serverReload($var[0]);
					}
					else {
						$user->serverReload();
					}
				break;
				
				case 'serverRestart':
					if(!empty($var[0])) {
						$user->serverRestart($var[0]);
					}
				break;
				
				case 'serverTemplate':
					if(!empty($var[0])) {
						$res = $user->serverTemplate($var[0]);
					}
				break;
				
				case 'serverBackup':
					if(!empty($var[0])) {
						$res = $user->serverBackup($var[0]);
					}
				break;
				
				case 'serverBackupDelete':
					if(!empty($var[0]) && !empty($var[1]) && !empty($var[2])) {
						$res = $user->serverBackupDelete($var[0],$var[1],$var[2]);
					}
				break;
				
				/*** Template ***/
				case 'serverTemplateRename':
					if(!empty($var[0]) && !empty($var[1]) && !empty($var[2])) {
						$res = $user->serverTemplateRename($var[0], $var[1], $var[2]);
					}
				break;
				
				case 'serverTemplateAdd':
					if(!empty($var[0]) && !empty($var[1])) {
						$res = $user->serverTemplateAdd($var[0], $var[1]);
					}
				
				case 'serverTemplateDelete':
					if(!empty($var[0]) && !empty($var[1])) {
						$res = $user->serverTemplateDelete($var[0], $var[1]);
					}
				break;
				/*** VPS ***/
				
				case 'vpsList':
					if(!empty($var[0]) && is_numeric($var[0])) {
						$res = $user->vpsList($var[0]);
					}
					else {
						$res = $user->vpsList();
					}
				break;
				
				case 'vpsAdd':
					if(!empty($var[0]) && !empty($var[1])) {
						$res = $user->vpsAdd($var[0], $var[1]);
					}
				break;
				
				case 'vpsUpdate':
					if(!empty($var[0]) && !empty($var[1])) {
						$res = $user->vpsUpdate($var[0], $var[1]);
					}
				break;
				
				case 'vpsReload':
					if(!empty($var[0])) {
						$user->vpsReload($var[0]);
					}
				break;
				
				case 'vpsDelete':
					if(!empty($var[0])) {
						$res = $user->vpsDelete($var[0]);
					}
				break;
				
				case 'vpsStart':
					if(!empty($var[0])) {
						$res = $user->vpsStart($var[0]);
					}
				break;
				
				case 'vpsStop':
					if(!empty($var[0])) {
						$res = $user->vpsStop($var[0]);
					}
				break;

				case 'vpsRestart':
					if(!empty($var[0])) {
						$res = $user->vpsRestart($var[0]);
					}
				break;
				
				case 'vpsClone':
					if(!empty($var[0]) && !empty($var[1]) && !empty($var[2])) {
						$res = $user->vpsClone($var[0],$var[1],$var[2]);
					}
				break;
				
				case 'vpsCmd':
					if(!empty($var[0]) && !empty($var[1])) {
						$res = $user->vpsCmd($var[0],$var[1]);
					}
				break;
				
				case 'vpsPassword':
					if(!empty($var[0]) && !empty($var[1])) {
						$res = $user->vpsPassword($var[0],$var[1]);
					}
				break;
				
				case 'vpsTemplate':
					if(!empty($var[0])) {
						$res = $user->vpsTemplate($var[0]);
					}
				break;
				
				case 'vpsReinstall':
					if(!empty($var[0]) && !empty($var[1])) {
						$res = $user->vpsReinstall($var[0],$var[1]);
					}
				break;
				
				case 'vpsBackup':
					if(!empty($var[0])) {
						$list = $user->vpsList();
						if(!empty($list[$var[0]])) {
							$res = $user->vpsBackup($var[0]);
						}
					}
				break;
				
				case 'vpsBackupAdd':
					if(!empty($var[0])) {
						$list = $user->vpsList();
						if(!empty($list[$var[0]])) {
							$res = $user->vpsBackupAdd($var[0]);
						}
					}
				break;
				
				case 'vpsBackupRestore':
					if(!empty($var[0]) && !empty($var[1])) {
						$list = $user->vpsList();
						if(!empty($list[$var[0]])) {
							$res = $user->vpsBackupRestore($var[0], $var[1]);
						}
					}
				break;
				
				case 'vpsBackupDelete':
					if(!empty($var[0]) && !empty($var[1])) {
						$list = $user->vpsList();
						if(!empty($list[$var[0]])) {
							$res = $user->vpsBackupDelete($var[0], $var[1]);
						}
					}
				break;
			}
			
			if(isset($res)) {
				$answer['answer'][$action] = $res;
			}
		}
	}
	
	$answer['user'] = array(
		'id'    => $user->getId(),
		'token' => $token,
		'rank'  => $user->getRank(),
		'language' => $user->getLanguage()
	);
}

echo json_encode($answer);

?>