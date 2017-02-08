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

////////////////////////////////////////////////////////////////////////
// CONSTANTS

define("SECONDS_1_HOUR", 360);
define("SECONDS_1_MINUTE", 60);
define("SIZE_TRUNCATE_TEXT", 20);
define("PAGINATION_BLOCK", 5);
define("NUM_PAGES", 5);
define("MAX_POINTS", 5);
////////////////////////////////////////////////////////////////////////

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
		array
		(
			'user' => array("=" => $user),
			'password' => array("=" => $password)
		));
	
	if ($validate)
	{
		$config['user'] = $user;
		$config['id_user'] = db_get_value('users', 'id',
			array('user' => array("=" => $user)));
		set_session_var('user', $user);
		set_session_var('id_user', $config['id_user']);
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

function db_make_where($conditions)
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
					$where_sql .= $conditions_field . " = :where_" . $conditions_field;
					break;
				case 'like':
					$where_sql .= $conditions_field . " like :where_" . $conditions_field;
					break;
			}
		}
	}
	
	return $where_sql;
}

function db_get_count($table, $conditions = null)
{
	global $config;
	
	if (is_null($conditions))
		$conditions = array();
	$where_sql = db_make_where($conditions);
	
	$stmt = $config['db']->prepare("
		SELECT COUNT(*)
		FROM " . $table . "
		WHERE " . $where_sql);
	
	$columns_type = db_get_columns_info($table);
	foreach ($conditions as $column => $condition)
	{
		$stmt->bindValue(":where_" .$column, reset($condition), $columns_type[$column]);
	}
	
	$stmt->execute();
	
	$row = $stmt->fetch();
	
	if (empty($row))
		return 0;
	else
		return $row[0];
}

function db_get_rows_sql($sql)
{
	global $config;
	
	$return = array();
	
	if (!empty($sql))
	{
		$stmt = $config['db']->prepare($sql);
		
		$stmt->execute();
		
		$return = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	
	return $return;
}

function db_get_rows($table, $fields = null, $conditions = null, $limit = null)
{
	global $config;
	
	
	if (is_null($conditions))
		$conditions = array();
	$where_sql = db_make_where($conditions);
	
	if (isset($fields))
	{
		if (!is_array($fields))
			$fields = array($fields);
		
		$select_sql = implode(",", $fields);
	}
	else
		$select_sql = "*";
	
	$limit_sql = "";
	if (!is_null($limit))
	{
		debug(debug_backtrace(), true);
		debug($limit, true);
		
		$limit_sql = "LIMIT " . $limit['limit'] . " OFFSET " . $limit['offset'];
	}
	
	$stmt = $config['db']->prepare("
		SELECT " . $select_sql . "
		FROM " . $table . "
		WHERE " . $where_sql . "
		" . $limit_sql);
	
	$columns_type = db_get_columns_info($table);
	foreach ($conditions as $column => $condition)
	{
		
		debug(debug_backtrace(), true);
		debug(func_get_args(), true);
		
		$stmt->bindValue(":where_" .$column, reset($condition), $columns_type[$column]);
	}
	
	$stmt->execute();
	
	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
	
	if (empty($rows))
		return array();
	else
		return $rows;
}

function db_get_columns_info($table)
{
	global $config;
	
	$return = array();
	
	$stmt = $config['db']->prepare("SELECT * FROM $table");
	$stmt->execute();
	
	for($i = 0; $i < $stmt->columnCount(); $i++)
	{
		$info = $stmt->getColumnMeta($i);
		$return[$info['name']] = $info['pdo_type'];
	}
	
	return $return;
}

function db_update($table, $values, $conditions)
{
	global $config;
	
	$columns_info = db_get_columns_info($table);
	
	if (is_null($conditions))
		$conditions = array();
	$where_sql = db_make_where($conditions);
	
	$sql = "UPDATE $table
		SET " . implode(",", array_map(function($v) { return "$v = :$v";}, array_keys($values))) . "
		WHERE $where_sql";
	
	$config['db']->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
	
	$stmt = $config['db']->prepare($sql);
	
	foreach ($values as $field => $value)
	{
		$stmt->bindValue(":" . $field, $value, $columns_info[$field]);
	}
	
	$columns_type = db_get_columns_info($table);
	foreach ($conditions as $column => $condition)
	{
		$stmt->bindValue(":where_" .$column, reset($condition), $columns_type[$column]);
	}
	
	$stmt->execute();
}

function db_insert($table, $values)
{
	global $config;
	
	$columns_info = db_get_columns_info($table);
	
	$sql = "INSERT INTO $table(" . implode(",", array_keys($values)) . ")
		VALUES(" . implode(",", array_map(function($v) { return ":$v";}, array_keys($values))) . ")";
	
	$stmt = $config['db']->prepare($sql);
	
	foreach ($values as $field => $value)
	{
		$stmt->bindValue(":" . $field, $value, $columns_info[$field]);
	}
	
	$stmt->execute();
	
	return $config['db']->lastInsertId("id");
}

function db_get_value($table, $field, $conditions)
{
	global $config;
	
	if (is_null($conditions))
		$conditions = array();
	$where_sql = db_make_where($conditions);
	
	$stmt = $config['db']->
		prepare("
			SELECT " . $field . "
			FROM " . $table . "
			WHERE " . $where_sql);
	
	$columns_type = db_get_columns_info($table);
	foreach ($conditions as $column => $condition)
	{
		$stmt->bindValue(":where_" . $column, reset($condition), $columns_type[$column]);
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
			'mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8',
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

function save_recipe()
{
	global $config;
	
	$return = true;
	
	$id_user = db_get_value('users', 'id',
		array('user' => array("=" => $config['user'])));
	
	$json_recipe = get_parameter("data");
	
	$recipe = json_decode($json_recipe, true);
	
	$id_recipe = (int)db_insert('recipes',
		array
		(
			'title' => $recipe['title'],
			'description' => $recipe['description'],
			'duration' => $recipe['duration'],
			'servings' => $recipe['servings'],
			'id_user' => $id_user
		)
	);
	
	
	if ($id_recipe > 0)
	{
		// Save the rest of parts of recipe.
		
		// --- Tags ----------------------------------------------------
		foreach ($recipe['tags'] as $tag)
		{
			$id_tag = (int)db_get_value('tags', 'id',
				array('tag' => array("=" => $tag)));
			
			if ($id_tag == 0)
			{
				// We need to save the new tag
				$id_tag = (int)db_insert('tags',
					array
					(
						'tags' => $tag,
						'id_user' => $id_user
					)
				);
			}
			
			db_insert('rel_tags_recipes',
				array
				(
					'id_tag' => $id_tag,
					'id_recipe' => $id_recipe
				)
			);
		}
		
		// --- Ingredients ---------------------------------------------
		foreach ($recipe['ingredients'] as $ingredient)
		{
			if (!is_int($ingredient['id']))
			{
				// It is a new ingredient
				$ingredient['id'] = (int)db_insert('ingredients',
					array
					(
						'ingredient' => $ingredient['id'],
						'id_user' => $id_user
					)
				);
			}
			
			if (!is_int($ingredient['measure_type']))
			{
				// It is a new ingredient
				$ingredient['measure_type'] = (int)db_insert('measure_types',
					array
					(
						'measure_type' => $ingredient['measure_type'],
						'id_user' => $id_user
					)
				);
			}
			
			db_insert('rel_ingredients_recipes',
				array
				(
					'id_ingredient' => $ingredient['id'],
					'id_recipe' => $id_recipe,
					'amount' => $ingredient['amount'],
					'id_measure_type' => $ingredient['measure_type'],
					'notes' => $ingredient['note']
				)
			);
		}
		
		// --- Steps ---------------------------------------------------
		$position = 1;
		foreach ($recipe['steps'] as $step)
		{
			db_insert('steps',
				array
				(
					'id_recipe' => $id_recipe,
					'position' => $position,
					'step' => $step['step'],
					'duration' => $step['duration']
				)
			);
			
			$position++;
		}
	}
	else
	{
		$return = false;
	}
	
	return $return;
}

function time_array_to_string($time)
{
	$return = "";
	
	if ($time['hours'] > 0) {
		$return .= $time['hours'] . " h";
	}
	
	if ($time['minutes'] != 0 || $time['seconds'] != 0) {
		if ($time['minutes'] > 0 || !empty($return)) {
			if (!empty($return))
				$return .= " : ";
			$return .= $time['minutes'] . " m";
		}
	}
	
	if ($time['seconds'] != 0) {
		if ($time['seconds'] > 0 || !empty($return)) {
			if (!empty($return))
				$return .= " : ";
			$return .= $time['seconds'] . " s";
		}
	}
	
	return $return;
}

function seconds_to_time_array($seconds)
{
	$return = array('hours' => 0, 'minutes' => 0, 'seconds' => 0);
	
	$return['hours'] = (int)($seconds / SECONDS_1_HOUR);
	
	$seconds -= ($return['hours'] * SECONDS_1_HOUR);
	$return['minutes'] = (int)($seconds / SECONDS_1_MINUTE);
	
	$seconds -= ($return['minutes'] * SECONDS_1_MINUTE);
	$return['seconds'] = $seconds;
	
	return $return;
}

function vote_recipe($id_recipe, $points)
{
	global $config;
	
	$exists = (bool)db_get_value('points', 'points', array
		(
			'id_recipe' => array('=' => $id_recipe)
		)
	);
	
	if ($exists)
	{
		db_get_value('points', 'points', array('id' => array('=' => 555)));
		
		db_update('points', array('points' => $points),
			array
			(
				'id_recipe' =>  array('=' => $id_recipe),
				'id_user' =>  array('=' => $config['id_user'])
			)
		);
	}
	else
	{
		db_insert('points',
			array(
				'id_recipe' => $id_recipe,
				'points' => $points,
				'id_user' => $config['id_user']));
	}
}

function get_votes_user($id_recipe)
{
	global $config;
	
	$points = db_get_value('points', 'points',
		array
		(
			'id_recipe' => array('=' => $id_recipe),
			'id_user' => array('=' => $config['id_user']),
		)
	);
	
	return (int)$points;
}

function get_recipe($id = 0)
{
	$return = array();
	
	$recipe = db_get_rows('recipes', null,
		array('id' => array("=" => $id)));
	
	if (empty($recipe))
	{
		return $return;
	}
	else
	{
		$recipe = $recipe[0];
		
		$return = $recipe;
		
		$return['user'] = db_get_value('users', 'user',
			array('id' => array('=' => $recipe['id_user'])));
		
		$tags = db_get_rows_sql("
			SELECT id_tag, tag
			FROM rel_tags_recipes
			INNER JOIN tags ON tags.id = rel_tags_recipes.id_tag
			WHERE id_recipe = " . $recipe['id'] . ";");
		$return['tags'] = $tags;
		
		$ingredients = db_get_rows_sql("
			SELECT *
			FROM rel_ingredients_recipes
			INNER JOIN ingredients ON ingredients.id = rel_ingredients_recipes.id_ingredient
			INNER JOIN measure_types ON measure_types.id = rel_ingredients_recipes.id_measure_type
			WHERE id_recipe = " . $recipe['id'] . ";");
		$return['ingredients'] = $ingredients;
		
		$steps = db_get_rows_sql("
			SELECT  *
			FROM steps
			WHERE id_recipe = " . $recipe['id'] . "
			ORDER BY position ASC;");
		$return['steps'] = $steps;
		
		$return['points'] = get_avg_point_from_recipe($id);
		$return['count_votes'] = get_count_votes($id);
	}
	
	return $return;
}

function get_count_votes($id_recipe = 0)
{
	$temp = db_get_rows_sql("
		SELECT
			COUNT(id_user) AS count
		FROM points
		WHERE id_recipe = " . $id_recipe . ";
	");
	
	if (empty($temp))
	{
		return 0;
	}
	else
	{
		return $temp[0]['count'];
	}
}

function get_avg_point_from_recipe($id_recipe = 0)
{
	$temp = db_get_rows_sql("
		SELECT
			(SUM(points) / COUNT(id_user)) AS average
		FROM points
		WHERE id_recipe = " . $id_recipe . ";
	");
	
	if (empty($temp))
	{
		return 0;
	}
	else
	{
		return $temp[0]['average'];
	}
}

function flat_array($array)
{
	$return = array();
	
	if (is_array($array))
	{
		foreach ($array as $item)
			$return[] = reset($item);
	}
	
	return $return;
}

function get_recipes_extended($conditions = null, $count = false, $pagination_values = null)
{
	$id_recipes = array();
	
	if (is_array($conditions))
	{
		if (isset($conditions['free_search']))
		{
			$id_tags = db_get_rows('tags', array('id'),
				array('tag' => array('like' => '%' . $conditions['free_search'] . '%')));
			$id_tags = flat_array($id_tags);
			$sql = "SELECT id_recipe
				FROM rel_tags_recipes
				WHERE id_tag IN (" . implode(",", $id_tags) . ")";
			$temp_id_recipes = db_get_rows_sql($sql);
			$temp_id_recipes = flat_array($temp_id_recipes);
			$id_recipes = array_merge($id_recipes, $temp_id_recipes);
			$id_recipes = array_unique($id_recipes);
			
			
			$id_ingredients = db_get_rows('ingredients', array('id'),
				array('ingredient' => array('like' => '%' . $conditions['free_search'] . '%')));
			$id_ingredients = flat_array($id_ingredients);
			$sql = "SELECT id_recipe
				FROM rel_ingredients_recipes
				WHERE id_ingredient IN (" . implode(",", $id_tags) . ")";
			$temp_id_recipes = db_get_rows_sql($sql);
			$temp_id_recipes = flat_array($temp_id_recipes);
			$id_recipes = array_merge($id_recipes, $temp_id_recipes);
			$id_recipes = array_unique($id_recipes);
			
			
			$temp_id_recipes = db_get_rows('steps', array('id_recipe'),
				array('step' => array('like' => '%' . $conditions['free_search'] . '%')));
			$temp_id_recipes = db_get_rows_sql($sql);
			$temp_id_recipes = flat_array($temp_id_recipes);
			$id_recipes = array_merge($id_recipes, $temp_id_recipes);
			$id_recipes = array_unique($id_recipes);
			
			
			$temp_id_recipes = db_get_rows('recipes', array('id'),
				array('title' => array('like' => '%' . $conditions['free_search'] . '%'),
					'description' => array('like' => '%' . $conditions['free_search'] . '%')));
			$temp_id_recipes = flat_array($temp_id_recipes);
			$id_recipes = array_merge($id_recipes, $temp_id_recipes);
			$id_recipes = array_unique($id_recipes);
		}
		
		
	}
	
	if ($count)
	{
		$sql = "SELECT COUNT(*)
			FROM recipes
			WHERE id IN (" . implode(",", $id_recipes) . ")";
		
		$count_row = db_get_rows_sql($sql);
		
		if (empty($count_row))
			return 0;
		else
			$return = reset(reset($count_row));
	}
	else
	{
		$sql = "SELECT *
			FROM recipes
			WHERE id IN (" . implode(",", $id_recipes) . ")
			LIMIT " . PAGINATION_BLOCK . " OFFSET " . $pagination_values['offset'];
		
		$return = db_get_rows_sql($sql);
	}
	
	return $return;
}

function get_recipes($conditions = null, $count = false, $pagination_values = null)
{
	if ($count)
		$recipes = db_get_count('recipes', null, $conditions);
	else
	{
		$recipes = db_get_rows('recipes', null, $conditions,
			array('limit' => PAGINATION_BLOCK, 'offset' => $pagination_values['offset']));
	}
	
	return $recipes;
}

function truncate_string($string, $size = null, $end_string = "…")
{
	if (is_null($size))
		$size = SIZE_TRUNCATE_TEXT;
	
	$return = $string;
	
	if (strlen($string) > $size)
	{
		$return = substr($string, 0, ($size - 1));
		$return .= $end_string;
	}
	
	return $string;
}

function pagination_get_values($count)
{
	$page = get_parameter('pagination_page', 1);
	
	$pages = (int)ceil($count / PAGINATION_BLOCK);
	
	$offset = ($page - 1) * PAGINATION_BLOCK;
	
	$ini_page = (int)floor(($page - 1) / NUM_PAGES) * NUM_PAGES + 1;
	$end_page = $ini_page + NUM_PAGES - 1;
	if ($end_page > $pages)
		$end_page = $pages;
	
	return array
		(
			'count' => $count,
			'page' => $page,
			'pages' => $pages,
			'offset' => $offset,
			'ini_page' => $ini_page,
			'end_page' => $end_page,
		);
}

function print_pagination($pagination_values, $url)
{
	if ($pagination_values['pages'] == 1)
		return;
	?>
	<div class="text-center">
		<ul class="pagination">
			<li class="disabled"><a href="javascript:">Total: <?=$pagination_values['count'];?></a></li>
		<?php
		if ($pagination_values['ini_page'] > 1)
		{
			$previous = $pagination_values['ini_page'] - 1;
			?>
			<li><a href="<?=$url;?>&pagination_page=1">First</a></li>
			<li><a href="<?=$url;?>&pagination_page=<?=$previous;?>">Previous</a></li>
			<?php
		}
		
		for ($i = $pagination_values['ini_page']; $i <= $pagination_values['end_page']; $i++)
		{
			if ($i == $pagination_values['page'])
			{
				?>
				<li class="active">
				<?php
			}
			else
			{
				?>
				<li>
				<?php
			}
			?>
			<a href="<?=$url;?>&pagination_page=<?=$i;?>"><?=$i;?></a></li>
			<?php
		}
		
		if ($pagination_values['pages'] > $pagination_values['end_page'])
		{
			
			$next = $pagination_values['end_page'] + 1;
			?>
			<li><a href="<?=$url;?>&pagination_page=<?=$next;?>">Next</a></li>
			<li><a href="<?=$url;?>&pagination_page=<?=$pagination_values['pages'];?>">Last</a></li>
			<?php
		}
		
		?>
		</ul>
	</div>
	<?php
}
?>