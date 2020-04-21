<?php

namespace App\Service;

use App\Entity\Contact;
use Doctrine\Common\Persistence\ManagerRegistry;


/**
 * 
 */
class InfosContacts 
{
	
	private $doctrine;

	public function __construct(ManagerRegistry $doc)
	{
		$this->doctrine = $doc;
	}



	public function GetNbrContacts(int $privateKey)
	{
		return $this->doctrine->getRepository(Contact::class)->findAllByKeyOnly($privateKey);
	}


}