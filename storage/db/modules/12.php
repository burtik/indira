<?php 
return array( 'id' => 12, 'created_at' => '1374770608', 'updated_at' => '1374801548', 'name' => 'modules', 'active' => 'true', 'settings' => array('id' => array('editable' => 'false', 'editor' => 'true', 'table' => 'false', 'validator' => 'predetermined', 'field' => array('type' => 'hidden', 'attributes' => array('style' => 'display%3Anone', 'class' => 'hidden', ), ), ), 'created_at' => array('editable' => 'true', 'editor' => 'false', 'table' => 'false', 'validator' => 'required', 'field' => array('type' => 'datetime', ), ), 'updated_at' => array('editable' => 'true', 'editor' => 'false', 'table' => 'false', 'validator' => 'required', 'field' => array('type' => 'datetime', ), ), 'name' => array('editable' => 'false', 'editor' => 'true', 'table' => 'true', 'validator' => 'required%7Cmax%3A25%7Cmin%3A2', 'field' => array('type' => 'text', 'attributes' => array('required' => 'true', 'maxlength' => '25', ), ), ), 'active' => array('editable' => 'true', 'editor' => 'true', 'table' => 'true', 'validator' => 'required', 'field' => array('type' => 'select', 'attributes' => array('required' => 'true', ), ), ), 'settings' => array('editable' => 'false', 'editor' => 'true', 'table' => 'false', 'validator' => 'predetermined', 'field' => array('type' => 'json', 'attributes' => array('rows' => '4', ), ), ), 'default_settings' => array('editable' => 'false', 'editor' => 'false', 'table' => 'false', 'validator' => 'predetermined', ), 'link' => array('editable' => 'true', 'editor' => 'true', 'table' => 'true', 'validator' => 'max%3A35', 'field' => array('type' => 'text', 'attributes' => array('required' => 'true', 'maxlength' => '35', ), ), ), 'access' => array('editable' => 'true', 'editor' => 'true', 'table' => 'true', 'validator' => 'required', 'field' => array('type' => 'select', 'attributes' => array('required' => 'true', ), ), ), 'view_access' => array('editable' => 'true', 'editor' => 'true', 'table' => 'true', 'validator' => 'required', 'field' => array('type' => 'select', 'attributes' => array('required' => 'true', ), ), ), ), 'default_settings' => array('id' => array('editable' => 'false', 'editor' => 'true', 'table' => 'false', 'validator' => 'predetermined', 'field' => array('type' => 'hidden', 'attributes' => array('style' => 'display%3Anone', 'class' => 'hidden', ), ), ), 'created_at' => array('editable' => 'true', 'editor' => 'false', 'table' => 'false', 'validator' => 'required', 'field' => array('type' => 'datetime', ), ), 'updated_at' => array('editable' => 'true', 'editor' => 'false', 'table' => 'false', 'validator' => 'required', 'field' => array('type' => 'datetime', ), ), 'name' => array('editable' => 'false', 'editor' => 'true', 'table' => 'true', 'validator' => 'required%7Cmax%3A25%7Cmin%3A2', 'field' => array('type' => 'text', 'attributes' => array('required' => 'true', 'maxlength' => '25', ), ), ), 'active' => array('editable' => 'true', 'editor' => 'true', 'table' => 'true', 'validator' => 'required', 'field' => array('type' => 'select', 'attributes' => array('required' => 'true', ), ), ), 'settings' => array('editable' => 'false', 'editor' => 'true', 'table' => 'false', 'validator' => 'predetermined', 'field' => array('type' => 'json', 'attributes' => array('rows' => '4', ), ), ), 'default_settings' => array('editable' => 'false', 'editor' => 'false', 'table' => 'false', 'validator' => 'predetermined', ), 'link' => array('editable' => 'true', 'editor' => 'true', 'table' => 'false', 'validator' => 'max%3A35', 'field' => array('type' => 'text', 'attributes' => array('required' => 'true', 'maxlength' => '35', ), ), ), 'access' => array('editable' => 'true', 'editor' => 'true', 'table' => 'true', 'validator' => 'required', 'field' => array('type' => 'select', 'attributes' => array('required' => 'true', ), ), ), ), 'link' => 'false', 'access' => '777', 'view_access' => '400',  );