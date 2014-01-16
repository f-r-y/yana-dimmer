<?php
$table = new Dimmer();
$table->drop();

$table_section = new Section();
$id_section = $table_section->load(array("label"=>"dimmer"))->getId();
$table_section->delete(array('label'=>'dimmer'));

$table_right = new Right();
$table_right->delete(array('section'=>$id_section));

?>