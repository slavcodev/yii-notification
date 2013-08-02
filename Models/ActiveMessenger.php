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
use Notification\Model\MessengerInterface;
use Notification\Model\QueueInterface;
use Notification\Model\SpecificationInterface;

/**
 * Class ActiveMessenger
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 *
 * @property mixed $id
 * @property QueueInterface[] $queues
 */
class ActiveMessenger extends CActiveRecord implements MessengerInterface
{
	public static function model($className = __CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'msg_messenger';
	}

	public function relations()
	{
		return array(
			'queues' => array(
				self::MANY_MANY,
				'NotificationYii\Models\ActiveQueue',
				'msg_messenger_queue_rel(messenger_id, queue_id)',
				'index' => 'id'
			),
		);
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->getPrimaryKey();
	}

	/**
	 * @param MessageInterface $message
	 * @return void
	 */
	public function send(MessageInterface $message)
	{
		foreach ($this->queues as $queue) {
			$queue->enqueue($message);
		}
	}

	/**
	 * @param QueueInterface $queue
	 * @param SpecificationInterface $specification
	 * @throws \CException
	 */
	public function bind(
		QueueInterface $queue,
		SpecificationInterface $specification = null
	) {
		if (!$queue instanceof ActiveQueue || $queue->getIsNewRecord()) {
			throw new \CException();
		}

		$cmd = $this->getDbConnection()->createCommand();

		if ($cmd->insert('msg_messenger_queue_rel', array(
				'messenger_id' => $this->getPrimaryKey(),
				'queue_id' => $queue->getPrimaryKey(),
			))) {
			$this->addRelatedRecord('queues', $queue, $queue->getPrimaryKey());
		}
	}

	/**
	 * @param QueueInterface $queue
	 * @param SpecificationInterface $specification
	 * @throws \CException
	 */
	public function unbind(
		QueueInterface $queue,
		SpecificationInterface $specification = null
	) {
		if (!$queue instanceof ActiveQueue || $queue->getIsNewRecord()) {
			throw new \CException();
		}

		$cmd = $this->getDbConnection()->createCommand();

		if ($cmd->delete('msg_messenger_queue_rel', array(
				'messenger_id' => $this->getPrimaryKey(),
				'queue_id' => $queue->getPrimaryKey(),
			))) {
			$queues = $this->queues;
			if (isset($queues[$queue->getPrimaryKey()])) {
				unset($queues[$queue->getPrimaryKey()]);
				$this->queues = $queues;
			}
		}
	}
}
