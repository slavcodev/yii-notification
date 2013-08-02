<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii;

// Infra
use Yii;
use CWebModule;
use NotificationYii\Models\ActiveMessenger;
use NotificationYii\Models\ActiveQueue;
use NotificationYii\Models\ActiveMessage;

// Domain
use Notification\Model\MessengerInterface;
use Notification\Model\QueueInterface;
use Notification\Model\MessageInterface;
use Notification\Service\MessageServiceInterface;

/**
 * Class Module
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class Module extends CWebModule implements MessageServiceInterface
{
	public $messengerClass = 'NotificationYii\Models\ActiveMessenger';
	public $queueClass = 'NotificationYii\Models\ActiveQueue';
	public $layout = 'main';

	protected function preinit()
	{
		parent::preinit();
		$this->controllerNamespace = __NAMESPACE__ . '\Controllers';
		$this->setControllerPath(__DIR__ . '/Controllers');
		$this->setViewPath(__DIR__ . '/Views');
	}

	/**
	 * @param $id
	 * @return MessengerInterface
	 */
	public function getMessenger($id)
	{
		/** @var \CActiveRecord $class */
		$class = $this->messengerClass;

		/** @var ActiveMessenger $model */
		$model = $class::model()->findByPk($id);

		if (!$model) {
			$model = new $class();
			$model->id = $id;
			$model->save();
		}

		return $model;
	}

	/**
	 * @param $id
	 * @return QueueInterface
	 */
	public function getQueue($id)
	{
		/** @var \CActiveRecord $class */
		$class = $this->queueClass;

		/** @var ActiveQueue $model */
		$model = $class::model()->findByPk($id);

		if (!$model) {
			$model = new $class();
			$model->id = $id;
			$model->save();
		}

		return $model;
	}

	public function notify($messengerId, $messageMeta)
	{
		$messenger = $this->getMessenger($messengerId);
		$message = new ActiveMessage();
		$message->setMeta($messageMeta);
		$messenger->send($message);
	}

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
	 * @throws \CDbException|\Exception
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
			} catch (\CDbException $e) {
				$transaction->rollback();
				throw $e;
			}
		}

		return $this;
	}
}
