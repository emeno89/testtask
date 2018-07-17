<?php

namespace src\Integration {

    interface DataProviderInterface
    {
        public function get(array $request);
    }

    class SimpleDataProvider implements DataProviderInterface
    {
        /** @var string */
        private $host;
        /** @var string */
        private $user;
        /** @var string */
        private $password;

        public function __construct(string $host, string $user, string $password)
        {
            $this->host = $host;
            $this->user = $user;
            $this->password = $password;
        }

        public function get(array $request): array
        {
            return [
                'id'    =>  uniqid(),
                'name'  =>  random_bytes(24)
            ];
        }
    }
}

namespace src\Manager {

    use Psr\Log\LoggerInterface;
    use Psr\SimpleCache\CacheInterface;
    use src\Integration\DataProviderInterface;

    class DataManager
    {
        const CACHE_TIME = '+1 day';

        /** @var DataProviderInterface */
        private $provider;
        /** @var CacheInterface */
        private $cache;
        /** @var LoggerInterface */
        private  $logger;

        public function __construct(
            DataProviderInterface $provider,
            CacheInterface $cache,
            LoggerInterface $logger
        ) {
            $this->provider = $provider;
            $this->cache = $cache;
            $this->logger = $logger;
        }

        public function getExternalData(array $input): array
        {
            try {

                $cacheKey = $this->getCacheKey($input);

                $cacheItem = $this->cache->get($cacheKey);
                if ($cacheItem) {
                    return $cacheItem;
                }

                $responseItem = $this->provider->get($input);

                if ($responseItem) {
                    $this->cache->set($cacheKey, $responseItem, time + strtotime(self::CACHE_TIME));
                }

                return $responseItem;
            } catch(\Exception $e) {
                $this->logger->critical($e->getMessage(), $input);
            }

            return [];
        }

        private function getCacheKey(array $input): string
        {
            return md5(serialize($input));
        }
    }
}