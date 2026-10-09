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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

function bluetooth_install() {
	$sql = file_get_contents(dirname(__FILE__) . '/install.sql');
	DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW);
	foreach (bluetooth::byType('bluetooth') as $bluetooth) {
		$bluetooth->save();
	}
	config::save('version',bluetooth::$_version,'bluetooth');
}

function bluetooth_update() {
	$sql = file_get_contents(dirname(__FILE__) . '/install.sql');
	DB::Prepare($sql, array(), DB::FETCH_TYPE_ROW);
	foreach (bluetooth::byType('bluetooth') as $bluetooth) {
		$bluetooth->save();
	}
	message::add('bluetooth','Pensez à mettre à jour vos antennes et relancer leurs dépendances si besoin ...');
	config::save('version',bluetooth::$_version,'bluetooth');
	if (config::byKey('allowUpdateAntennas','bluetooth',0) == 1) {
		log::add('bluetooth','info','Mise à jour des fichiers de toutes les antennes');
		bluetooth::send_allremotes();
	}
}

function bluetooth_remove() {
	DB::Prepare('DROP TABLE IF EXISTS `bluetooth_remote`', array(), DB::FETCH_TYPE_ROW);
}

?>
