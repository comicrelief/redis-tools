<?php
namespace RedisTools;

class Configurator
{
    /** @var string Secret alias to Redis config command */
    private $configAlias;

    /**
     * @param string $configAlias
     * @throws \RuntimeException if config alias is missing or empty
     */
    public function __construct(string $configAlias)
    {
        if (empty($configAlias)) {
            throw new \RuntimeException('Config alias must be set');
        }

        $this->configAlias = $configAlias;
    }

    /**
     * @param \Predis\Client    $client
     * @param string            $policyName A valid `maxmemory-policy` value - see https://redis.io/topics/lru-cache
     * @return bool Success
     * @throws \RuntimeException if Redis returns an error response
     */
    public function setEvictionPolicy(\Predis\Client $client, string $policyName)
    {
        $response = $client->executeRaw([$this->configAlias, 'set', 'maxmemory-policy', $policyName], $wasError);

        if ($wasError) {
            $client->disconnect();
            throw new \RuntimeException('Redis error setting eviction policy: ' . $response);
        }

        return true;
    }
}
