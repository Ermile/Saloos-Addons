<?php
namespace lib\db;

/** termusage managing **/
class termuseage
{
	/**
	 * this library work with termuseage
	 * v1.0
	 */

	/**
	 * insert mutli tags (get id of tags) to teruseage
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert_multi($_args)
	{
		// array : id of tags
		$tags_id = $_args['tags'];
		$termusage_id = $_args['termusage_id'];
		$termusage_foreign = $_args['termusage_foreign'];

		if (empty($tags_id)){
			return ;
		}

		$values = [];
		foreach ($tags_id as $key => $value) {
			$values[] = "('$value', '$termusage_id', '$termusage_foreign')";
		}

		$values = join($values, ",");

		$query = "
				INSERT INTO
					termusages
					(term_id, termusage_id, termusage_foreign)
				VALUES
					$values
				";
		return \lib\db::query($query);
	}
}
?>