<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii\Tests;

use Yii;
use CVarDumper;
use NotificationYii\Components\ModelMessage;

/**
 * Class Controller
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class Controller
{
	public function actionIndex()
	{
		// Создаем месенжер текущего пользователя и подпсиваем на него очереди
		// $this->initMessenger();

		// Добавляем сообщения в месенжер
		$this->addMessages();

		// Читаем сообщения из очереди
		// Можно кроном, можно onRequestEnd
		$this->readMessages();
	}

	/**
	 * @return \NotificationYii\Module CModule
	 */
	protected function getService()
	{
		return Yii::app()->getModule('notification');
	}

	protected function initMessenger()
	{
		// Месенжер текущего пользователя
		$messenger = $this->getService()->getActiveMessenger();

		// Очередь сообщений попадающих в лог
		$queue = $this->getService()->getQueue('log');
		$messenger->bind($queue);

		// Очередь сообщений отправляемых на почту в суппорт
		$queue = $this->getService()->getQueue('support-mail');
		$messenger->bind($queue);

		// Подписываем очередь какого-то юзера
		$queue = $this->getService()->getQueue('some-user-id');
		$messenger->bind($queue);
	}

	protected function addMessages()
	{
		// Месенжер текущего пользователя
		$messenger = $this->getService()->getActiveMessenger();

		// Отправляем сообщения
		$model = new UserModel();
		$model->onAfterValidate = function($event) use ($messenger) {
			// Месенжер текущего пользователя отправляет сообщение
			$msg = new ModelMessage('user.validate', $event->sender);
			$messenger->send($msg);
		};
		// Дваждыы проводим валидацию, чтоб добавить два сообщения
		$model->validate();
		$model->validate();

		$model = new PostModel();
		$model->onAfterValidate = function($event) use ($messenger) {
			$msg = new ModelMessage('post.validate', $event->sender);
			$messenger->send($msg);
		};
		$model->validate();
	}

	protected function readMessages()
	{
		$queue = $this->getService()->getQueue('log');

		// В очереди 3 сообщения
		CVarDumper::dump($queue->count(), 1, true);

		// Пропускаем одно (первое вошедшее - валидация юзера)
		$queue->seek(1);

		$logger = new \NotificationYii\Components\LogMessageService();
		$logger->debug = true;

		while ($msg = $queue->dequeue()) {
			$logger->publish($msg);
		}
	}
}
