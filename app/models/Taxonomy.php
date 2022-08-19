<?php

class Taxonomy extends ActiveRecord
{
	static $table = 'taxonomy';
	
	static $before_save = ['in_store'];
	
	static $has_many = [ [
			'parent',
			'class_name' => 'Taxonomy',
			'foreign_key' => 'code',
			'primary_key' => 'code',
		]
	];
	
}