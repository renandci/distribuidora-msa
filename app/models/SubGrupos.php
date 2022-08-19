<?php

/**
 * @author Renan Henrique <renan@dcisuporte.com.br>
 * @company Data Control Infomatica
 */

/**
 * Description of SubGrupos
 *
 * @author renan
 */
class SubGrupos extends ActiveRecord
{
    static $table = 'subgrupos';
    
	static $before_save = ['in_store'];
	
	static $validates_presence_of = [];
	
	// static $has_one = [ [
	// 		'test',
	// 		'class_name' => 'ProdutosMenus',
	// 		'foreign_key' => 'id_subgrupo',
	// 		'primary_key' => 'id',
	// 	]
	// ];

	static $belongs_to = [ [
			'parent', 
			'primary_key' => 'id',
			'foreign_key' => 'parent_id', 
			'class_name' => 'SubGrupos'
		]
	];

	static $has_many = [ [
			'children', 
			'primary_key' => 'id',
			'foreign_key' => 'parent_id', 
			'class_name' => 'SubGrupos'
		],
		['parent_children', 'foreign_key' => 'parent_id', 'primary_key' => 'id', 'class_name' => 'SubGrupos'],
	];
}
