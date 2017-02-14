<?php
namespace RedisToolsTest;

use PHPUnit\Framework\TestCase;
use RedisTools\Hosts;

class HostsTest extends TestCase
{
    public function testSuccessfulMultiServiceParse()
    {
        $vcapServices = '{"p-redis": [{"credentials": {"password": "pass", "host": "frost-redis", "port": "6379"}}, {"credentials": {"password": "pass2", "host": "frost-redis-2", "port": "6380"}}]}';

        $services = Hosts::parseServices($vcapServices);
        $this->assertCount(2, $services);

        $this->assertEquals('frost-redis', $services[0]['host']);
        $this->assertEquals(6379, $services[0]['port']);
        $this->assertEquals('pass', $services[0]['options']['parameters']['password']);

        $this->assertEquals('frost-redis-2', $services[1]['host']);
        $this->assertEquals(6380, $services[1]['port']);
        $this->assertEquals('pass2', $services[1]['options']['parameters']['password']);
    }

    public function testSuccessfulOneServiceParse()
    {
        $vcapServices = '{"p-redis": [{"credentials": {"password": "pass", "host": "frost-redis", "port": "6379"}}]}';

        $services = Hosts::parseServices($vcapServices);
        $this->assertCount(1, $services);

        $this->assertEquals('frost-redis', $services[0]['host']);
        $this->assertEquals(6379, $services[0]['port']);
        $this->assertEquals('pass', $services[0]['options']['parameters']['password']);
    }

    public function testExceptionWhenNoRedisService()
    {
        $vcapServices = '{"some-other-service": [{"credentials": {"password": "pass", "host": "frost-redis", "port": "6379"}}]}';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Environment must contain at least one p-redis service');

        Hosts::parseServices($vcapServices);
    }

    public function testExceptionWhenNoServices()
    {
        $vcapServices = '';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Environment must contain at least one p-redis service');

        Hosts::parseServices($vcapServices);
    }
}
