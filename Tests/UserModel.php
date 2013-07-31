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
 * Class UserModel
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class UserModel extends CModel
{
	public $id = '1';
	public $name = 'Veaceslav Medvedev';
	public $mail = 'slavcopost@gmail.com';

	public function attributeNames()
	{
		return array('id', 'name', 'mail');
	}
}
