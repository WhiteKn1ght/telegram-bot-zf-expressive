<?php
namespace Bot\Telegram\Objects;

/**
 *
 * @author rusakov.vv
 * Class Chat
 * *
 *
 * @method int      getId()         Unique identifier for this user or bot.
 * @method bool     getIsBot()      True, if this user is a bot
 * @method string   getFirstName()  User's or bot's first name.
 * @method string   getLastName()   (Optional). User's or bot's last name.
 * @method string   getUsername()   (Optional). User's or bot's username.
 *
 */
class Chat extends BaseObject
{
    /**
     * {@inheritdoc}
     */
    public function relations()
    {
        return [];
    }
}

