<?php
namespace Longman\TelegramBot\Commands;

use \Longman\TelegramBot\Request;
use \Longman\TelegramBot\Entities\Update;

class testCommand extends Command
{
    protected $name = 'test';                      //your command's name
    protected $description = 'A command for test'; //Your command description
    protected $usage = '/test';                    // Usage of your command
    protected $version = '1.0.0';
    protected $enabled = true;
    protected $public = true;


    public function execute()
    {
        $update = $this->getUpdate();                //get Updates
        $message = $this->getMessage();              // get Message info

        $chat_id = $message->getChat()->getId();     //Get Chat Id
        $message_id = $message->getMessageId();      //Get message Id
        //$text = $message->getText(true);           // Get recieved text

        $data = array();                             // prepapre $data
        $data['chat_id'] = $chat_id;                 //set chat Id
        $data['reply_to_message_id'] = $message_id;  //set message Id
        $data['text'] = 'This is just a Test...';    //set reply message

        $result = Request::sendMessage($data);       //send message
        return $result;
     }

}
