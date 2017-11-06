<?php
namespace PhpPact\Factories;

use PhpPact\Mocks\MockHttpService\Models\ProviderServiceRequest;
use Windwalker\Http\Request\Request;
use Windwalker\Http\Stream\StringStream;
use Windwalker\Uri\PsrUri;

/**
 * Class RequestFactory
 * @package PhpPact\Factories
 */
class RequestFactory
{
    /**
     * @param ProviderServiceRequest $providerServiceRequest
     * @param string $host
     * @param string $schema
     * @return Request
     */
    public static function createFromProviderServiceRequest(
        ProviderServiceRequest $providerServiceRequest,
        $host = 'localhost',
        $schema = 'http'
    ) {
        $request = new Request();

        $request = $request->withUri(
            (new PsrUri($schema . "://" . $host))
                ->withPath($providerServiceRequest->getPath()));

        foreach ($providerServiceRequest->getHeaders() as $headerName => $value) {
            $request = $request->withAddedHeader($headerName, $value);
        }

        $body = $providerServiceRequest->getBody();

        if ($body) {
            $request = $request->withBody(new StringStream($body));
        }
        return $request->withMethod($providerServiceRequest->getMethod());
    }
}
