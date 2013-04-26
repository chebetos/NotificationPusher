<?php

namespace Sly\NotificationPusher\Pusher;

use Sly\NotificationPusher\Pusher\BasePusherInterface;
use Sly\NotificationPusher\Collection\MessagesCollection;
use Sly\NotificationPusher\Model\MessageInterface;

/**
 * BasePusher.
 *
 * @uses BasePusherInterface
 * @author Cédric Dugat <ph3@slynett.com>
 */
class BasePusher implements BasePusherInterface
{
    protected $config;
    protected $connection;
    protected $messages;

    /**
     * Constructor.
     *
     * @param array $config Configuration
     */
    public function __construct(array $config = array())
    {
        $this->config   = array_merge($this->getDefaultConfig(), $config);
        $this->messages = new MessagesCollection();
    }

    /**
     * Get default configuration.
     *
     * @return array
     */
    protected function getDefaultConfig()
    {
        return array(
            'dev'      => false,
            'simulate' => false,
            'feedback' => false,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function addMessage(MessageInterface $message)
    {
        $this->messages->set($message);

        return $this->messages;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessages()
    {
        return $this->messages->getMessages();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        if (null === $this->connection) {
            $this->connection = $this->initAndGetConnection();
        }

        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function push()
    {
        $this->prePush();

        $this->connection = $this->initAndGetConnection();

        foreach ($this->getMessages() as $message) {
            if (true === $this->config['simulate']) {
                $message->setStatus(MessageInterface::STATUS_SIMULATED_SENT);
            } elseif (true === $this->pushMessage($message)) {
                $message->setStatus(MessageInterface::STATUS_SENT);
            } else {
                $message->setStatus(MessageInterface::STATUS_FAILED);
            }
        }

        $this->postPush();

        return $this->messages->getSentMessages();
    }

    /**
     * {@inheritdoc}
     */
    public function prePush()
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function postPush()
    {
        return $this;
    }
}
