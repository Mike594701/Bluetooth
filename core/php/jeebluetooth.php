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
require_once dirname(__FILE__) . "/../../../../core/php/core.inc.php";

if (!jeedom::apiAccess(init('apikey'), 'bluetooth')) {
	echo 'Clef API non valide, vous n\'etes pas autorisé à effectuer cette action';
	die();
}

if (init('test') != '') {
	echo 'OK';
	die();
}
$result = json_decode(file_get_contents("php://input"), true);
if (!is_array($result)) {
	die();
}
if (isset($result['learn_mode'])) {
	if ($result['learn_mode'] == 1) {
		config::save('include_mode', 1, 'bluetooth');
		event::add('bluetooth::includeState', array(
			'mode' => 'learn',
			'state' => 1)
		);
	} else {
		config::save('include_mode', 0, 'bluetooth');
		event::add('bluetooth::includeState', array(
			'mode' => 'learn',
			'state' => 0)
		);
	}
}
$remotes = bluetooth_remote::getCacheRemotes('allremotes',array());
if (isset($result['started'])) {
	if ($result['started'] == 1) {
		log::add('bluetooth','info','Antenna ' . $result['source'] . ' alive sending known devices');
		if ($result['source'] != 'local'){
			foreach ($remotes as $remote){
				if ($remote->getRemoteName() == $result['source']){
					$remote->setCache('lastupdate',date("Y-m-d H:i:s"));
					$version = '1.0';
					if (isset($result['version'])){
						$version = $result['version'];
					}
					$currentVersion = $remote->getConfiguration('version','1.0');
					if ($version != $currentVersion){
						$remote->setConfiguration('version',$version);
					}
					$remote->save();
					break;
				}
			}
		} else {
			$oldversion = config::byKey('version','bluetooth','1.0');
			$version = '1.0';
			if (isset($result['version'])){
				$version = $result['version'];
			}
			if ($version != $oldversion){
				config::save('version',$version,'bluetooth');
			}
		}
		usleep(500);
		bluetooth::sendIdToDeamon($result['source']);
	}
}
if (isset($result['heartbeat'])) {
	if ($result['heartbeat'] == 1) {
		log::add('bluetooth','info','This is a heartbeat from antenna ' . $result['source']);
		if ($result['source'] != 'local'){
			foreach ($remotes as $remote){
				if ($remote->getRemoteName() == $result['source']){
					$remote->setCache('lastupdate',date("Y-m-d H:i:s"));
					break;
				}
			}
		}
	}
}

if (isset($result['devices'])) {
	foreach ($result['devices'] as $key => $datas) {
		if (!isset($datas['id'])) {
			continue;
		}
		if (isset($datas['source'])){
			if ($datas['source'] != 'local'){
				foreach ($remotes as $remote){
					if ($remote->getRemoteName() == $datas['source']){
						$remote->setCache('lastupdate',date("Y-m-d H:i:s"));
						break;
					}
				}
			}
		}
		$bluetooth = bluetooth::byLogicalId($datas['id'], 'bluetooth');
		if (!is_object($bluetooth)) {
			if ($datas['learn'] != 1) {
				continue;
			}
			log::add('bluetooth','info','This is a learn from antenna ' . $datas['source']);
			$bluetooth = bluetooth::createFromDef($datas);
			if (!is_object($bluetooth)) {
				log::add('bluetooth', 'debug', __('Aucun équipement trouvé pour : ', __FILE__) . secureXSS($datas['id']));
				continue;
			}
			event::add('jeedom::alert', array(
				'level' => 'warning',
				'page' => 'bluetooth',
				'message' => '',
			));
			foreach ($remotes as $remote){
				if ($remote->getRemoteName() == $datas['source']){
					$bluetooth->setConfiguration('antennareceive',$remote->getId());
					$bluetooth->setConfiguration('antenna',$remote->getId());
					$bluetooth->save();
					break;
				}
			}
			event::add('bluetooth::includeDevice', $bluetooth->getId());
		}
		if (!$bluetooth->getIsEnable()) {
			continue;
		}
		if (isset($datas['specificconfiguration'])) {
			$bluetooth->setConfiguration('specificconfiguration',$datas['specificconfiguration']);
			$bluetooth->save();
		}
		if (isset($datas['rssi'])) {
			$rssicmd = $bluetooth->getCmd(null, 'rssi' . $datas['source']);
			if (!is_object($rssicmd)) {
				$rssicmd = new bluetoothCmd();
				$rssicmd->setLogicalId('rssi' . $datas['source']);
				$rssicmd->setIsVisible(0);
				$rssicmd->setIsHistorized(0);
				$rssicmd->setName(__('Rssi '. $datas['source'], __FILE__));
				$rssicmd->setType('info');
				$rssicmd->setSubType('numeric');
				$rssicmd->setUnite('dbm');
				$rssicmd->setEqLogic_id($bluetooth->getId());
				$rssicmd->save();
			}
			if ($rssicmd->getConfiguration('returnStateValue') == -200 || $rssicmd->getConfiguration('returnStateTime') == 2){
				$rssicmd->setConfiguration('returnStateValue','');
				$rssicmd->setConfiguration('returnStateTime','');
				$rssicmd->save();
			}
			$presentcmd = $bluetooth->getCmd(null, 'present' . $datas['source']);
			if (!is_object($presentcmd)) {
				$presentcmd = new bluetoothCmd();
				$presentcmd->setLogicalId('present' . $datas['source']);
				$presentcmd->setIsVisible(0);
				$presentcmd->setIsHistorized(0);
				$presentcmd->setName(__('Present '. $datas['source'], __FILE__));
				$presentcmd->setType('info');
				$presentcmd->setSubType('binary');
				$presentcmd->setTemplate('dashboard','line');
				$presentcmd->setTemplate('mobile','line');
				$presentcmd->setEqLogic_id($bluetooth->getId());
				$presentcmd->save();
			}
			$oldrssi = $bluetooth->getCache('rssi' . $datas['source'],-200);
			$delta = abs($oldrssi-$datas['rssi']);
			if ($delta >= 10){
				$bluetooth->checkAndUpdateCmd($rssicmd,$datas['rssi']);
				$bluetooth->setCache('rssi' . $datas['source'],$datas['rssi']);
			}
			$bluetooth->checkAndUpdateCmd($presentcmd,$datas['present']);
		}
		if ($bluetooth->getConfiguration('specificclass',0) != 0) {
			$device= $bluetooth->getConfiguration('device');
			require_once dirname(__FILE__) . '/../config/devices/'.$device.'/class/'.$device.'.class.php';
			$class= $device.'bluetooth';
			$childrenclass = new $class();
			$datas = $childrenclass->calculateInputValue($bluetooth,$datas);
		}
		if (isset($datas['battery'])){
			$bluetooth->batteryStatus($datas['battery']);
		}
		foreach ($bluetooth->getCmd('info') as $cmd) {
			$logicalId = $cmd->getLogicalId();
			if ($logicalId == '') {
				continue;
			}
			if (substr($logicalId,0,4) == 'rssi' || substr($logicalId,0,7) == 'present'){
				continue;
			}
			$path = explode('::', $logicalId);
			$value = $datas;
			foreach ($path as $key) {
				if (!isset($value[$key])) {
					continue (2);
				}
				$value = $value[$key];
			}
			if (!is_array($value)) {
				$bluetooth->checkAndUpdateCmd($cmd,$value);
			}
		}
		$bluetooth->computePresence();
	}
}
