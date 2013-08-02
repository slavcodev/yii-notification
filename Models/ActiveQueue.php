<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii\Models;

use ActiveRecord;
use Notification\Model\MessageInterface;
use Notification\Model\QueueInterface;
use Yii;

/**
 * Class ActiveMessenger
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 *
 * @property mixed $id
 */
class ActiveQueue extends ActiveRecord implements QueueInterface
{
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
			->where('queue_id = ? AND published = 0', array($this->getPrimaryKey()))
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
				'published' => false,
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
			->where('queue_id = ? AND published = 0', array($this->getPrimaryKey()))
			->order('create_at ASC')
			->limit(1)
			->queryRow();

		if (!$res) {
			return null;
		}

		$this->getDbConnection()
			->createCommand()
			->update('msg_queue_message', array(
					'published' => true,
				), 'id = :id', array(
					':id' => (int) $res['id'],
				));

		return unserialize($res['message']);
	}
}
