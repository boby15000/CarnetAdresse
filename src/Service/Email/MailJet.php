<?php

namespace App\Service\Email;

use Mailjet\Client;
use \Mailjet\Resources;
use App\Service\Email\Message;


/**
 * Service d'envoie de mail ou de SMS.
 * @author   Nicolas Fourgheon "boby15000@hotmail.com"
 * @license  MIT https://opensource.org/licenses/MIT
 * @date 13/04/2020
 */
class MailJet 
{

	//private const KEY_API ='e53d7c5ce90f1a3d04922f6356c1164b';
	//private const KEY_PRIVATE = 'd7ff7d022f91cf570172a329340800e9';

	private $mj;
	private $indiceMessage;
	private $bodyMessage;
	private $status;

	public function getStatus(): ?array
	{
		return $this->status;
	}


	public function __construct(string $keyAPI, string $keyPrivate)
	{
		$this->mj = new Client($keyAPI, $keyPrivate,true,['version' => 'v3.1']);
	}


	public function addMessage(Message $message): self
	{
		if ( $this->bodyMessage == null )
		{ $this->indiceMessage = 0; }
		else
		{ $this->indiceMessage++ ; }	

		$this->bodyMessage['Messages'][$this->indiceMessage] = $message->getMessage() ;

		return $this;
	}




	public function send(): bool
	{
		//dump($this->bodyMessage);
		$response = $this->mj->post(Resources::$Email, ['body' => $this->bodyMessage]);
		$this->status = $response->getData();
		return $response->success();
	}




}