<?php

namespace JJSR\Bundle\GameSessionBundle\Tests\Entity;

use JJSR\Bundle\GameSessionBundle\Entity\Language;

class LanguageTest extends \PHPUnit_Framework_TestCase
{
	public function testCreateLanguage()
	{
		$language = new Language();
		$language->setName('lt');
		$language->setDisplayName('Language test');
		$this->assertEquals('lt', $language->getName());
		$this->assertEquals('Language test',  $language->getDisplayName());
	}
}