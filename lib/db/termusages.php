<?php
namespace lib\db;

/** termusage managing **/
class termusages
{
	/**
	 * this library work with termusages
	 * v1.0
	 */

	public static function check($_args)
	{
		if(!isset($_args['term_id']))
		{
			return false;
		}

		if(!isset($_args['termusage_foreign']))
		{
			return false;
		}

		if(!isset($_args['termusage_id']))
		{
			return false;
		}

		$query =
		"
			SELECT
				*
			FROM
				termusages
			WHERE
				term_id           = $_args[term_id] AND
				termusage_id      = $_args[termusage_id] AND
				termusage_foreign = '$_args[termusage_foreign]'
			LIMIT 1
		";
		return \lib\db::get($query, null, true);
	}


	public static function update($_old, $_new)
	{
		if(!isset($_old['term_id']) || !isset($_new['term_id']))
		{
			return false;
		}

		if(!isset($_old['termusage_foreign']) || !isset($_new['termusage_foreign']))
		{
			return false;
		}

		if(!isset($_old['termusage_id']) || !isset($_new['termusage_id']))
		{
			return false;
		}

		$query =
		"
			UPDATE
				termusages
			SET
				term_id           = $_new[term_id],
				termusage_id      = $_new[termusage_id],
				termusage_foreign = '$_new[termusage_foreign]'
			WHERE
				term_id           = $_old[term_id] AND
				termusage_id      = $_old[termusage_id] AND
				termusage_foreign = '$_old[termusage_foreign]'
			LIMIT 1
		";
		return \lib\db::query($query);
	}
	/**
	 * insert mutli tags (get id of tags) to teruseage
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert_multi($_args)
	{
		if (empty($_args)){
			return ;
		}

		// marge all input array to creat list of field to be insert
		$fields = [];
		foreach ($_args as $key => $value) {
			$fields = array_merge($fields, $value);
		}

		// creat multi insert query : INSERT INTO TABLE (FIELDS) VLUES (values), (values), ...
		$values = [];
		$together = [];
		foreach ($_args	 as $key => $value) {
			foreach ($fields as $field_name => $vain) {
				if(array_key_exists($field_name, $value)){
					$values[] = "'" . $value[$field_name] . "'";
				}else{
					$values[] = "NULL";
				}
			}
			$together[] = join($values, ",");
			$values = [];
		}

		if(empty($fields))
		{
			return null;
		}

		$fields = join(array_keys($fields), ",");

		$values = join($together, "),(");

		// crate string query
		$query = "
				INSERT INTO	termusages
				($fields)
				VALUES
				($values)
				";
		return \lib\db::query($query);
	}


	public static function usage($_usageid, $_foreign = 'posts', $_type = null)
	{
		if($_foreign === null)
		{
			$_foreign = 'posts';
		}
		$type = null;
		if($_type)
		{
			$type = " AND terms.term_type = '$_type' ";
		}
		$query =
		"
			SELECT
				*
			FROM
				terms
			INNER JOIN termusages ON termusages.term_id = terms.id
			WHERE
				termusages.termusage_foreign = '$_foreign' AND
				termusages.termusage_id      = $_usageid
				$type
		";
		return \lib\db::get($query);
	}
}
?>