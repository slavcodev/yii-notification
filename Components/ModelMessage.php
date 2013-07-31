<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii\Components;

use CModel;
use Notification\Model\Message;

/**
 * Class ModelMessage
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class ModelMessage extends Message
{
	public function __construct($id, CModel $model)
	{
		parent::__construct($id, $model->getAttributes());
	}
}
