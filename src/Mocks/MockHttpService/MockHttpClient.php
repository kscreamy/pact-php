<?php
namespace PhpPact\Mocks\MockHttpService;

use Http\Client\HttpClient;
use Psr\Http\Message\RequestInterface;

/**
 * Class MockHttpClient
 * @package PhpPact\Mocks\MockHttpService
 */
class MockHttpClient implements HttpClient
{
    /**
     * @var MockProviderHost
     */
    private $host;

    /**
     * MockHttpClient constructor.
     * @param MockProviderHost $host
     */
    public function __construct(MockProviderHost $host)
    {
        $this->host = $host;
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->host->handle($request);
    }
}
