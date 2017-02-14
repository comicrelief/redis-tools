<?php
namespace RedisTools;

class Hosts
{
    /**
     * Takes input in standard VCAP_SERVICES JSON format.
     * Retrieves configurations for any p-redis instances found.
     *
     * @param string $vcapServices
     * @return array    2-D array where 2nd dimension has keys 'host', 'port' and 'options' ready for Predis client.
     */
    public static function parseServices(string $vcapServices): array
    {
        $services = json_decode($vcapServices);
        $redisServices = $services->{'p-redis'} ?? null;

        if (!$redisServices) {
            throw new \RuntimeException('Environment must contain at least one p-redis service');
        }

        $outputConfig = [];
        foreach ($redisServices as $redisService) {
            $outputConfig[] = [
                'host' => $redisService->credentials->host,
                'port' => $redisService->credentials->port,
                'options' => [
                    'parameters' => [
                        'password' => $redisService->credentials->password,
                    ]
                ]
            ];
        }

        return $outputConfig;
    }
}
