<?php
namespace lib\db;

/** work with polls **/
class polls
{
	/**
	 * this library work with acoount
	 * v2.8
	 */


	/**
	 * get list of polls
	 * @param  [type] $_user_id set userid
	 * @param  [type] $_return  set return field value
	 * @param  string $_type    set type of poll
	 * @return [type]           an array or number
	 */
	public static function get($_user_id = null, $_return = null, $_type = 'sarshomar')
	{
		// calc type if needed
		if($_type === null)
		{
			$_type = "post_type LIKE 'poll\_%'";
		}
		else
		{
			$_type = "post_type = 'poll_". $_type. "'";
		}
		// calc user id if exist
		if($_user_id)
		{
			$_user_id = "AND user_id = $_user_id";
		}
		else
		{
			$_user_id = null;
		}
		// generate query string
		$qry = "SELECT * FROM posts WHERE $_type $_user_id";
		// run query
		if($_return && $_return !== 'count')
		{
			$result = \lib\db::get($qry, $_return);
		}
		else
		{
			$result = \lib\db::get($qry);
		}
		// if user want count of result return count of it
		if($_return === 'count')
		{
			return count($result);
		}
		// return last insert id
		return $result;
	}


	/**
	 * return last question for this user
	 * @param  [type] $_user_id [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function getLast($_user_id, $_type = 'sarshomar')
	{
		// calc type if needed
		if($_type === null)
		{
			$_type = "post_type LIKE 'poll\_%'";
		}
		else
		{
			$_type = "post_type = 'poll_". $_type. "'";
		}

		$qry ="SELECT
				posts.id as id,
				posts.post_title as question,
				options.option_meta as answers
			FROM posts
			LEFT JOIN `options` ON `options`.post_id = posts.id
			WHERE
				$_type AND
				post_status = 'publish' AND
				posts.id NOT IN
				(
					SELECT post_id FROM options
						WHERE
						`options`.option_cat LIKE 'polls\_%' AND
						`options`.option_key LIKE 'answer\_%'AND
						`options`.user_id = $_user_id
				)
			ORDER BY posts.id ASC
			LIMIT 1
		";

		$result      = \lib\db::get($qry, null, true);
		$returnValue =
		[
			'id'       => null,
			'question' => null,
			'answers'  => null,
			'tags'     => null,
		];
		if(isset($result['question']))
		{
			$returnValue['id']       = $result['id'];
			$returnValue['question'] = $result['question'];
			$tagList                 = \lib\db\tags::usage($returnValue['id']);
			foreach ($tagList as $key => $value)
			{
				$newValue                = "#". str_replace(' ', '\_', $value);
				$returnValue['tags']     .= $newValue.' ';
				$returnValue['question'] = str_replace($value.' ', $newValue.' ', $returnValue['question']);
			}
		}
		if(isset($result['answers']))
		{
			$returnValue['answers'] = json_decode($result['answers'], true);
			$returnValue['answers'] = array_column($returnValue['answers'], 'txt', 'id');
		}

		return $returnValue;
	}


	/**
	 * get result of specefic item
	 * @param  [type] $_poll_id [description]
	 * @param  [type] $_value   [description]
	 * @param  [type] $_key     [description]
	 * @return [type]           [description]
	 */
	public static function getResult($_poll_id, $_value = null, $_key = null)
	{
		// get answers grouped by items
		$qry = "SELECT
				option_value as `item`,
				count(id) as `count`
			FROM options
			WHERE
				option_cat LIKE 'polls\_%' AND
				option_key = 'answer_$_poll_id' AND
				option_status = 'enable'

			GROUP BY item
		";
		// get result of this question
		$result = \lib\db::get($qry, ['item', 'count']);
		// var_dump($result);

		// get list of answers of this question
		$qry = "SELECT option_meta as `meta`
			FROM options
			WHERE
				option_cat = 'meta_polls' AND
				option_key = 'answers_$_poll_id' AND
				option_status = 'enable'

			LIMIT 1
		";
		$answers      = \lib\db::get($qry, 'meta', true);
		$answers      = json_decode($answers, true);
		$final_result = $answers;
		// fill result into answers list
		foreach ($final_result as $key => $value)
		{
			// if has count set this number
			if(isset($result[$key]))
			{
				$final_result[$key]['count'] = (int) $result[$key];
			}
			// else set zero
			else
			{
				$final_result[$key]['count'] = 0;
			}
		}
		// filter output value
		if(is_string($_value))
		{
			if(is_string($_key))
			{
				$final_result = array_column($final_result, $_value, $_key);
			}
			else
			{
				$final_result = array_column($final_result, $_value);
			}
		}
		return $final_result;
	}


	/**
	 * get list of questions that this user answered
	 * @param  [type] $_user_id [description]
	 * @param  string $_type    [description]
	 * @return [type]           [description]
	 */
	public static function getAnweredList($_user_id, $_type = 'sarshomar', $_return = null)
	{
		// calc type if needed
		if($_type === null)
		{
			$_type = "post_type LIKE 'poll\_%'";
		}
		else
		{
			$_type = "post_type = 'poll_". $_type. "'";
		}

		$qry ="SELECT * FROM posts
			LEFT JOIN `options` ON `options`.post_id = posts.id

			WHERE
				$_type AND
				user_id = $_user_id AND
				option_cat = 'polls_$_user_id' AND
				option_key LIKE 'answer\_%' AND
				option_status = 'enable'
		";

		$result = \lib\db::get($qry, $_return);
		return $result;
	}


	/**
	 * save poll into database
	 * @return [type] [description]
	 */
	public static function save($_input, $_user_id)
	{
		// return false if count of input value less than 3
		// 1 question
		// 2 answer or more
		if(count($_input) < 3 || count($_input) > 10 || !isset($_input['question']))
		{
			return false;
		}
		// extract question
		$question = $_input['question'];
		unset($_input['question']);
		// save question into post table
		$saveResult = self::saveQuestion($question, $_input, $_user_id);
		// return final result
		return $saveResult;
	}


	/**
	 * save question into post table
	 * @param  [type] $_question    [description]
	 * @param  [type] $_answersList [description]
	 * @return [type]               [description]
	 */
	public static function saveQuestion($_question, $_answersList, $_user_id)
	{
		$slug         = \lib\utility\filter::slug($_question);
		$url          = 'civility/'.$_user_id.'/'.$slug;
		$myAnswersList = json_encode($_answersList, JSON_UNESCAPED_UNICODE);
		$pubDate      = date('Y-m-d H:i:s');
		// create query string
		$qry = "INSERT INTO posts
		(
			`post_language`,
			`post_title`,
			`post_slug`,
			`post_url`,
			`post_meta`,
			`post_type`,
			`post_status`,
			`post_publishdate`,
			`user_id`
		)
		VALUES
		(
			'fa',
			'$_question',
			'$slug',
			'$url',
			'$myAnswersList',
			'poll',
			'draft',
			'$pubDate',
			$_user_id
		)";
		// run query
		$result        = \lib\db::query($qry);
		// return last insert id
		$questionId    = \lib\db::insert_id();
		// save answers into options table
		$saveAnsStatus = self::saveAnswersList($_answersList, $questionId);
		return $saveAnsStatus;
	}


	/**
	 * save answers into options table
	 * @param  [type] $_answersList raw answer list
	 * @return [type]               [description]
	 */
	public static function saveAnswersList($_answersList, $_post_id)
	{
		$answers = [];
		$max_ans = 10;
		// foreach answers exist fill the array
		foreach ($_answersList as $key => $value)
		{
			$answers[$key]['txt'] = $value;
		}
		// decode for saving into db
		$answers     = json_encode($answers, JSON_UNESCAPED_UNICODE);
		$option_data =
		[
			'post'   => $_post_id,
			'cat'    => 'meta_polls',
			'key'    => 'answers_'.$_post_id,
			'value'  => "",
			'meta'   => $answers,
			'status' => 'enable',
		];
		// save in options table and if successful return session_id
		return \lib\utility\option::set($option_data, true);
	}


	/**
	 * save user answer into options table
	 * @param  [type] $_user_id [description]
	 * @param  [type] $_post_id [description]
	 * @param  [type] $_answer  [description]
	 * @return [type]           [description]
	 */
	public static function saveAnswer($_user_id, $_post_id, $_answer, $_answer_txt = null)
	{
		// set status of skip answers to disable
		$status = 'enable';
		if($_answer < 0)
		{
			$status = 'disable';
		}
		$meta =
		[
			'question'   => $_post_id,
			'answer'     => $_answer,
			'answer_txt' => $_answer_txt,
			'date'       => date('Y-m-d H:i:s'),
		];
		$option_data =
		[
			'user'   => $_user_id,
			'post'   => $_post_id,
			'cat'    => 'polls_'. $_user_id,
			'key'    => 'answer_'.$_post_id,
			'value'  => $_answer,
			'meta'   => $meta,
			'status' => $status,
			'modify' => 'now',
		];
		// save in options table and if successful return session_id
		return \lib\utility\option::set($option_data, true);
	}


	/**
	 * delete answers of specefic user
	 * @param  [type] $_user_id [description]
	 * @return [type]           [description]
	 */
	public static function removeUserAnswers($_user_id)
	{
		$qry = "DELETE FROM options
			WHERE
				user_id = $_user_id AND
				option_cat = 'polls_$_user_id' AND
				option_key LIKE 'answer\_%'
			";
		$result = \lib\db::query($qry);
		return $result;
	}
}
?>