<?php

namespace App\Service\Email;

use \Mailjet\Client;
use \Mailjet\Resources;
use \Mailjet\Response;
use App\Service\Email\Message;


/**
 * Service d'envoie de mail ou de SMS.
 * @author   Nicolas Fourgheon "boby15000@hotmail.com"
 * @license  MIT https://opensource.org/licenses/MIT
 * @date 13/04/2020
 */
class Mailjet 
{

	private $mj;
	private $indiceMessage;
	private $bodyMessage;
	private $status;
	private $emailFrom;
	private $emailName;

	public function GetStatus(): ?array
	{
		return $this->status;
	}


	public function __construct(string $keyAPI, string $keyPrivate, string $emailFrom, ?string $emailName)
	{
		$this->mj = new Client($keyAPI, $keyPrivate,true,['version' => 'v3.1']);
		$this->emailFrom = $emailFrom;
		$this->emailName = $emailName;
	}


	public function NewMessage(): Message
	{
		return new Message($this->emailFrom, $this->emailName);
	}



	public function AddMessage(Message $message): self
	{
		if ( $this->bodyMessage == null )
		{ $this->indiceMessage = 0; }
		else
		{ $this->indiceMessage++ ; }	

		$this->bodyMessage['Messages'][$this->indiceMessage] = $message->getMessage() ;

		return $this;
	}




	public function Send(): bool
	{
		//dump($this->bodyMessage);
		$response = $this->mj->post(Resources::$Email, ['body' => $this->bodyMessage]);
		$this->status = $response->getData();
		return $response->success();
	}




}