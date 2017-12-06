<?php

namespace PhpPact\Mocks\MockHttpService\Mappers;

use PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile;
use PhpPact\Models\Pacticipant;

class ProviderServicePactMapper implements \PhpPact\Mappers\IMapper
{
    /**
     *
     * @param \stdClass $json
     * @return ProviderServicePactFile
     */
    public function convert($request)
    {
        if (is_string($request)) {
            $request = \json_decode($request);
        }

        $pact = new ProviderServicePactFile();

        $provider = new Pacticipant();
        $provider->setName($request->provider);
        $pact->setProvider($provider);

        $consumer = new Pacticipant();
        $consumer->setName($request->consumer);
        $pact->setConsumer($consumer);

        $pact->setMetaData($request->metadata);
        $pact->setInteractions($request->interactions);

        $matches = [];
        preg_match('#/pact-version/([^\/]+)/#', $request->_links->{"pb:publish-verification-results"}->href, $matches);

        $pact->setPactUuid($matches[1]);

        $pact->setConsumerVersion($request->_links->{"pb:consumer-version"}->name);

        return $pact;
    }
}
