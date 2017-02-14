Redis tools
===========
So far this repo contains just one Redis tool, to set configuration periodically. It might get more later.

Tests
-----
`composer test`

Set Redis config tool
---------------------
The purpose of this tool is to set some configuration values on running Redis instances, without a restart.

### Why?

We use [shared Cloud Foundry infrastructure](https://run.pivotal.io/) where our deployment process is shared with other users. We don't currently have control over the configuration of Redis instances at launch.

While we may set configuration ourselves, it is possible that CloudOps will replace instances and they will return to the default configuration. So to keep configuration in place reliably, we must set it periodically on a schedule.

### Running regularly from CI

This tool is designed to be run on a schedule. We do this by running the app on a schedule, once in each Cloud Foundry space, from Concourse CI.

See [`ci`](./ci) for the manifest files we use to tell Cloud Foundry which Redis instances to bind.

### Configs values set

We just change one value currently, `maxmemory-policy`. This is because the default for recent Redis versions is `noeviction`, which means virtual machines will fill up and eventually fall over.

We set it to `allkeys-lru` instead, which means that the least recently used keys are always evicted first. See [Using Redis as an LRU cache](https://redis.io/topics/lru-cache).

### Environment variables

This script currently supports working only with p-redis services instances on Cloud Foundry. It assumes that configuration happens using a secret hash alias of _CONFIG_.

When testing on an unsecured local Redis, you can set:

    CONFIG_ALIAS=CONFIG

and (for example):

    VCAP_SERVICES={"p-redis": [{"credentials": {"password": "yourpassword", "host": "your-redis", "port": "6379"}}]}

### Run on local

You can run this tool locally against a Docker-ised Redis.

If you have `frost-tools` and the CR directory structure:

* `cd ~/cr_root/frost/frost-tools/docker`
* `docker-compose run redis-tools php bin/set-redis-config.php`
