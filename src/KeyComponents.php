<?php

namespace MatthewKilpatrick\PrefixedApiKey;

class KeyComponents
{
  private string $prefix;
  private string $shortToken;
  private string $longToken;
  private string $longTokenHash;

  public function __construct(string $prefix, string $shortToken, string $longToken, string $longTokenHash)
  {
    $this->prefix = $prefix;
    $this->shortToken = $shortToken;
    $this->longToken = $longToken;
    $this->longTokenHash = $longTokenHash;
  }

  public function getPrefix(): string
  {
    return $this->prefix;
  }

  public function getToken(): string
  {
    return "{$this->prefix}_{$this->shortToken}_{$this->longToken}";
  }

  public function getShortToken(): string
  {
    return $this->shortToken;
  }

  public function getLongToken(): string
  {
    return $this->longToken;
  }

  public function getLongTokenHash(): string
  {
    return $this->longTokenHash;
  }
}