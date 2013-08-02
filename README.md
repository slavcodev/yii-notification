Notifications Yii Module
=====

*Integration of the [Notification DDD](https://github.com/slavcodev-ddd/notification) for Yii 1.1*

## Install

Configure your application main.php

```php
return array(
	'aliases' => array(
		// Download Notification-DDD components into vendor path
		'Notification' => 'webroot.vendor.notification.src.notification',
		// And this module tu modules path
		'NotificationYii' => 'application.modules.notification',
	),

	'modules' => array(
		'notification' => array(
			'class' => '\NotificationYii\Module',
			// Setup module to use application views path
			'viewPath' => dirname(dirname(__DIR__)) . '/views/notifications',
		),
	),
);
```

## How it works

The module has three entity:

- Messenger
- Queue
- Message (aka notification)

## Usage

You have model Post with 2 events: onUpdate, onComment.


```php
class Post extends ActiveRecord {
	// You create for this model the unique messenger ID.
	public function getMessengerId() {
		return 'post-' . $this->id;
	}

	// Bind events handlers
	public function init() {
		parent::init();

		$this->onUpdate = function($event) {
			Yii::app()
				->getModule('notification')
				->notify(
					$event->sender->getMessengerId(),
					// Add message data
					array(
						'type' => 'post.update',
						'postId' => $event->sender->id,
					)
				);

		$this->onComment = function($event) {
			Yii::app()
				->getModule('notification')
				->notify(
					$event->sender->getMessengerId(),
					// Add message data
					array(
						'type' => 'post.comment',
						'postId' => $event->sender->id,
						'commentId' => $event->params['comment']->id,
					)
				);
		}
	}
}
```

Now you need to create queues and bind it to this messenger.

```php
class User extends ActiveRecord {
	// You create for this model the unique queue ID.
	public function getQueueId() {
		return 'user-' . $this->id;
	}

	// Call this method to bind user queue to post messenger.
	// For example on user add your first comment on this post.
	public function watchPost(Post $post) {
		$factory = Yii::app()->getModule('notification');
		$messenger = $factory->getMessenger($post->getMessengerId());
		$queue = $factory->getQueue($this->getQueueId());
		$messenger->bind($queue);
	}
}
```

Well done, when events trigger in user queue will be enqueue new messages.

Examples how works with queue:

```php
$factory = Yii::app()->getModule('notification');
$userQueue = $factory->getQueue(Yii::app()->user->model->getQueueId());

// Get queue count, e.g. to add label to menu
echo $userQueue->count();

// Queue works in FIFO model (first input, first output)
// To skip too many messages leaving 1000
$userQueue->seek($userQueue->count() - 1000);

// Traverse remaining 1000 messages
while ($msg = $userQueue->dequeue()) {
	echo $msg->getMeta('type');
}
```
