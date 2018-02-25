-- Copyright (C) 2016 Miguel de Dios
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- higher any later version.
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the Free Software Foundation,
-- Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301  USA

-- ---------------------------------------------------------------------
-- TABLES
-- ---------------------------------------------------------------------
-- recipes(_id_, title, description, duration, servings, id_user)
-- tags(_id_, tag, id_user)
-- rel_tags_recipes(id_tag, id_recipe)
-- ingredients(id, ingredient, id_user)
-- rel_ingredients_recipes(id_ingredient, id_recipe, amount, measure_type)
-- steps(_id_, position, step, duration, id_user)
-- rel_steps_recipes(id_step, id_recipe)
-- user(_id_, user, email, password, validate)


DROP DATABASE IF EXISTS recipes_book;
CREATE DATABASE recipes_book;

USE recipes_book;

CREATE TABLE users
(
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	user VARCHAR(500) DEFAULT "",
	email VARCHAR(500) DEFAULT "",
	password VARCHAR(500) DEFAULT "",
	validate TINYINT DEFAULT 0
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE recipes
(
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	title VARCHAR(500) DEFAULT "",
	description VARCHAR(1000) DEFAULT "",
	duration INT DEFAULT 0 COMMENT "In seconds",
	servings INT DEFAULT 0,
	id_user INT
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE tags
(
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	tag VARCHAR(500) DEFAULT "",
	id_user INT
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE rel_tags_recipes
(
	id_tag INT,
	id_recipe INT
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE ingredients
(
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	ingredient VARCHAR(500) DEFAULT "",
	id_user INT
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE rel_ingredients_recipes
(
	id_ingredient INT,
	id_recipe INT,
	amount FLOAT DEFAULT 0,
	id_measure_type INT,
	notes VARCHAR(1000) DEFAULT ""
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE measure_types
(
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	measure_type VARCHAR(500) DEFAULT "",
	id_user INT
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE steps
(
	id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	position INT DEFAULT 0,
	step VARCHAR(1000) DEFAULT "" COMMENT "In seconds",
	duration INT DEFAULT 0,
	id_recipe INT
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE points
(
	id_recipe INT,
	id_user INT,
	points INT DEFAULT 0
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- ---------------------------------------------------------------------
-- Default and example Data
-- ---------------------------------------------------------------------
INSERT INTO users (user, email, password, validate)
VALUES('admin', '', MD5('admin'), 1);


INSERT INTO recipes (title, description, duration, servings, id_user)
SELECT 'Gazpacho',
	'Wonderful cold soup full of fresh Mediterranean vegetables! Quick and easy.',
	300, 3, id
FROM users
WHERE user = 'admin';


INSERT INTO measure_types(measure_type, id_user)
SELECT 'Teaspoon', id
FROM users
WHERE user = 'admin';
INSERT INTO measure_types(measure_type, id_user)
SELECT 'Tablespoon', id
FROM users
WHERE user = 'admin';
INSERT INTO measure_types(measure_type, id_user)
SELECT 'gram', id
FROM users
WHERE user = 'admin';
INSERT INTO measure_types(measure_type, id_user)
SELECT 'Kilogram', id
FROM users
WHERE user = 'admin';
INSERT INTO measure_types(measure_type, id_user)
SELECT 'Pinch', id
FROM users
WHERE user = 'admin';
INSERT INTO measure_types(measure_type, id_user)
SELECT 'Liter', id
FROM users
WHERE user = 'admin';
INSERT INTO measure_types(measure_type, id_user)
SELECT 'Unit', id
FROM users
WHERE user = 'admin';
INSERT INTO measure_types(measure_type, id_user)
SELECT 'Clove', id
FROM users
WHERE user = 'admin';


INSERT INTO tags(tag, id_user)
SELECT 'Spanish', id
FROM users
WHERE user = 'admin';
INSERT INTO tags(tag, id_user)
SELECT 'Non mexican', id
FROM users
WHERE user = 'admin';
INSERT INTO tags(tag, id_user)
SELECT 'Energy drink', id
FROM users
WHERE user = 'admin';
INSERT INTO tags(tag, id_user)
SELECT 'Feel the fiesta', id
FROM users
WHERE user = 'admin';



INSERT INTO rel_tags_recipes(id_tag, id_recipe)
SELECT
(
	SELECT id
	FROM tags
	WHERE tag = 'Spanish'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
);
INSERT INTO rel_tags_recipes(id_tag, id_recipe)
SELECT
(
	SELECT id
	FROM tags
	WHERE tag = 'Non mexican'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
);
INSERT INTO rel_tags_recipes(id_tag, id_recipe)
SELECT
(
	SELECT id
	FROM tags
	WHERE tag = 'Energy drink'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
);
INSERT INTO rel_tags_recipes(id_tag, id_recipe)
SELECT
(
	SELECT id
	FROM tags
	WHERE tag = 'Feel the fiesta'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
);



INSERT INTO ingredients(ingredient, id_user)
SELECT 'Tomato', id
FROM users
WHERE user = 'admin';
INSERT INTO ingredients(ingredient, id_user)
SELECT 'Green pepper', id
FROM users
WHERE user = 'admin';
INSERT INTO ingredients(ingredient, id_user)
SELECT 'Red pepper', id
FROM users
WHERE user = 'admin';
INSERT INTO ingredients(ingredient, id_user)
SELECT 'Cucumber', id
FROM users
WHERE user = 'admin';
INSERT INTO ingredients(ingredient, id_user)
SELECT 'Onion', id
FROM users
WHERE user = 'admin';
INSERT INTO ingredients(ingredient, id_user)
SELECT 'Bread', id
FROM users
WHERE user = 'admin';
INSERT INTO ingredients(ingredient, id_user)
SELECT 'Garlic', id
FROM users
WHERE user = 'admin';
INSERT INTO ingredients(ingredient, id_user)
SELECT 'Olive oil', id
FROM users
WHERE user = 'admin';
INSERT INTO ingredients(ingredient, id_user)
SELECT 'Vinegar', id
FROM users
WHERE user = 'admin';
INSERT INTO ingredients(ingredient, id_user)
SELECT 'Salt', id
FROM users
WHERE user = 'admin';
INSERT INTO ingredients(ingredient, id_user)
SELECT 'Water', id
FROM users
WHERE user = 'admin';



INSERT INTO rel_ingredients_recipes
SELECT
(
	SELECT id
	FROM ingredients
	WHERE ingredient = 'Tomato'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
),
1,
(
	SELECT id
	FROM measure_types
	WHERE measure_type = 'Kilogram'
),
"Best, the tomatoes are near to mature.";
INSERT INTO rel_ingredients_recipes
SELECT
(
	SELECT id
	FROM ingredients
	WHERE ingredient = 'Green pepper'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
),
1,
(
	SELECT id
	FROM measure_types
	WHERE measure_type = 'Unit'
),
"The green pepper has the weight near to 60 grams.";
INSERT INTO rel_ingredients_recipes
SELECT
(
	SELECT id
	FROM ingredients
	WHERE ingredient = 'Red pepper'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
),
1,
(
	SELECT id
	FROM measure_types
	WHERE measure_type = 'Unit'
),
"The red pepper has the weight near to 60 grams.";
INSERT INTO rel_ingredients_recipes
SELECT
(
	SELECT id
	FROM ingredients
	WHERE ingredient = 'Cucumber'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
),
1,
(
	SELECT id
	FROM measure_types
	WHERE measure_type = 'Unit'
),
"A medium size cucumber";
INSERT INTO rel_ingredients_recipes
SELECT
(
	SELECT id
	FROM ingredients
	WHERE ingredient = 'Onion'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
),
0.5,
(
	SELECT id
	FROM measure_types
	WHERE measure_type = 'Unit'
),
"";
INSERT INTO rel_ingredients_recipes
SELECT
(
	SELECT id
	FROM ingredients
	WHERE ingredient = 'Garlic'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
),
1,
(
	SELECT id
	FROM measure_types
	WHERE measure_type = 'Clove'
),
"You can add two or half cloves, depends of your taste.";
INSERT INTO rel_ingredients_recipes
SELECT
(
	SELECT id
	FROM ingredients
	WHERE ingredient = 'Olive oil'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
),
4,
(
	SELECT id
	FROM measure_types
	WHERE measure_type = 'Tablespoon'
),
"";
INSERT INTO rel_ingredients_recipes
SELECT
(
	SELECT id
	FROM ingredients
	WHERE ingredient = 'Vinegar'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
),
2,
(
	SELECT id
	FROM measure_types
	WHERE measure_type = 'Tablespoon'
),
"";
INSERT INTO rel_ingredients_recipes
SELECT
(
	SELECT id
	FROM ingredients
	WHERE ingredient = 'Salt'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
),
2,
(
	SELECT id
	FROM measure_types
	WHERE measure_type = 'Teaspoon'
),
"";
INSERT INTO rel_ingredients_recipes
SELECT
(
	SELECT id
	FROM ingredients
	WHERE ingredient = 'Water'
),
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
),
1,
(
	SELECT id
	FROM measure_types
	WHERE measure_type = 'liter'
),
"You can add less water as half liter or none. Depends of your taste.";



INSERT INTO steps(position, step, duration, id_recipe)
SELECT
	1,
	"Mix all (don't add the olive oil, it is for the last step.) the ingredients into a blender or termomix.",
	240,
	id
FROM recipes
WHERE title = 'Gazpacho';
INSERT INTO steps(position, step, duration, id_recipe)
SELECT
	2,
	"Add the olive oil and mix with slow speed into the blender, because if you mix a fast speed the colour of gazpacho will be pink instead the fresh red color.",
	60,
	id
FROM recipes
WHERE title = 'Gazpacho';



INSERT INTO points
SELECT
(
	SELECT id
	FROM recipes
	WHERE title = 'Gazpacho'
),
(
	SELECT id
	FROM users
	WHERE user = 'admin'
),
5;