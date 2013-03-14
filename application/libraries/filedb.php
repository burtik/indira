<?php
	
class Filedb{
	
	private $dir = 'storage/db';

	public static $file_pattern = "<?php 
return array( %s );";

	public static $import_dir = 'storage/db';

	public static $table;

	public static $models_dir = 'application/models';

	public static $storage_dir = 'storage/db';

	public static $model;

	protected static $_inst = NULL;


	public function __construct()
	{

		if(!isset(static::$table)){

			static::$table = $this->table();

		}


		return $this->init();
	}



	public static function get_table($table=null){

		if(!empty(static::$table)){

			return static::$table;

		}elseif($table != null){

			return strtolower(Str::plural(class_basename($table)));

		}

	}



	public function table()
	{
		
		return static::$table = strtolower(Str::plural(class_basename($this)));

	}



	static function get_tables(){

		$tables_by_model = array_diff(scandir(static::$models_dir), array('..', '.'));

		foreach ($tables_by_model as $table) {

			$table_model_file = substr($table, 0, -4);

			$tables[] = $table_model_file::get_table($table_model_file);

		}
		
		return $tables;
	}


	static function get_db_size(){

		$size = 0;
		$tables = static::get_tables();

		foreach ($tables as $table) {

			$size += static::get_table_size($table);

		}

		return $size;
	}


	static function get_table_size($table){

		$size = 0;
		$records = array_diff(scandir('storage/db/'.$table), array('..', '.'));
		
		foreach ($records as $record) {
			
			$size += filesize('storage/db/'.$table.'/'.$record);
		
		}

		return $size;

	}

	static function get_table_records($table){

		return count(array_diff(scandir('storage/db/'.$table), array('..', '.')));

	}


	static function get_db_records(){

		$records = 0;
		$tables = static::get_tables();

		foreach ($tables as $table) {

			$records += static::get_table_records($table);

		}

		return $records;
	}


	
	public function init(){

		$this->full_table = array();
		if(is_dir($this->dir.'/'.static::$table)){

			$rows = scandir($this->dir.'/'.static::$table);

		}else{

			return 'No such table ('.$this->dir.'/'.static::$table.')!';

		}

		foreach ($rows as $key => $value) {
			
			if(strpos($value,'.php') !== false){
				
				$file_id = substr($value, 0, -4);

				$this->full_table[$file_id] = $this->get_file($file_id);
			
			}
		}

		return $this;

	}



	public function get_file($id){

		return static::decode_obj(require $this->dir.'/'.static::$table.'/'.$id.'.php');

	}



	private function get($columns=null){

		if($columns){

			$this->_only($columns);

			if($this->result){

				return static::array_to_object($this->result);
			}else{

				return $this->result;
			}

		}else{

			if($this->result){

				return static::array_to_object($this->result);
			}else{

				return '';
			}

		}

	}



	private function find($id){

		$id = intval($id);

		foreach ($this->_t() as $file_id => $row) {

			if($row->id == $id){

				return $this->get_file($id);

			}
		}
	}



	private function first(){
		return $this->limit(0,1)->get();
	}



	private function where_id($id){

		$id = intval($id);

		foreach ($this->_t() as $file_id => $row) {

			if(is_array($id)){

				if(in_array($row->id, $id)){

					$res[$file_id] = $row;

				}

			}else{

				if($row->id == $id){

					$res[$file_id] = $row;

				}

			}
		
		}
		
		if(isset($res)){
			$this->result = $res;
		}else{
			$this->result = '';
		}
		return $this;
	}



	private function where_not_id($id){

		$id = intval($id);

		foreach ($this->_t() as $file_id => $row) {

			if(is_array($id)){

				if(!in_array($file_id, $id)){

					$res[$file_id] = $row;

				}

			}else{

				if($file_id !== $id){

					$res[$file_id] = $row;

				}

			}
		
		}
		
		if(isset($res)){
			$this->result = $res;
		}else{
			$this->result = '';
		}
		return $this;
	}


/*
////////////////////////////////////////////////////////*Think about it*//*

	private function distinct(){

		$this->result = array_unique(static::object_to_array($this->_t()));

		return $this;
	}

	private function distinct(){

		$array = static::object_to_array($this->_t());
		$this->result = static::array_to_object(array_unique($array));

		return $this;
	}
*/



	private function limit($start, $end){

		if($this->_t()){
			$this->result = array_slice($this->_t(), $start, $end);
		}

		return $this;

	}

	private function skip($num){
		$this->limit($num, null);

		return $this;
	}

	private function take($num){
		$this->limit(0, $num);

		return $this;
	}


	private function order_by($field, $type='ASC'){

		if($this->_t()){
			foreach ($this->_t() as $file_id => $row) {
				$res[$file_id] = $row->{$field}; 
			}

			if($type == 'ASC' || $type == 'asc'){
				asort($res);
			}
			if($type == 'DESC' || $type == 'desc'){
				arsort($res);
			}


			$table = $this->_t();
			foreach ($res as $id => $value) {
				$res[$id] = $table[$id];
			}

			$this->result = $res;
		}else{
			$this->result = '';
		}

		return $this;

	}



	private function where($column, $operator, $data){
		
		$this->_switch_select($column, $operator, $data, false, false);

		return $this;

	}


	private function and_where($column, $operator, $data){
		
		$this->_switch_select($column, $operator, $data, true, false);

		return $this;

	}


	private function or_where($column, $operator, $data){
		
		$this->_switch_select($column, $operator, $data, false, true);

		return $this;

	}


	public function _switch_select($column, $operator, $data, $and, $or){

		if($and || $or){
			if(!isset($this->result)){
				$this->result = '';
			}
		}

		switch ($operator) {
		    case '=':
		        $this->where_equal($column, $data, $and, $or);
		        break;
		    case '<>':
		    case '!=':
		    	$this->where_not($column, $data, $and, $or);
		    	break;
		    case 'like':
		    	$this->where_like($column, $data, $and, $or);
		    	break;
		    case '>':
		    	$this->where_compare($column, $data, $and, $or, 'greater');
		    	break;
		   	case '<':
		    	$this->where_compare($column, $data, $and, $or, 'less');
		    	break;
		    case '>=':
		    	$this->where_compare($column, $data, $and, $or, 'greater_or_equal');
		    	break;
		   	case '<=':
		    	$this->where_compare($column, $data, $and, $or, 'less_or_equal');
		    	break;
		    default:
		    	die('Wrong operator ('. $operator .') provided or this operator is not supported');

		}

		return $this;

	}


	private function where_compare($column, $data, $and=false, $or=false, $type){

		if($this->_t_where($and, $or)){

			foreach ($this->_t_where($and, $or) as $file_id => $row) {

				if(isset($row->{$column})){

					if(is_array($data)){

						die('Arrays is not supported in "Greater than", "Less than" and other similar operations');

					}else{

						$data = intval($data);

						if($type == 'greater')
						{

							if($row->{$column} > $data){

								if($or){
									$this->result[$file_id] = $row;
								}else{
									$res[$file_id] = $row;
								}

							}

						}elseif($type == 'less'){

							if($row->{$column} < $data){

								if($or){
									$this->result[$file_id] = $row;
								}else{
									$res[$file_id] = $row;
								}

							}

						}elseif($type == 'greater_or_equal'){

							if($row->{$column} >= $data){

								if($or){
									$this->result[$file_id] = $row;
								}else{
									$res[$file_id] = $row;
								}

							}

						}elseif($type == 'less_or_equal'){

							if($row->{$column} <= $data){

								if($or){
									$this->result[$file_id] = $row;
								}else{
									$res[$file_id] = $row;
								}
							}
						}
					}
				}
			}

			if($and || $or){
				if(isset($res)){
					$this->result = $res;
				}elseif($and && !$or){
					$this->result = '';
				}
			}else{
				if(isset($res)){
					$this->result = $res;
				}else{
					$this->result = '';
				}
			}

		}else{

			$this->result = '';

		}

		return $this;
	}


	private function where_equal($column, $data, $and=false, $or=false){

		if($this->_t_where($and, $or)){

			foreach ($this->_t_where($and, $or) as $file_id => $row) {

				if(isset($row->{$column})){

					if(is_array($data)){

						if(in_array($row->{$column}, $data)){

							if($or){
								$this->result[$file_id] = $row;
							}else{
								$res[$file_id] = $row;
							}
						}

					}else{

						if($row->{$column} ==  $data){

							if($or){
								$this->result[$file_id] = $row;
							}else{
								$res[$file_id] = $row;
							}
						}
					}
				}
			}

			if($and || $or){
				if(isset($res)){
					$this->result = $res;
				}elseif($and && !$or){
					$this->result = '';
				}
			}else{
				if(isset($res)){
					$this->result = $res;
				}else{
					$this->result = '';
				}
			}

		}else{

			$this->result = '';

		}

		return $this;
	}



	private function where_not($column, $data, $and=false, $or=false){
		
		if($this->_t_where($and, $or)){

			foreach ($this->_t_where($and, $or) as $file_id => $row) {

				if(isset($row->{$column})){

					if(is_array($data)){

						if(!in_array($row->{$column}, $data)){

							if($or){
								$this->result[$file_id] = $row;
							}else{
								$res[$file_id] = $row;
							}

						}

					}else{

						if($row->{$column} !==  $data){

							if($or){
								$this->result[$file_id] = $row;
							}else{
								$res[$file_id] = $row;
							}

						}
					}
				}
			}

			if($and || $or){
				if(isset($res)){
					$this->result = $res;
				}elseif($and && !$or){
					$this->result = '';
				}
			}else{
				if(isset($res)){
					$this->result = $res;
				}else{
					$this->result = '';
				}
			}

		}else{

			$this->result = '';

		}

		return $this;
	}


	private function where_like($column, $data, $and=false, $or=false){

		if($this->_t_where($and, $or)){

			foreach ($this->_t_where($and, $or) as $file_id => $row) {

				if(isset($row->{$column})){

					if(is_array($data)){

						foreach($data as $needle){

							if(strripos($row->{$column}, $needle) !== false){

								if($or){
									$this->result[$file_id] = $row;
								}else{
									$res[$file_id] = $row;
								}

							}
						}

					}else{

						if(strripos($row->{$column}, $data) !== false){

							if($or){
								$this->result[$file_id] = $row;
							}else{
								$res[$file_id] = $row;
							}

						}
					}
				}
			}

			if($and || $or){
				if(isset($res)){
					$this->result = $res;
				}elseif($and && !$or){
					$this->result = '';
				}
			}else{
				if(isset($res)){
					$this->result = $res;
				}else{
					$this->result = '';
				}
			}

		}else{

			$this->result = '';

		}

		return $this;
	}



	private function all(){

		return $this->_t();

	}


	public function _t(){

		if(isset($this->result)){

			return $this->result;

		}else{

		 	return $this->full_table;

		}

	}


	public function _t_where($and, $or){

		if($and){
			if(isset($this->result)){
				$table = $this->result;
			}else{
				die('"and_where" or "or_where" must be at least as second parameter!');
			}
		}elseif($or){
			$table = $this->full_table;
		}else{
			$table = $this->_t();
		}

		return $table;

	}


	public function _only($column){

		if($this->_t()){

			foreach ($this->_t() as $file_id => $row) {

				if(is_array($column)){

					$mid_res = array();
					
					foreach ($column as $value) {

						if(isset($row->{$value})){
						
							 $mid_res = array_merge($mid_res, array($value => $row->{$value}));
						}
					}

					if($mid_res){
						$res[$file_id] = $mid_res;
					}

				}else{

					if(isset($row->{$column})){

						$res[$file_id] = array($column => $row->{$column});

					}
				}
			}

			if(isset($res)){
				$this->result = $res;
			}else{
				$this->result = '';
			}

			return $this;

		}else{

			return null;

		}

	}


	private function only($column){

		if($this->_t()){

			foreach ($this->_t() as $file_id => $row) {

				if(is_array($column)){
					
					foreach ($column as $value) {

						if(isset($row->{$value})){
						
							$res[$file_id][$value] = $row->{$value};
						}
					}

				}else{

					if(isset($row->{$column})){

						$res = $row->{$column};
					}

				}
			}

			if(isset($res)){
				$this->result = $res;
			}else{
				$res = '';
			}
			return $res;

		}else{

			return null;

		}
	}
	

	private function where_only($column, $data, $only=null){

		foreach ($this->_t() as $file_id => $row) {
			
			if(isset($row->{$column})){

				if($row->{$column} == $data){

					if(is_array($only)){
						
						foreach ($only as $value) {
							
							$res[$value] = $row->{$value};
						
						}

					}elseif($only){

						$res[$file_id] = $row->{$only};
					
					}else{

						$res[$file_id] = $row->{$column};

					}
				}
			}
		}

		if(isset($res)){
			$this->result = $res;
		}else{
			$this->result = '';
		}
		return $this;

	}


	public function where_in($object, $column, $data){

		foreach ($object as $file => $row) {
			
			if($row->{$column} == $data){

				$res[$file] = static::array_to_object($row);

			}
		}

		if(isset($res)){
			$this->result = $res;
		}else{
			$this->result = '';
		}
		return $this;
	}



	public static function array_to_object($array){
		
		$obj = new stdClass;

		foreach($array as $k => $v) {

			if(is_array($v)){

				$obj->{$k} = static::array_to_object($v);

			}else{

				$obj->{$k} = $v;

			}

		}
		return $obj;
	}



	public static function object_to_array($data, $once=false){

	    if (is_array($data) || is_object($data))
	    {
	        $result = array();
	        foreach ($data as $key => $value){

	        	if($once){
	        		$result[$key] = $value;
	        	}else{
	        		$result[$key] = static::object_to_array($value);
	        	}

	        }
	        return $result;
	    }
	    return $data;
	}



	public static function decode_table($table, $file){

		$file = require static::$dir.'/'.$table.'/'.$file.'.php';

		foreach ($file as $key => $value) {

			$result[$key] = rawurldecode($value);

		}

		return static::array_to_object($result);

	}


	public static function decode_obj($obj){

		foreach ($obj as $key => $value) {

			$result[$key] = rawurldecode($value);

		}

		return static::array_to_object($result);

	}



	public static function import_from_db(){

		$dbobj = DB::query('SHOW TABLES');
		$db_prefix = Config::get('database.connections.'.Config::get('database.default').'.prefix');
		$db_name = Config::get('database.connections.'.Config::get('database.default').'.database');


		foreach ($dbobj as $key => $table_data) {
			
			$table = DB::query('SELECT * FROM '.$table_data->{'tables_in_'.$db_name});
			$dir_name = str_replace($db_prefix, '', $table_data->{'tables_in_'.$db_name});

			foreach($table as $key => $row)
			{
				
				

				$file_dir 	= 	static::$import_dir."/".$dir_name."/".$row->id.".php";

$file 		= 	"<?php 
";
$file 		.= 	"return array( ";
				foreach ($row as $key => $value) {

					if($key == 'id'){

$file 	.= 	"'".$key."' => ".rawurlencode($value).", 
";

					}else{

$file 	.= 	"'".$key."' => '".rawurlencode($value)."', 
";

					}

				}
$file 		.= 	");";

				if(!file_exists(dirname($file_dir))){
	    			
	    			mkdir(dirname($file_dir), 0777, true);

	    		}

				file_put_contents($file_dir, $file);

			}

		}

	}



	public static function _row($key, $value, $timestamp=null){

		if($key == $timestamp){

			return "'".$key."' => '".rawurlencode(date("Y-m-d H:i:s"))."', 
";
		}elseif($key == 'id'){

			return "'".$key."' => ".intval($value).", 
";
		}else{

			return "'".$key."' => '".rawurlencode($value)."', 
";
		}

	}




	private function update($data=array()){

		$result = "";
		$table_dir = $this->dir."/".static::$table;

		foreach ($this->_t() as $row) {

			$file_dir 	= 	$this->dir."/".static::$table."/".$row->id.".php";
			$file = "";

			foreach (static::$model as $model_key => $default_value) {

				if(array_key_exists($model_key, $data)){

					$file .= static::_row($model_key, $data[$model_key], 'updated_at');

				}elseif(isset($row->$model_key)){

					$file .= static::_row($model_key, $row->$model_key, 'updated_at');

				}else{

					$file .= static::_row($model_key, $default_value);

 				}			
			}

			$file = sprintf(static::$file_pattern, $file);
			
			if(!is_dir($table_dir)){
    			
    			mkdir($table_dir, 0755, true);

    		}

    		$result .= file_put_contents($file_dir, $file);
		}
 
		return $result;
	}



	private function insert($data=array()){

		$table_dir = $this->dir."/".static::$table;
		$file = "";

		if(!is_dir($table_dir)){
			
			mkdir($table_dir, 0755, true);

		}

		$rows = scandir($this->dir.'/'.static::$table);
		
		foreach ($rows as $value) {
			$rows_1[] = str_replace('.php', '', $value);
		}

		$max_id = intval(max($rows_1));
		$new_id = ++$max_id;
		$file_dir 	= 	$this->dir."/".static::$table."/".$new_id.".php";

		foreach (static::$model as $model_key => $default_value) {

			if(array_key_exists($model_key, $data)){

				$file .= static::_row($model_key, $data[$model_key], 'created_at');

			}else{

				if($model_key == 'id'){

					$file .= static::_row($model_key, $new_id);

				}elseif($model_key == 'created_at'){

					$file .= static::_row($model_key, $data, 'created_at');

				}else{

					$file .= static::_row($model_key, $default_value, 'created_at');
				}
			}			
		}

		$file = sprintf(static::$file_pattern, $file);
			
		if(!is_dir($table_dir)){
			
			mkdir($table_dir, 0755, true);

		}

		file_put_contents($file_dir, $file);

		return $new_id;
	}



	private function delete($ids){

		if(is_array($ids)){
			
			foreach($ids as $id){
			
				return unlink($this->dir."/".static::$table."/".$id.".php");

			}

		}else{

			return unlink($this->dir."/".static::$table."/".$ids.".php");

		}

	}


	private function add_qrcode($id) {		
		//QR-Code SETTINGS
			$filename = $id.'_post.png';
			$errorCorrectionLevel = 'H';
			$matrixPointSize = 2;
			$PNG_WEB_DIR = '/uploads/';
			$filename = $PNG_WEB_DIR.$filename;
			$QR_data = Config::get('application.url').'/'.$id;
			
		//RUN QR-Code GENERATION
			QRcode::png($QR_data, './public'.$filename, $errorCorrectionLevel, $matrixPointSize, 2);
		
		//ADD QR-Code INTO DB
		
		return '.'.$filename;
		
	}


    final private function  __clone() { }

	public static function __callStatic($method, $parameters) 
	{
		$model = get_called_class();
		if ($model::$_inst === NULL || $model::$_inst !== $model)
		{
			$model::$_inst = new $model;
		}

		return call_user_func_array(array($model::$_inst, $method), $parameters);
		
	}


	public function __destruct() {
		
   	}


	public function __call($method, $parameters) 
	{
		$model = get_called_class();
		return call_user_func_array(array($model::$_inst, $method), $parameters);
	}


}