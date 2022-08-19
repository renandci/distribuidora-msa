<?php

class SkyhubLog extends ActiveRecord
{
	static $table = 'skyhub_log';

	static $has_one = [
		[
			'produto',
			'class_name' => 'Produtos',
			'primary_key' => 'id_produtos',
			'foreign_key' => 'id'
		]
	];
    
}