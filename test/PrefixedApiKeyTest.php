<?php

use MatthewKilpatrick\PrefixedApiKey\KeyComponents;
use MatthewKilpatrick\PrefixedApiKey\PrefixedApiKey;
use PHPUnit\Framework\TestCase;

class PrefixedApiKeyTest extends TestCase
{

  private function getExampleKeyComponents(): KeyComponents
  {
    return new KeyComponents(
      'demo',
      'Fl4vboTd',
      'ApDAFQC6PCB2FD66to1FIZCi',
      'e5cd40e8bf7701fea8f0a7e7d924813636f91f6481ea46ffa54d2ecacc856aab'
    );
  }

  public function testGenerateApiKey(): void
  {
    $generator = new PrefixedApiKey();
    $components = $generator->generateApiKey('token', 'stp', 8, 24);

    $this->assertStringStartsWith('token_', $components->getToken(), 'Token should be correctly prefixed');
    $this->assertStringStartsWith('stp', $components->getShortToken(), 'Short token should be correctly prefixed');
    $this->assertEquals(8, strlen($components->getShortToken()), 'Short token length should match specified length');
    $this->assertEquals(24, strlen($components->getLongToken()), 'Long token length should match specified length');
  }

  public function testGenerateApiKeyWithUnderscoreInPrefix(): void
  {
    $this->expectExceptionMessage('Prefix can\'t contain underscore');
    $generator = new PrefixedApiKey();
    $generator->generateApiKey('token_with_underscores');
  }

  public function testGenerateApiKeyWithEmptyPrefix(): void
  {
    $this->expectExceptionMessage('Prefix required');
    $generator = new PrefixedApiKey();
    $generator->generateApiKey('');
  }

  public function testGetValidTokenComponents(): void
  {
    $generator = new PrefixedApiKey();
    $components = $generator->getTokenComponents('demo_Fl4vboTd_ApDAFQC6PCB2FD66to1FIZCi');
    $expectedComponents = $this->getExampleKeyComponents();

    $this->assertEquals($expectedComponents->getPrefix(), $components->getPrefix(), 'Prefix should be correctly extracted');
    $this->assertEquals($expectedComponents->getShortToken(), $components->getShortToken(), 'Short token should be correctly extracted');
    $this->assertEquals($expectedComponents->getLongToken(), $components->getLongToken(), 'Long token should be correctly extracted');
    $this->assertEquals($expectedComponents->getLongTokenHash(), $components->getLongTokenHash(), 'Long token hash should be correctly extracted');
  }

  public function testGetInvalidTokenComponents(): void
  {
    $this->expectExceptionMessage('Invalid token');
    $generator = new PrefixedApiKey();
    $generator->getTokenComponents('demo_Fl4vboTd_ApDAFQC6PCB2FD66to1FIZCi_invaliddata');
  }

  public function testCheckValidApiKey(): void
  {
    $generator = new PrefixedApiKey();
    $this->assertTrue($generator->checkApiKey('demo_Fl4vboTd_ApDAFQC6PCB2FD66to1FIZCi', $this->getExampleKeyComponents()->getLongTokenHash()), 'Valid token should be correctly identified');

  }

  public function testCheckInvalidApiKey(): void
  {
    $generator = new PrefixedApiKey();
    $this->assertFalse($generator->checkApiKey('demo_Fl4vboTd_ApDAFQC6PCB2FD66to1FIZCj', $this->getExampleKeyComponents()->getLongTokenHash()), 'Invalid token should be correctly identified');
  }
}