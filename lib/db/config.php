<?php
namespace lib\db;


class config
{
	/**
	 * Makes a where.
	 *
	 * @param      <type>  $_where  The where
	 *
	 * @return     array   ( description_of_the_return_value )
	 */
	public static function make_where($_where, $_options = [])
	{
		$default_options =
		[
			'condition'  => 'AND',
			'table_name' => null,
		];

		if(!is_array($_options))
		{
			$_options = [];
		}

		$_options = array_merge($default_options, $_options);

		$table_name = null;
		if($_options['table_name'])
		{
			$table_name = "`$_options[table_name]`.";
		}

		$where = [];
		foreach ($_where as $field => $value)
		{
			if(is_string($value) && preg_match("/\%/", $value))
			{
				$where[] = " $table_name`$field` LIKE '$value' ";
			}
			elseif($value === null || is_null($value))
			{
				$where[] = " $table_name`$field` IS NULL ";
			}
			elseif(is_string($value))
			{
				$where[] = " $table_name`$field` = '$value' ";
			}
			elseif(is_numeric($value))
			{
				$where[] = " $table_name`$field` = $value ";
			}
		}

		if(!empty($where))
		{
			$where = implode($_options['condition'], $where);
		}
		else
		{
			$where = false;
		}

		return $where;
	}


	/**
	 * Makes a set.
	 *
	 * @param      <type>  $_args  The arguments
	 */
	public static function make_set($_args, $_options = [])
	{
		$default_options =
		[
			'type' => 'update',
		];

		if(!is_array($_options))
		{
			$_options = [];
		}
		$_options = array_merge($default_options, $_options);

		$set = [];
		foreach ($_args as $key => $value)
		{
			if($value === null)
			{
				$set[] = " `$key` = NULL ";
			}
			elseif(is_numeric($value))
			{
				$set[] = " `$key` = $value ";
			}
			elseif(is_string($value) && substr($value, 0,7) === '(SELECT')
			{
				$set[] = " `$key` = $value ";
			}
			elseif(is_string($value) && (!$value || $value === '' ))
			{
				$set[] = " `$key` = NULL";
			}
			else
			{
				$set[] = " `$key` = '$value' ";
			}
		}

		if(!empty($set))
		{
			if($_options['type'] === 'update')
			{
				$set = implode(',', $set);
			}
			elseif($_options['type'] === 'insert')
			{

			}
			else
			{
				$set = false;
			}
		}
		else
		{
			$set = false;
		}
		return $set;
	}


	public static function make_multi_insert($_args)
	{
		if(!is_array($_args))
		{
			return false;
		}
		// marge all input array to creat list of field to be insert
		$fields = [];
		foreach ($_args as $key => $value)
		{
			$fields = array_merge($fields, $value);
		}

		// empty record not inserted
		if(empty($fields))
		{
			return false;
		}

		// creat multi insert query : INSERT INTO TABLE (FIELDS) VLUES (values), (values), ...
		$values = [];
		$together = [];
		foreach ($_args	 as $key => $value)
		{
			foreach ($fields as $field_name => $vain)
			{
				if(array_key_exists($field_name, $value))
				{
					if(is_numeric($value[$field_name]))
					{
						$values[] = $value[$field_name];
					}
					elseif($value[$field_name] === null || (is_string($value[$field_name]) && (!$value[$field_name] || $value[$field_name] === '' )))
					{
						$values[] = "NULL";
					}
					else
					{
						$values[] = "'" . $value[$field_name] . "'";
					}
				}
				else
				{
					$values[] = "NULL";
				}
			}
			$together[] = join($values, ",");
			$values     = [];
		}

		$fields = join(array_keys($fields), ",");

		$values = join($together, "),(");

		$temp_query = "($fields) VALUES ($values) ";
		return $temp_query;
	}
}
?>