<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii\Tests;

use CModel;

/**
 * Class PostModel
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class PostModel extends CModel
{
	public $id = '1';
	public $title = 'Some post title';
	public $body = 'Some post body';

	public function attributeNames()
	{
		return array('id', 'title', 'body');
	}
}
