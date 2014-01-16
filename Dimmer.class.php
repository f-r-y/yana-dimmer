<?php

/*
 @nom: dimmer
 @auteur: Aymeric HM aka fry <f_r_y_@hotmail.com>
 @description:  Classe de gestion des variateurs via pi-blaster
 */

class Dimmer extends SQLiteEntity{

	protected $id,$name,$description,$pin,$room,$step, $value;
	protected $TABLE_NAME = 'plugin_dimmer';
	protected $CLASS_NAME = 'Dimmer';
	
	protected $object_fields = 
	array(
		'id'=>'key',
		'name'=>'string',
		'description'=>'string',
		'pin'=>'int',
		'room'=>'int',
		'step'=>'int',
		'value'=>'float'
	);

	function __construct(){
		parent::__construct();
	}

	function setId($id){
		$this->id = $id;
	}
	
	function getId(){
		return $this->id;
	}

	function getName(){
		return $this->name;
	}

	function setName($name){
		$this->name = $name;
	}

	function getDescription(){
		return $this->description;
	}

	function setDescription($description){
		$this->description = $description;
	}

	function getPin(){
		return $this->pin;
	}

	function setPin($pin){
		$this->pin = $pin;
	}

	function getRoom(){
		return $this->room;
	}

	function setRoom($room){
		$this->room = $room;
	}

	function getStep(){
		return $this->step;
	}

	function setStep($step){
		$this->step = $step;
	}

	function getValue(){
		return $this->value;
	}

	function setValue($value){
		$this->value = $value;
	}

}

?>