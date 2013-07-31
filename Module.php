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

// Domain
use Notification\Model\MessengerInterface;
use Notification\Model\QueueInterface;

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

	/**
	 * @return MessengerInterface
	 */
	public function getActiveMessenger()
	{
		return $this->getMessenger(Yii::app()->user->name);
	}

	/**
	 * @param $id
	 * @return QueueInterface
	 */
	public function getMessenger($id)
	{
		/** @var \CActiveRecord $class */
		$class = $this->messengerClass;

		/** @var \CActiveRecord $model */
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

		/** @var \CActiveRecord $model */
		$model = $class::model()->findByPk($id);

		if (!$model) {
			$model = new $class();
			$model->id = $id;
			$model->save();
		}

		return $model;
	}
}
