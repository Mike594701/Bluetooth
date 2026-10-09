<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

try {
	require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
	include_file('core', 'authentification', 'php');

	if (!isConnect('admin')) {
		throw new Exception('401 Unauthorized');
	}

	ajax::init();

	if (init('action') == 'changeIncludeState') {
		bluetooth::changeIncludeState(init('state'), init('mode'), init('type'));
		ajax::success();
	}
	
	if (init('action') == 'deleteUnknown') {
		$eqLogics = eqLogic::byType('bluetooth');
		foreach ($eqLogics as $eqLogic) {
			if ($eqLogic->getConfiguration('device','') == 'default') {
				if ($eqLogic->getObject_id()==''){
					$eqLogic->remove();
				}
			}
		}
		ajax::success();
	}
	
	if (init('action') == 'getAllTypes') {
		$list = array();
		$allconfs = bluetooth::devicesParameters();
		foreach ($allconfs as $key=>$data){
			$list[$data['name']] = $data['configuration']['name'];
		}
		ksort($list);
		ajax::success($list);
	}
	
	if (init('action') == 'allantennas') {
		if (init('remote') == 'local') {
			if (init('type') == 'reception'){
				foreach (eqLogic::byType('bluetooth') as $eqLogic){
					$eqLogic->setConfiguration('antennareceive','local');
					$eqLogic->save();
				}
			} else {
				foreach (eqLogic::byType('bluetooth') as $eqLogic){
					$eqLogic->setConfiguration('antenna','local');
					$eqLogic->save();
				}
			}
		} else {
			if (init('type') == 'reception'){
				foreach (eqLogic::byType('bluetooth') as $eqLogic){
					$eqLogic->setConfiguration('antennareceive',init('remoteId'));
					$eqLogic->save();
				}
			} else {
				foreach (eqLogic::byType('bluetooth') as $eqLogic){
					$eqLogic->setConfiguration('antenna',init('remoteId'));
					$eqLogic->save();
				}
			}
		}
		ajax::success();
	}
	
	if (init('action') == 'syncconfbluetooth') {
		bluetooth::syncconfbluetooth(false);
		ajax::success();
	}

	if (init('action') == 'getMobileGraph') {
		ajax::success(bluetooth::getMobileGraph());
	}

	if (init('action') == 'getMobileHealth') {
		ajax::success(bluetooth::getMobileHealth());
	}

	if (init('action') == 'saveAntennaPosition') {
		ajax::success(bluetooth::saveAntennaPosition(init('antennas')));
	}
	
	if (init('action') == 'launchremotes') {
		ajax::success(bluetooth::launch_allremotes());
	}
	
	if (init('action') == 'sendremotes') {
		ajax::success(bluetooth::send_allremotes());
	}
	
	if (init('action') == 'updateremotes') {
		ajax::success(bluetooth::update_allremotes());
	}
	
	if (init('action') == 'stopremotes') {
		ajax::success(bluetooth::stop_allremotes());
	}

	if (init('action') == 'autoDetectModule') {
		$eqLogic = bluetooth::byId(init('id'));
		if (!is_object($eqLogic)) {
			throw new Exception(__('bluetooth eqLogic non trouvé : ', __FILE__) . init('id'));
		}
		if (init('createcommand') == 1){
			foreach ($eqLogic->getCmd() as $cmd) {
				$cmd->remove();
			}
		}
		$eqLogic->setConfiguration('applyDevice','');
		$eqLogic->save();
		ajax::success();
	}

	if (init('action') == 'getModelListParam') {
		$bluetooth = bluetooth::byId(init('id'));
		if (!is_object($bluetooth)) {
			ajax::success(array());
		}
		ajax::success($bluetooth->getModelListParam(init('conf')));
	}

	if (init('action') == 'save_bluetoothRemote') {
		$bluetoothRemoteSave = jeedom::fromHumanReadable(json_decode(init('bluetooth_remote'), true));
		$bluetooth_remote = bluetooth_remote::byId($bluetoothRemoteSave['id']);
		if (!is_object($bluetooth_remote)) {
			$bluetooth_remote = new bluetooth_remote();
		}
		utils::a2o($bluetooth_remote, $bluetoothRemoteSave);
		$bluetooth_remote->save();
		ajax::success(utils::o2a($bluetooth_remote));
	}

	if (init('action') == 'get_bluetoothRemote') {
		$bluetooth_remote = bluetooth_remote::byId(init('id'));
		if (!is_object($bluetooth_remote)) {
			throw new Exception(__('Remote inconnu : ', __FILE__) . init('id'), 9999);
		}
		ajax::success(jeedom::toHumanReadable(utils::o2a($bluetooth_remote)));
	}

	if (init('action') == 'remove_bluetoothRemote') {
		$bluetooth_remote = bluetooth_remote::byId(init('id'));
		if (!is_object($bluetooth_remote)) {
			throw new Exception(__('Remote inconnu : ', __FILE__) . init('id'), 9999);
		}
		$bluetooth_remote->remove();
		ajax::success();
	}

	if (init('action') == 'sendRemoteFiles') {
		if (!bluetooth::sendRemoteFiles(init('remoteId'))) {
			ajax::error(__('Erreur, vérifiez la log bluetooth', __FILE__));
		}
		ajax::success();
    }

	if (init('action') == 'getRemoteLog') {
		if (!bluetooth::getRemoteLog(init('remoteId'))) {
			ajax::error(__('Erreur, vérifiez la log bluetooth', __FILE__));
		}
		ajax::success();
     }

	 if (init('action') == 'getRemoteLogDependancy') {
		if (!bluetooth::getRemoteLog(init('remoteId'),'_dependancy')) {
			ajax::error(__('Erreur, vérifiez la log bluetooth', __FILE__));
		}
		ajax::success();
     }

	 if (init('action') == 'launchremote') {
		if (!bluetooth::launchremote(init('remoteId'))) {
			ajax::error(__('Erreur, vérifiez la log bluetooth', __FILE__));
		}
		ajax::success();
     }

	 if (init('action') == 'stopremote') {
		if (!bluetooth::stopremote(init('remoteId'))) {
			ajax::error(__('Erreur, vérifiez la log bluetooth', __FILE__));
		}
		ajax::success();
     }

	 if (init('action') == 'remotelearn') {
        ajax::success(bluetooth::remotelearn(init('remoteId'), init('state')));
     }

	 if (init('action') == 'dependancyRemote') {
        ajax::success(bluetooth::dependancyRemote(init('remoteId')));
     }

	 if (init('action') == 'aliveremote') {
        ajax::success(bluetooth::aliveremote(init('remoteId')));
     }

	if (init('action') == 'changeLogLive') {
		ajax::success(bluetooth::changeLogLive(init('level')));
	}

	throw new Exception('Aucune methode correspondante');
	/*     * *********Catch exeption*************** */
} catch (Exception $e) {
	ajax::error(displayException($e), $e->getCode());
}
?>
