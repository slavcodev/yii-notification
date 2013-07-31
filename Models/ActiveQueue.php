<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii\Models;

use CActiveRecord;
use Notification\Model\MessageInterface;
use Notification\Model\QueueInterface;
use Yii;

/**
 * Class ActiveMessenger
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class ActiveQueue extends CActiveRecord implements QueueInterface
{
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'msg_queue';
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->getPrimaryKey();
	}

	/**
	 * @param $position
	 * @return $this
	 */
	public function seek($position)
	{
		$this->getDbConnection()
			->createCommand()
			->delete('msg_queue_message', '1 = 1 ORDER BY create_at ASC LIMIT ' . (int) $position);

		return $this;
	}

	public function count()
	{
		return $this->getDbConnection()
			->createCommand()
			->select('COUNT(id)')
			->from('msg_queue_message')
			->where('queue_id = ?', array($this->getPrimaryKey()))
			->group('queue_id')
			->queryScalar();
	}

	/**
	 * @param MessageInterface $message
	 * @return $this
	 */
	public function enqueue(MessageInterface $message)
	{
		$this->getDbConnection()
			->createCommand()
			->insert('msg_queue_message', array(
				'queue_id' => $this->getPrimaryKey(),
				'create_at' => new \CDbExpression('NOW()'),
				'message' => serialize($message),
			));

		return $this;
	}

	/**
	 * @return MessageInterface
	 */
	public function dequeue()
	{
		$res = $this->getDbConnection()
			->createCommand()
			->from('msg_queue_message')
			->where('queue_id = ?', array($this->getPrimaryKey()))
			->order('create_at ASC')
			->limit(1)
			->queryRow();

		if (!$res) {
			return null;
		}

		$this->getDbConnection()
			->createCommand()
			->delete('msg_queue_message', 'id = :id', array(
				':id' => $res['id'],
			));

		return unserialize($res['message']);
	}
}
