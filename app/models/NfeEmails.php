<?php

class NfeEmails extends ActiveRecord
{
  static $table = 'nfe_emails';

  static $before_save = ['in_store'];

  static $validates_presence_of = [];

  static $has_one = [
    [
      'emitente',
      'class_name' => 'NfeEmitentes',
      'primary_key' => 'id_nfe_emitentes',
      'foreign_key' => 'id'
    ]
  ];

  static $has_many = [
    [
      'emitentes',
      'class_name' => 'NfeEmitentes',
      'primary_key' => 'id_nfe_emitentes',
      'foreign_key' => 'id'
    ]
  ];
}
