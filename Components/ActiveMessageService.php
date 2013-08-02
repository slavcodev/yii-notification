<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii\Components;

// Infra
use Yii;
use CDbException;

// Domain
use Notification\Model\MessengerInterface;
use Notification\Model\QueueInterface;
use Notification\Model\MessageInterface;
use Notification\Service\MessageServiceInterface;

/**
 * Class ActiveMessageService
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class ActiveMessageService implements MessageServiceInterface
{
	/**
	 * ActiveMessage no need to publish, they are stored in the database.
	 *
	 * @param MessageInterface $message
	 * @return MessageServiceInterface
	 */
	public function publish(MessageInterface $message)
	{
		return $this;
	}

	/**
	 * @param QueueInterface $queue
	 * @return MessageServiceInterface
	 * @throws CDbException
	 */
	public function dispatch(QueueInterface $queue)
	{
		if ($queue->count() > 0) {
			$transaction = Yii::app()->getDb()->beginTransaction();

			try {
				while ($message = $queue->dequeue()) {
					$this->publish($message);
				}

				$transaction->commit();
			} catch (CDbException $e) {
				$transaction->rollback();
				throw $e;
			}
		}

		return $this;
	}

	/**
	 * @param MessageInterface $message
	 * @param MessengerInterface $messenger
	 * @return MessageServiceInterface
	 */
	public function send(MessageInterface $message, MessengerInterface $messenger) {
		foreach ($messenger->getIterator() as $queue) {
			$queue->enqueue($message);
		}

		return $this;
	}
}
