<?php
/**
 * Slavcodev Components
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

namespace NotificationYii\Components;

// Infra
use Notification\Model\MessengerInterface;
use Notification\Model\QueueInterface;
use Yii;
use CApplicationComponent;
use CLogger;
use StdLib\VarDumper;

// Domain
use Notification\Model\MessageInterface;
use Notification\Service\MessageServiceInterface;

/**
 * Class LogMessageService
 *
 * @author Veaceslav Medvedev <slavcopost@gmail.com>
 * @version 0.1
 */
class LogMessageService extends CApplicationComponent implements MessageServiceInterface
{
	/** @var bool */
	public $debug = false;
	/** @var string */
	public $level = CLogger::LEVEL_INFO;
	/** @var string */
	public $category = 'notify';

	public function publish(MessageInterface $message)
	{
		if ($this->debug) {
			VarDumper::dump($message);
		} else {
			Yii::log(serialize($message), $this->level, $this->category);
		}

		return $this;
	}

	/**
	 * @param MessageInterface $message
	 * @param MessengerInterface $messenger
	 * @return MessageServiceInterface
	 */
	public function send(
		MessageInterface $message,
		MessengerInterface $messenger
	) {
		foreach ($messenger->getIterator() as $queue) {
			$queue->enqueue($message);
		}

		return $this;
	}

	/**
	 * @param QueueInterface $queue
	 * @return MessageServiceInterface
	 */
	public function dispatch(QueueInterface $queue)
	{
		while ($message = $queue->dequeue()) {
			$this->publish($message);
		}

		return $this;
	}
}
