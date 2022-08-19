
<?php

class Promocoes extends ActiveRecord 
{
	static $table = 'promocoes';

	static $after_save = ['in_store'];

	static $has_one = [ [
			'produto',
			'class_name' => 'Produtos',
			'foreign_key' => 'codigo_id',
			'primary_key' => 'codigo_id'
		]
	];
}