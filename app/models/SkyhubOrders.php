<?php

class SkyhubOrders extends ActiveRecord
{
	static $table = 'skyhub_orders';

	static $has_many = [[
			'skyhub_produto',
			'class_name' => 'SkyhubProdutos',
			'foreign_key' => 'id_skyhub_orders',
			'primary_key' => 'id',
		]
	];
}