<?php
/*
@name Dimmer
@author Aymeric HM aka fry <f_r_y_@hotmail.com>
@link https://github.com/f-r-y/yana-dimmer
@licence CC by nc sa
@version 0.0.2
@description Plugin de variateur via pi-blaster (permet de dimmer des led en pwm, ou plus costaud via les composants adaptés)
 https://github.com/sarfata/pi-blaster suivre le readme pour installer
 /!\ pour les possesseurs de rpi rev 2 il faut editer le fichier pi-blaster.c et remplacer '21' par '27' a la ligne 45 avant de compiler (la numérotation des GPIO a changé enntre les deux révisions)
*/

 include('Dimmer.class.php');
 


function dimmer_plugin_setting_page(){
	global $_,$myUser;
	if(isset($_['section']) && $_['section']=='dimmer' ){

		if($myUser!=false){
			$dimmerManager = new Dimmer();
			$dimmers = $dimmerManager->populate();
			$roomManager = new Room();
			$rooms = $roomManager->populate();
			$selected =  new Dimmer();

			//Si on est en mode modification
			if (isset($_['id']))
				$selected = $dimmerManager->getById($_['id']);
			
			?>

		<div class="span9 userBloc">


		<h1>Variateurs</h1>
		<p>Gestion des variateurs via pi-blaster</p>  

		<form action="action.php?action=dimmer_add_dimmer" method="POST">
		<fieldset>
		    <legend>Formulaire du variateur</legend>

		    <div class="left">
			    <label for="nameDimmer">Nom</label>
			    <input type="hidden" name="id" value="<?php echo $selected->getId(); ?>">
			    <input type="text" id="nameDimmer" value="<? echo $selected->getName(); ?>" onkeyup="$('#vocalCommand').html($(this).val());" name="nameDimmer" placeholder="Lumiere Canapé…"/>
			    <small>Commande vocale associée : "YANA, augmente <span id="vocalCommand"></span>"</small>
			    <label for="descriptionDimmer">Description</label>
			    <input type="text" name="descriptionDimmer" value="<?echo $selected->getDescription(); ?>" id="descriptionDimmer" placeholder="Variateur sous le canapé…" />
			    <label for="pinDimmer">Pin GPIO (Numéro sur la raspberry leaf)</label>
			    <input type="text" name="pinDimmer" value="<? echo $selected->getPin(); ?>" id="pinDimmer" placeholder="4, 17, 18 …" />
			    <label for="stepDimmer">Saut (entre 0 et 1, 0.01 pour varier de 0.01 en 0.01)</label>
			    <input type="text" name="stepDimmer" value="<? echo $selected->getStep(); ?>" id="stepDimmer" placeholder="0.01, 0.1" />
			    <label for="roomDimmer">Pièce</label>
			    <select name="roomDimmer" id="roomDimmer">
			    	<?php foreach($rooms as $room){ ?>
			    	<option <? if ($selected->getRoom()== $room->getId()){echo 'selected"selected"';} ?> value="<?php echo $room->getId(); ?>"><?php echo $room->getName(); ?></option>
			    	<?php } ?>
			    </select>
			</div>

  			<div class="clear"></div>
		    <br/><button type="submit" class="btn">Enregistrer</button>
	  	</fieldset>
		<br/>
	</form>

		<table class="table table-striped table-bordered table-hover">
	    <thead>
	    <tr>
	    	<th>Nom</th>
		    <th>Description</th>
		    <th>Pin GPIO</th>
		    <th>Saut</th>
		    <th>Pièce</th>
		    <th></th>
	    </tr>
	    </thead>
	    
	    <?php foreach($dimmers as $dimmer){ 

	    	$room = $roomManager->load(array('id'=>$dimmer->getRoom())); 
	    	?>
	    <tr>
	    	<td><?php echo $dimmer->getName(); ?></td>
		    <td><?php echo $dimmer->getDescription(); ?></td>
		    <td><?php echo $dimmer->getPin(); ?></td>
		    <td><?php echo $dimmer->getStep(); ?></td>
		    <td><?php echo $room->getName(); ?></td>
		    <td><a class="btn" href="action.php?action=dimmer_delete_Dimmer&id=<?php echo $dimmer->getId(); ?>"><i class="icon-remove"></i></a>
		    <a class="btn" href="setting.php?section=dimmer&id=<?php echo $dimmer->getId(); ?>"><i class="icon-edit"></i></a></td>
		    </td>
	    </tr>
	    <?php } ?>
	    </table>
		</div>

<?php }else{ ?>

		<div id="main" class="wrapper clearfix">
			<article>
					<h3>Vous devez être connecté</h3>
			</article>
		</div>
<?php
		}
	}

}

function dimmer_plugin_setting_menu(){
	global $_;
	echo '<li '.(isset($_['section']) && $_['section']=='dimmer'?'class="active"':'').'><a href="setting.php?section=dimmer"><i class="icon-chevron-right"></i> Variateurs</a></li>';
}




function dimmer_display($room){
	global $_;


	$dimmerManager = new Dimmer();
	$dimmers = $dimmerManager->loadAll(array('room'=>$room->getId()));
	
	foreach ($dimmers as $dimmer) {
			
	?>

	<div class="span3">
          <h5><?php echo $dimmer->getName() ?></h5>
		   
		   <p><?php echo $dimmer->getDescription() ?>
		  	</p><ul>
		  		<li>PIN GPIO : <code><?php echo $dimmer->getPin() ?></code></li>
		  		<li>Type : <code>Variateur</code></li>
		  		<li>Emplacement : <code><?php echo $room->getName() ?></code></li>
		  		<li>Valeur : <code><?php echo $dimmer->getValue() ?></code></li>
		  	</ul>
		  <p></p>
		  	 <div class="btn-toolbar">
				<div class="btn-group">
					<a class="btn btn-success" href="action.php?action=dimmer_change_state&engine=<?php echo $dimmer->getId() ?>&amp;code=<?php echo $dimmer->getPin() ?>&amp;state=on"><i class="icon-thumbs-up icon-white"></i></a>
					<a class="btn" href="action.php?action=dimmer_change_state&engine=<?php echo $dimmer->getId() ?>&amp;code=<?php echo $dimmer->getPin() ?>&amp;state=off"><i class="icon-thumbs-down "></i></a>

					<a class="btn btn-success" href="action.php?action=dimmer_change_state&engine=<?php echo $dimmer->getId() ?>&amp;code=<?php echo $dimmer->getPin() ?>&amp;state=more"><i class="icon-thumbs-up icon-white"></i></a>
					<a class="btn" href="action.php?action=dimmer_change_state&engine=<?php echo $dimmer->getId() ?>&amp;code=<?php echo $dimmer->getPin() ?>&amp;state=less"><i class="icon-thumbs-down "></i></a>
				</div>
			</div>
        </div>


	<?php
	}
}

function dimmer_vocal_command(&$response,$actionUrl){
	$dimmerManager = new Dimmer();

	$dimmers = $dimmerManager->populate();
	foreach($dimmers as $dimmer){
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', allume '.$dimmer->getName(),'url'=>$actionUrl.'?action=dimmer_change_state&engine='.$dimmer->getId().'&state=on&webservice=true','confidence'=>'0.9');
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', eteint '.$dimmer->getName(),'url'=>$actionUrl.'?action=dimmer_change_state&engine='.$dimmer->getId().'&state=off&webservice=true','confidence'=>'0.9');
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', augmente '.$dimmer->getName(),'url'=>$actionUrl.'?action=dimmer_change_state&engine='.$dimmer->getId().'&state=more&webservice=true','confidence'=>'0.9');
		$response['commands'][] = array('command'=>VOCAL_ENTITY_NAME.', diminue '.$dimmer->getName(),'url'=>$actionUrl.'?action=dimmer_change_state&engine='.$dimmer->getId().'&state=less&webservice=true','confidence'=>'0.9');
	}
}

function dimmer_action_dimmer(){
	global $_,$conf,$myUser;

	switch($_['action']){
		case 'dimmer_delete_dimmer':
			if($myUser->can('dimmer','d')){
				$dimmerManager = new Dimmer();
				$wdimmerManager->delete(array('id'=>$_['id']));
			}
			header('location:setting.php?section=dimmer');
		break;

		case 'dimmer_add_dimmer':
			if($myUser->can('dimmer',$_['id']!=''? 'u' : 'c')){
				$dimmerManager = new Dimmer();
				$dimmer = $_['id']!=''?$dimmerManager->getById($_['id']): new Dimmer();
				
				$dimmer->setName($_['nameDimmer']);
				$dimmer->setDescription($_['descriptionDimmer']);
				$dimmer->setPin($_['pinDimmer']);
				$dimmer->setRoom($_['roomDimmer']);
				$dimmer->setStep($_['stepDimmer']);
				$dimmer->setValue(0);
				$dimmer->save();
			}
			header('location:setting.php?section=dimmer');

		break;
		case 'dimmer_change_state':
			global $_,$myUser;

			
			if($myUser->can('dimmer','u')){
				$dimmerManager = new Dimmer();
				$dimmer = $dimmerManager->getById($_['engine']);

				if ($_['state'] == 'on') {
					$dimmer->setValue(1);
					$value = 1;
				}
				if ($_['state'] == 'off') {
					$dimmer->setValue(0);
					$value = 0;
				}
				if ($_['state'] == 'more') {
					$value = $dimmer->getValue() + $dimmer->getStep();
					if ($value>1) {
						$dimmer->setValue(1);
					} else {
						$dimmer->setValue($value);
					}
					$value = $dimmer->getValue();
				}
				if ($_['state'] == 'less') {
					$value = $dimmer->getValue() - $dimmer->getStep();
					if ($value<0) {
						$dimmer->setValue(0);
					} else {
						$dimmer->setValue($value);
					}
					$value = $dimmer->getValue();
				}
				$dimmer->save();
					$cmd = 'echo "'.$dimmer->getPin().'='.$value.'" > /dev/pi-blaster';
					system($cmd,$out);

				
				if(!isset($_['webservice'])){
					header('location:index.php?module=room&id='.$dimmer->getRoom());
				}else{
					$affirmations = array(	'A vos ordres!',
								'Bien!',
								'Oui commandant!',
								'Avec plaisir!',
								'J\'aime vous obéir!',
								'Avec plaisir!',
								'Certainement!',
								'Je fais ça sans tarder!',
								'Avec plaisir!',
								'Oui chef!');
					$affirmation = $affirmations[rand(0,count($affirmations)-1)];
					$response = array('responses'=>array(
											array('type'=>'talk','sentence'=>$affirmation)
														)
									);

					$json = json_encode($response);
					echo ($json=='[]'?'{}':$json);
				}
			}else{
				$response = array('responses'=>array(
											array('type'=>'talk','sentence'=>'Je ne vous connais pas, je refuse de faire ça!')
														)
									);
				echo json_encode($response);
			}
		break;
	}
}


Plugin::addCss("/css/style.css"); 
Plugin::addHook("action_post_case", "dimmer_action_dimmer"); 
Plugin::addHook("node_display", "dimmer_display");   
Plugin::addHook("setting_bloc", "dimmer_plugin_setting_page");
Plugin::addHook("setting_menu", "dimmer_plugin_setting_menu");  
Plugin::addHook("vocal_command", "dimmer_vocal_command");
?>