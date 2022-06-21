<?php

namespace MatthewKilpatrick\PrefixedApiKey;

use Tuupola\Base58;

class PrefixedApiKey
{
  public function generateApiKey(string $keyPrefix, string $shortTokenPrefix='', int $shortTokenLength=8, int $longTokenLength=24): KeyComponents
  {
    if (strlen($keyPrefix) === 0) {
      throw new \Exception('Prefix required');
    }

    if (strpos($keyPrefix, '_') !== false) {
      throw new \Exception('Prefix can\'t contain underscore');
    }

    $base58 = new Base58();

    $shortTokenBytes = $this->generateRandomBytes($shortTokenLength);
    $longTokenBytes = $this->generateRandomBytes($longTokenLength);

    $shortToken = $base58->encode($shortTokenBytes);
    $shortTokenPadLen = max(0, $shortTokenLength - strlen($shortToken) - strlen($shortTokenPrefix));
    $shortToken = substr($shortTokenPrefix . str_repeat('0', $shortTokenPadLen) . $shortToken, 0, $shortTokenLength);

    $longToken = $base58->encode($longTokenBytes);
    $longTokenPadLen = max(0, $longTokenLength - strlen($longToken));
    $longToken = substr(str_repeat('0', $longTokenPadLen) . $longToken, 0, $longTokenLength);

    $longTokenHash = $this->hashLongToken($longToken);
    return new KeyComponents($keyPrefix, $shortToken, $longToken, $longTokenHash);
  }

  public function getTokenComponents(string $token): KeyComponents
  {
    $parts = explode('_', $token);
    if (count($parts) !== 3) {
      throw new \Exception('Invalid token');
    }
    return new KeyComponents($parts[0], $parts[1], $parts[2], $this->hashLongToken($parts[2]));
  }

  public function checkApiKey(string $token, string $expectedLongTokenHash): bool
  {
    $components = $this->getTokenComponents($token);
    return $components->getLongTokenHash() === $expectedLongTokenHash;
  }

  private function generateRandomBytes(int $length): string
  {
    return random_bytes($length);
  }

  private function hashLongToken(string $longToken): string
  {
    return hash('sha256', $longToken, false);
  }
}