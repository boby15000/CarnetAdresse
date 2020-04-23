<?php

namespace App\Service\Email;


/**
 * Service d'envoie de mail ou de SMS.
 * @author   Nicolas Fourgheon "boby15000@hotmail.com"
 * @license  MIT https://opensource.org/licenses/MIT
 * @date 13/04/2020
 */
class Message 
{
	const PATERN_EMAIL = "%^[^\W][a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\@[a-zA-Z0-9_]+(\.[a-zA-Z0-9_]+)*\.[a-zA-Z]{2,4}$%";
	const EMAIL_FROM = 'boby15000@hotmail.com';
	const EMAIL_NAME = 'Fourgheon Nicolas' ;

	private $indiceTo = 0 ;
	private $message;
	

	public function __construct()
	{
		$this->from( self::EMAIL_FROM,  self::EMAIL_NAME );
	}

	

	public function getMessage(): array
	{
		return $this->message; 
	}

	/*
	 * @param Subject $subject Sujet du Message
	 * @throws MyBiduleNotFoundException
	 * @return Message
	 */
	public function subject(string $subject): self
	{
		$this->message['Subject'] = $subject; 

		return $this;
	}

	/*
	 * @param Email $email Email de l'expéditeur
	 * @param Name $name Nom de l'expiditeur (optionel)
	 * @throws MyBiduleNotFoundException
	 * @return MailJet
	 */
	public function from(string $email, string $name = ''): self
	{
		$this->message['From'] = ['Email' => $email, 'Name' => $name];
		
		return $this;
	}


	public function to(string $email, string $name = ''): self
	{
		$this->message['To'][$this->indiceTo] = ['Email' => $email, 'Name' => $name];

		$this->indiceTo++;
		
		return $this;
	}


	public function toMulti(array $email): self
	{
		foreach ($email as $key => $value) {
			
			if ( preg_match(self::PATERN_EMAIL, $key) )
			{ $this->To($key, $value); }
			else
			{ $this->To($value, $key); }
		}

		return $this;
	}

	
	public function text(string $text): self
	{
		$this->message['TextPart'] = $text; 

		return $this;
	}


	public function html(string $html): self
	{
		$this->message['HTMLPart'] = $html ; 

		return $this;
	}


	public function joinFile(): self
	{
		return $this;
	}

}