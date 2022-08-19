<?php

class SkyhubProdutos extends ActiveRecord
{
	static $table = 'skyhub_produtos';

	static $has_one = [ [
			'order',
			'class_name' => 'SkyhubOrders',
			'primary_key' => 'id_skyhub_orders',
			'foreign_key' => 'id'
		]
	];
    
}