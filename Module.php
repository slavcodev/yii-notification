<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii;

// Infra
use CAction;
use CController;
use Yii;
use CWebModule;
use NotificationYii\Models\ActiveMessenger;
use NotificationYii\Models\ActiveQueue;
use NotificationYii\Models\ActiveMessage;
use NotificationYii\Components\ActiveMessageService;

/**
 * Class Module
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class Module extends CWebModule
{
	public $messengerClass = 'NotificationYii\Models\ActiveMessenger';
	public $queueClass = 'NotificationYii\Models\ActiveQueue';
	public $messageClass = 'NotificationYii\Models\ActiveMessage';
	public $layout = 'main';

	protected $messageService;

	protected function preinit()
	{
		parent::preinit();
		$this->controllerNamespace = __NAMESPACE__ . '\Controllers';
		$this->setControllerPath(__DIR__ . '/Controllers');
		$this->setViewPath(__DIR__ . '/Views');
	}

	public function beforeControllerAction($controller, $action)
	{
		// Забираем сообщения пользователя  из очереди ожидания
		$queue = $this->getQueue('user-' . Yii::app()->getUser()->getId());
		$this->getMessageService()->dispatch($queue);

		return parent::beforeControllerAction($controller, $action);
	}

	/**
	 * @return ActiveMessageService
	 */
	public function getMessageService()
	{
		if (null === $this->messageService) {
			$this->messageService = new ActiveMessageService();
		}

		return $this->messageService;
	}

	/**
	 * @param $id
	 * @return ActiveMessenger
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
	 * @return ActiveQueue
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
		$this->getMessageService()->send(
				$this->createMessage()->setMeta($messageMeta),
				$this->getMessenger($messengerId)
			);

		return $this;
	}

	/**
	 * @return ActiveMessage
	 */
	public function createMessage()
	{
		return new $this->messageClass;
	}
}
