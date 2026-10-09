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

if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}

$remotes = bluetooth_remote::all();
$id = init('id');
sendVarToJS('plugin', $id);
?>
<div id='div_bluetoothRemoteAlert' style="display: none;"></div>
<div class="row row-overflow">
	<div class="col-lg-3 col-md-4 col-sm-5 col-xs-5">
		<div class="bs-sidebar">
			<ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
				<a class="btn btn-default bluetoothRemoteAction" style="width : 100%;margin-top : 5px;" data-action="add"><i class="fas fa-plus-circle"></i> {{Ajouter Antenne}}</a>
				<a class="btn btn-warning bluetoothRemoteAction" style="width : 100%;margin-bottom: 5px;" data-action="refresh"><i class="fas fa-sync"></i> {{Rafraichir}}</a>
				<li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
				<?php
foreach ($remotes as $remote) {
	$icon = '<i class="fas fa-heartbeat" style="color:green"></i>';
	$last = $remote->getCache('lastupdate','0');
	if ($last == '0' or time() - strtotime($last)>65){
		$icon = '<i class="fas fa-deaf" style="color:#b20000"></i>';
	}
	echo '<li class="cursor li_bluetoothRemote" data-bluetoothRemote_id="' . $remote->getId() . '" data-bluetoothRemote_name="' . $remote->getRemoteName() . '"><a>' . $remote->getRemoteName() . ' '. $icon. ' - v' . $remote->getConfiguration('version','1.0') . ' - ' . $remote->getCache('lastupdate','0') .'</a></li>';
}
?>
			</ul>
		</div>
	</div>
	 <div class="col-lg-19 col-md-8 col-sm-7 col-xs-7 remoteThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
<legend><i class="fas fa-table"></i>  {{Mes Antennes}}</legend>

<div class="eqLogicThumbnailContainer">
	<div class="cursor bluetoothRemoteAction logoPrimary" data-action="add" style="width:10px">
      <i class="fas fa-plus-circle"></i>
	  <br/>
    <span>{{Ajouter}}</span>
  </div>
  <?php
foreach ($remotes as $remote) {
	echo '<div class="eqLogicDisplayCard cursor col-lg-2" data-remote_id="' . $remote->getId() . '" style="width:10px">';
	echo '<img class="lazy" src="plugins/bluetooth/3rdparty/antenna.png"/>';
	echo '</br>';
	echo '<span class="name">' . $remote->getRemoteName() . '</span>';
	echo '</div>';
}
?>
</div>
</div>

	<div class="col-lg-9 col-md-8 col-sm-7 col-xs-7 bluetoothRemote" style="border-left: solid 1px #EEE; padding-left: 25px;display:none;">
		<a class="btn btn-success bluetoothRemoteAction pull-right" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a>
		<a class="btn btn-danger bluetoothRemoteAction pull-right" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>

			<form class="form-horizontal">
					<fieldset>
						<legend><i class="fas fa-arrow-circle-left returnAction cursor"></i> {{Général}}</legend>
						<div class="form-group">
							<label class="col-sm-2 control-label">{{Nom}}</label>
							<div class="col-sm-3">
								<input type="text" class="bluetoothRemoteAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="bluetoothRemoteAttr form-control" data-l1key="remoteName" placeholder="{{Nom de l'antenne}}"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">{{Ip}}</label>
							<div class="col-sm-3">
								<input type="text" class="bluetoothRemoteAttr form-control" data-l1key="configuration" data-l2key="remoteIp"/>
							</div>
							<label class="col-sm-1 control-label">{{Port}}</label>
							<div class="col-sm-3">
								<input type="text" class="bluetoothRemoteAttr form-control" data-l1key="configuration" data-l2key="remotePort"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">{{User}}</label>
							<div class="col-sm-3">
								<input type="text" class="bluetoothRemoteAttr form-control" data-l1key="configuration" data-l2key="remoteUser"/>
							</div>
							<label class="col-sm-1 control-label">{{Password}}</label>
							<div class="col-sm-3">
								<input type="password" class="bluetoothRemoteAttr form-control" data-l1key="configuration" data-l2key="remotePassword"/>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">{{Device}}</label>
							<div class="col-sm-3">
								<input type="text" class="bluetoothRemoteAttr form-control" data-l1key="configuration" data-l2key="remoteDevice" placeholder="{{ex : hci0}}"/>
							</div>
						</div>
						<?php
						if (method_exists( $id ,'sendRemoteFiles')){
							echo '<div class="form-group">
						<label class="col-sm-2 control-label">{{Envoi des fichiers nécessaires}}</label>
						<div class="col-sm-3">
							<a class="btn btn-warning bluetoothRemoteAction" data-action="sendFiles"><i class="fas fa-upload"></i> {{Envoyer les fichiers}}</a>
						</div>';
						if (method_exists( $id ,'dependancyRemote')){
							echo '<label class="col-sm-2 control-label">{{Installation des dépendances}}</label>
						<div class="col-sm-3">
							<a class="btn btn-warning bluetoothRemoteAction" data-action="dependancyRemote"><i class="fas fa-spinner"></i> {{Lancer les dépendances}}</a>
						</div>
						<div class="col-sm-2">
							<a class="btn btn-success bluetoothRemoteAction" data-action="getRemoteLogDependancy"><i class="far fa-file-alt"></i> {{Log dépendances}}</a>
						</div>';
						}
						echo'</div>';
						}
						if (method_exists( $id ,'launchremote')){
							echo '<div class="form-group">
						<label class="col-sm-2 control-label">{{Gestion du démon}}</label>
						<div class="col-sm-2">
							<a class="btn btn-success bluetoothRemoteAction" data-action="launchremote"><i class="fas fa-play"></i> {{Lancer}}</a>
						</div>
						<div class="col-sm-2">
							<a class="btn btn-danger bluetoothRemoteAction" data-action="stopremote"><i class="fas fa-stop"></i> {{Arret}}</a>
						</div>
						<div class="col-sm-2">
							<a class="btn btn-success bluetoothRemoteAction" data-action="getRemoteLog"><i class="far fa-file-alt"></i> {{Log}}</a>
						</div>
						</div>
						<div class="form-group">
						<label class="col-sm-2 control-label">{{Gestion du démon automatique}}</label>
						<div class="col-sm-2">
							<a class="btn btn-danger bluetoothRemoteAction" data-action="changeAutoModeRemote"></a>
							<input type="hidden" class="bluetoothRemoteAttr form-control" data-l1key="configuration" data-l2key="remoteDaemonAuto"/>
						</div>
						</div>';
						}
						?>
						<div class="form-group">
						<label class="col-sm-2 control-label">{{Mettre tous les équipements sur cette antenne}}</label>
						<div class="col-sm-2">
							<a class="btn btn-success bluetoothRemoteAction" data-action="all" data-type="reception"><i class="fas fa-sign-in-alt fa-rotate-90"></i> {{Réception}}</a>
						</div>
						<div class="col-sm-2">
							<a class="btn btn-danger bluetoothRemoteAction" data-action="all" data-type="emission"><i class="fas fa-sign-in-alt fa-rotate-270"></i> {{Emission}}</a>
						</div>
						</div>
						<div class="alert alert-info">{{La durée d'installation des dépendances sur une antenne peut prendre jusqu'à presque 30 minutes selon les antennes. SI vous utilisez des antennes PENSEZ à autoriser l'api du plugin sur autre chose que LOCALHOST}}</div>
				</div>
						</fieldset>
				</form>
	</div>
</div>

<script>
	function refreshDaemonMode() {
		var auto = $('.bluetoothRemoteAttr[data-l2key="remoteDaemonAuto"]').value();
		if(auto == 1){
			$('.bluetoothRemoteAction[data-action=stopremote]').hide();
			$('.bluetoothRemoteAction[data-action=changeAutoModeRemote]').removeClass('btn-success').addClass('btn-danger');
			$('.bluetoothRemoteAction[data-action=changeAutoModeRemote]').html('<i class="fas fa-times"></i> {{Désactiver}}');
		}else{
			$('.bluetoothRemoteAction[data-action=stopremote]').show();
			$('.bluetoothRemoteAction[data-action=changeAutoModeRemote]').removeClass('btn-danger').addClass('btn-success');
			$('.bluetoothRemoteAction[data-action=changeAutoModeRemote]').html('<i class="fas fa-magic"></i> {{Activer}}');
		}
	}
	$('.bluetoothRemoteAction[data-action=refresh]').on('click',function(){
		$('#md_modal').dialog('close');
		$('#md_modal').dialog({title: "{{Gestion des antennes bluetooth}}"});
		$('#md_modal').load('index.php?v=d&plugin=bluetooth&modal=bluetooth.remote&id=bluetooth').dialog('open');
	});

	$('.bluetoothRemoteAction[data-action=add]').on('click',function(){
		$('.bluetoothRemote').show();
		$('.remoteThumbnailDisplay').hide();
		$('.bluetoothRemoteAttr').value('');
	});

	$('.eqLogicDisplayCard').on('click',function(){
		displaybluetoothRemote($(this).attr('data-remote_id'));
	});

	function displaybluetoothRemote(_id){
		$('.li_bluetoothRemote').removeClass('active');
		$('.li_bluetoothRemote[data-bluetoothRemote_id='+_id+']').addClass('active');
		$.ajax({
			type: "POST",
			url: "plugins/bluetooth/core/ajax/bluetooth.ajax.php",
			data: {
				action: "get_bluetoothRemote",
				id: _id,
			},
			dataType: 'json',
			async: true,
			global: false,
			error: function (request, status, error) {
			},
			success: function (data) {
				if (data.state != 'ok') {
					return;
				}
				$('.bluetoothRemote').show();
				$('.remoteThumbnailDisplay').hide();
				$('.bluetoothRemoteAttr').value('');
				$('.bluetoothRemote').setValues(data.result,'.bluetoothRemoteAttr');
				setTimeout(function() { refreshDaemonMode(); }, 200);
			}
		});
	}

	function displaybluetoothRemoteComm(_id){
		$('.li_bluetoothRemote').removeClass('active');
		$('.li_bluetoothRemote[data-bluetoothRemote_id='+_id+']').addClass('active');
		$.ajax({
			type: "POST",
			url: "plugins/bluetooth/core/ajax/bluetooth.ajax.php",
			data: {
				action: "get_bluetoothRemote",
				id: _id,
			},
			dataType: 'json',
			async: true,
			global: false,
			error: function (request, status, error) {
			},
			success: function (data) {
				if (data.state != 'ok') {
					return;
				}
				$('.bluetoothRemote').show();
				$('.bluetoothRemoteAttrcomm').value('');
				$('.bluetoothRemote').setValues(data.result,'.bluetoothRemoteAttrcomm');
			}
		});
	}

	$('.li_bluetoothRemote').on('click',function(){
		displaybluetoothRemote($(this).attr('data-bluetoothRemote_id'));
		$('.remoteThumbnailDisplay').hide();
	});

	$('.returnAction').on('click',function(){
		$('.bluetoothRemote').hide();
		$('.li_bluetoothRemote').removeClass('active');
		setTimeout(function() { $('.remoteThumbnailDisplay').show() }, 100);
		;
	});

	$('.bluetoothRemoteAction[data-action=changeAutoModeRemote]').on('click',function(){
		var auto = 1 - $('.bluetoothRemoteAttr[data-l2key="remoteDaemonAuto"]').value();
		$('.bluetoothRemoteAttr[data-l2key="remoteDaemonAuto"]').val(auto);
		$('.bluetoothRemoteAction[data-action=save]').click();
	});

	$('.bluetoothRemoteAction[data-action=save]').on('click',function(){
		var bluetooth_remote = $('.bluetoothRemote').getValues('.bluetoothRemoteAttr')[0];
		$.ajax({
			type: "POST",
			url: "plugins/bluetooth/core/ajax/bluetooth.ajax.php",
			data: {
				action: "save_bluetoothRemote",
				bluetooth_remote: json_encode(bluetooth_remote),
			},
			dataType: 'json',
			error: function (request, status, error) {
				handleAjaxError(request, status, error,$('#div_bluetoothRemoteAlert'));
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_bluetoothRemoteAlert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				$('#div_bluetoothRemoteAlert').showAlert({message: '{{Sauvegarde réussie}}', level: 'success'});
				$('#md_modal').dialog('close');
				$('#md_modal').dialog({title: "{{Gestion des antennes bluetooth}}"});
				$('#md_modal').load('index.php?v=d&plugin=bluetooth&modal=bluetooth.remote&id=bluetooth').dialog('open');
				setTimeout(function() { displaybluetoothRemote(data.result.id) }, 200);

			}
		});
	});

	$('.bluetoothRemoteAction[data-action=sendFiles]').on('click',function(){
		var bluetooth_remote = $('.bluetoothRemote').getValues('.bluetoothRemoteAttr')[0];
		$.ajax({
			type: "POST",
			url: "plugins/"+plugin+"/core/ajax/"+plugin+".ajax.php",
			data: {
				action: "sendRemoteFiles",
				remoteId: $('.li_bluetoothRemote.active').attr('data-bluetoothRemote_id'),
			},
			dataType: 'json',
			error: function (request, status, error) {
				handleAjaxError(request, status, error,$('#div_bluetoothRemoteAlert'));
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_bluetoothRemoteAlert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				$('#div_bluetoothRemoteAlert').showAlert({message: '{{Envoi réussi}}', level: 'success'});
			}
		});
	});

	$('.bluetoothRemoteAction[data-action=getRemoteLog]').on('click',function(){
		var bluetooth_remote = $('.bluetoothRemote').getValues('.bluetoothRemoteAttr')[0];
		$.ajax({
			type: "POST",
			url: "plugins/"+plugin+"/core/ajax/"+plugin+".ajax.php",
			data: {
				action: "getRemoteLog",
				remoteId: $('.li_bluetoothRemote.active').attr('data-bluetoothRemote_id'),
			},
			dataType: 'json',
			error: function (request, status, error) {
				handleAjaxError(request, status, error,$('#div_bluetoothRemoteAlert'));
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_bluetoothRemoteAlert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				$('#div_bluetoothRemoteAlert').showAlert({message: '{{Log récupérée}}', level: 'success'});
			}
		});
	});

	$('.bluetoothRemoteAction[data-action=getRemoteLogDependancy]').on('click',function(){
		var bluetooth_remote = $('.bluetoothRemote').getValues('.bluetoothRemoteAttr')[0];
		$.ajax({
			type: "POST",
			url: "plugins/"+plugin+"/core/ajax/"+plugin+".ajax.php",
			data: {
				action: "getRemoteLogDependancy",
				remoteId: $('.li_bluetoothRemote.active').attr('data-bluetoothRemote_id'),
			},
			dataType: 'json',
			error: function (request, status, error) {
				handleAjaxError(request, status, error,$('#div_bluetoothRemoteAlert'));
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_bluetoothRemoteAlert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				$('#div_bluetoothRemoteAlert').showAlert({message: '{{Log récupérée}}', level: 'success'});
			}
		});
	});

	$('.bluetoothRemoteAction[data-action=launchremote]').on('click',function(){
		var bluetooth_remote = $('.bluetoothRemote').getValues('.bluetoothRemoteAttr')[0];
		$.ajax({
			type: "POST",
			url: "plugins/"+plugin+"/core/ajax/"+plugin+".ajax.php",
			data: {
				action: "launchremote",
				remoteId: $('.li_bluetoothRemote.active').attr('data-bluetoothRemote_id'),
			},
			dataType: 'json',
			error: function (request, status, error) {
				handleAjaxError(request, status, error,$('#div_bluetoothRemoteAlert'));
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_bluetoothRemoteAlert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				$('#div_bluetoothRemoteAlert').showAlert({message: '{{Lancement réussi}}', level: 'success'});
			}
		});
	});
	
	$('.bluetoothRemoteAction[data-action=all]').on('click',function(){
	var type = $(this).attr('data-type');
	var remoteId=$('.li_bluetoothRemote.active').attr('data-bluetoothRemote_id');
	bootbox.confirm('{{Etes-vous sûr de vouloir mettre tous les équipements sur cette antenne en : }}' +$(this).attr('data-type'), function (result) {
		if (result) {
			$.ajax({
				type: "POST",
				url: "plugins/bluetooth/core/ajax/bluetooth.ajax.php",
				data: {
					action: "allantennas",
					remote: "remote",
					remoteId: remoteId,
					type: type,
				},
				dataType: 'json',
				error: function (request, status, error) {
					handleAjaxError(request, status, error,$('#div_bluetoothRemoteAlert'));
				},
				success: function (data) {
					if (data.state != 'ok') {
						$('#div_bluetoothRemoteAlert').showAlert({message: data.result, level: 'danger'});
						return;
					}
					$('#div_bluetoothRemoteAlert').showAlert({message: '{{Réussie}}', level: 'success'});
				}
			});
		}
	});
});

	$('.bluetoothRemoteAction[data-action=dependancyRemote]').on('click',function(){
		var bluetooth_remote = $('.bluetoothRemote').getValues('.bluetoothRemoteAttr')[0];
		$.ajax({
			type: "POST",
			url: "plugins/"+plugin+"/core/ajax/"+plugin+".ajax.php",
			data: {
				action: "dependancyRemote",
				remoteId: $('.li_bluetoothRemote.active').attr('data-bluetoothRemote_id'),
			},
			dataType: 'json',
			error: function (request, status, error) {
				handleAjaxError(request, status, error,$('#div_bluetoothRemoteAlert'));
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_bluetoothRemoteAlert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				$('#div_bluetoothRemoteAlert').showAlert({message: '{{Envoi réussi}}', level: 'success'});
			}
		});
	});

	$('.bluetoothRemoteAction[data-action=stopremote]').on('click',function(){
		var bluetooth_remote = $('.bluetoothRemote').getValues('.bluetoothRemoteAttr')[0];
		$.ajax({
			type: "POST",
			url: "plugins/"+plugin+"/core/ajax/"+plugin+".ajax.php",
			data: {
				action: "stopremote",
				remoteId: $('.li_bluetoothRemote.active').attr('data-bluetoothRemote_id'),
			},
			dataType: 'json',
			error: function (request, status, error) {
				handleAjaxError(request, status, error,$('#div_bluetoothRemoteAlert'));
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_bluetoothRemoteAlert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				$('#div_bluetoothRemoteAlert').showAlert({message: '{{Arrêt réussi}}', level: 'success'});
			}
		});
	});

	$('.bluetoothRemoteAction[data-action=remotelearn]').on('click',function(){
		var bluetooth_remote = $('.bluetoothRemote').getValues('.bluetoothRemoteAttr')[0];
		$.ajax({
			type: "POST",
			url: "plugins/"+plugin+"/core/ajax/"+plugin+".ajax.php",
			data: {
				action: "remotelearn",
				remoteId: $('.li_bluetoothRemote.active').attr('data-bluetoothRemote_id'),
				state: $(this).attr('data-type'),
			},
			dataType: 'json',
			error: function (request, status, error) {
				handleAjaxError(request, status, error,$('#div_bluetoothRemoteAlert'));
			},
			success: function (data) {
				if (data.state != 'ok') {
					$('#div_bluetoothRemoteAlert').showAlert({message: data.result, level: 'danger'});
					return;
				}
				$('#div_bluetoothRemoteAlert').showAlert({message: '{{Envoi réussi}}', level: 'success'});
			}
		});
	});

	$('.bluetoothRemoteAction[data-action=remove]').on('click',function(){
		bootbox.confirm('{{Etês-vous sûr de vouloir supprimer cette Antenne ?}}', function (result) {
			if (result) {
				$.ajax({
					type: "POST",
					url: "plugins/bluetooth/core/ajax/bluetooth.ajax.php",
					data: {
						action: "remove_bluetoothRemote",
						id: $('.li_bluetoothRemote.active').attr('data-bluetoothRemote_id'),
					},
					dataType: 'json',
					error: function (request, status, error) {
						handleAjaxError(request, status, error,$('#div_bluetoothRemoteAlert'));
					},
					success: function (data) {
						if (data.state != 'ok') {
							$('#div_bluetoothRemoteAlert').showAlert({message: data.result, level: 'danger'});
							return;
						}
						$('.li_bluetoothRemote.active').remove();
						$('.bluetoothRemote').hide();
						$('.remoteThumbnailDisplay').show();
						$('#md_modal').dialog('close');
						$('#md_modal').dialog({title: "{{Gestion des antennes bluetooth}}"});
						$('#md_modal').load('index.php?v=d&plugin=bluetooth&modal=bluetooth.remote&id=bluetooth').dialog('open');
					}
				});
			}
		});
	});
window.setInterval(function () {
    displaybluetoothRemoteComm($('.li_bluetoothRemote.active').attr('data-bluetoothRemote_id'));
}, 5000);
</script>
