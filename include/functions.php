<?php
/*
Copyright (C) 2016 Miguel de Dios
--
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
higher any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software Foundation,
Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA
*/

function get_parameter($parameter, $default = null)
{
	if (isset($_POST[$parameter]))
		return $_POST[$parameter];
	elseif (isset($_GET[$parameter]))
		return $_GET[$parameter];
	else
		return $default;
}

function set_session_var($var, $value = null)
{
	session_start();
	
	$_SESSION[$var] = $value;
	
	session_write_close();
}

function get_sesion_var($var, $default = null)
{
	$return = $default;
	
	session_start();
	
	if (isset($_SESSION[$var]))
		$return = $_SESSION[$var];
	
	session_write_close();
	
	return $return;
}

function debug($var, $file = null)
{
	$more_info = '';
	if (is_string($var))
	{
		$more_info = 'size: ' . strlen($var);
	}
	elseif (is_bool($var))
	{
		$more_info = 'val: ' . 
			($var ? 'true' : 'false');
	}
	elseif (is_null($var))
	{
		$more_info = 'is null';
	}
	elseif (is_array($var))
	{
		$more_info = count($var);
	}
	
	if ($file === true)
		$file = '/tmp/debug';
	
	if (!empty($file))
	{
		$f = fopen($file, "a");
		ob_start();
		echo date("Y/m/d H:i:s") . " (" . gettype($var) . ") " . $more_info . "\n";
		print_r($var);
		echo "\n\n";
		$output = ob_get_clean();
		fprintf($f,"%s",$output);
		fclose($f);
	}
	else
	{
		echo "<pre>" .
			date("Y/m/d H:i:s") . " (" . gettype($var) . ") " . $more_info .
			"</pre>";
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}
}

function logout()
{
	global $config;
	
	$config['user'] = null;
	set_session_var('user', $user);
}

function login()
{
	global $config;
	
	$user = get_parameter('user', '');
	$password = md5(get_parameter('password', ''));
	
	$validate = (bool)db_get_value('users', 'validate',
		array('user' => $user, 'password' => $password));
	
	if ($validate)
	{
		$config['user'] = $user;
		set_session_var('user', $user);
	}
	
	return $validate;
}

function get_measure_types()
{
	$measure_types = db_get_rows('measure_types', array('id', 'measure_type'));
	
	
	if (empty($measure_types))
		$measure_types = array();
	
	
	
	$return = array();
	foreach ($measure_types as $measure)
	{
		$return[$measure['id']] = $measure['measure_type'];
	}
	
	return $return;
}

function db_get_rows($table, $fields = null, $conditions = null)
{
	global $config;
	
	
	$where_sql = "1=1";
	if (count($conditions) >= 1)
	{
		foreach ($conditions as $conditions_field => $condition)
		{
			$where_sql .= " AND ";
			
			switch (key($condition)) {
				case '=':
					$where_sql .= $conditions_field . " = ?";
					break;
				case 'like':
					$where_sql .= $conditions_field . " like ?";
					break;
			}
		}
	}
	else
	{
		$conditions = array();
	}
	
	if (isset($fields))
	{
		if (!is_array($fields))
			$fields = array($fields);
		
		$select_sql = implode(",", $fields);
	}
	else
		$select_sql = "*";
	
	$stmt = $config['db']->prepare("
		SELECT " . $select_sql . "
		FROM " . $table . "
		WHERE " . $where_sql);
	
	$i = 1;
	foreach ($conditions as $condition)
	{
		$stmt->bindValue($i, reset($condition), PDO::PARAM_STR);
		$i++;
	}
	
	$stmt->execute();
	
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	if (empty($rows))
		return array();
	else
		return $rows;
}

function db_get_value($table, $field, $condition)
{
	global $config;
	
	$where_sql = "";
	if (count($condition) > 1)
	{
		$first = true;
		foreach ($condition as $condition_field => $value)
		{
			if (!$first)
				$where_sql .= " AND ";
			$first = false;
			
			$where_sql .= $condition_field . " = ?";
		}
	}
	elseif (count($condition) == 1)
	{
		$where_sql =  key($condition) . " = ?";
	}
	
	$stmt = $config['db']->prepare("SELECT " . $field . " FROM " . $table . " where " . $where_sql);
	
	$i = 1;
	foreach ($condition as $value_condition)
	{
		$stmt->bindValue($i, $value_condition, PDO::PARAM_STR);
		$i++;
	}
	
	$stmt->execute();
	
	$row = $stmt->fetch();
	
	if (empty($row))
		return null;
	else
		return $row[$field];
}

function db_connect()
{
	global $config;
	
	try
	{
		
		$config['db'] = new PDO(
			'mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'],
			$config['db_user'],
			$config['db_password'],
			array(
				PDO::ATTR_PERSISTENT => true
			)
		);
		
		return true;
	}
	catch(PDOException $e)
	{
		return false;
	}
}

function user_logged()
{
	global $config;
	
	if (isset($config['user']))
	{
		if (is_null($config['user']))
			return false;
		else
			return true;
	}
	
	return false;
}

function set_message($message)
{
	global $config;
	
	$config['message'] = $message;
	set_session_var("message", $config['message']);
}


function get_message()
{
	global $config;
	
	if (isset($config['message']))
	{
		$message = $config['message'];
	}
	else {
		$message = get_sesion_var('message');
	}
	
	return $message;
}
?>