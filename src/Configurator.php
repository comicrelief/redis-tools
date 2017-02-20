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
     * Sets the Redis eviction policy, if it's not already what we want it to be.
     *
     * @param \Predis\Client    $client
     * @param string            $policyName A valid `maxmemory-policy` value - see https://redis.io/topics/lru-cache
     * @return bool Success
     * @throws \RuntimeException if Redis returns an error response
     */
    public function setEvictionPolicy(\Predis\Client $client, string $policyName)
    {
        $currentPolicy = $this->getEvictionPolicy($client);

        if ($currentPolicy === $policyName) {
            return true; // No need to change anything
        }

        echo 'Proceeding to set config...' . PHP_EOL;

        $response = $client->executeRaw([$this->configAlias, 'set', 'maxmemory-policy', $policyName], $wasError);

        if ($wasError) {
            $client->disconnect();
            throw new \RuntimeException('Redis error setting eviction policy: ' . $response);
        }

        return true;
    }

    /**
     * @param \Predis\Client $client
     * @return string
     */
    public function getEvictionPolicy(\Predis\Client $client)
    {
        $response = $client->executeRaw([$this->configAlias, 'get', 'maxmemory-policy'], $wasError);

        if ($wasError) {
            $client->disconnect();
            throw new \RuntimeException('Redis error getting eviction policy: ' . $response);
        }

        echo 'Current policy is ' . $response . '.' . PHP_EOL;

        return $response;
    }
}
