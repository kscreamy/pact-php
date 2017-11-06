<?php
namespace PhpPact\Mocks\MockHttpService\Matchers;

use PhpPact\Matchers\Checkers\IMatchChecker;
use PhpPact\Matchers\Checkers\MatcherResult;
use PhpPact\Matchers\Checkers\SuccessfulMatcherCheck;
use PhpPact\Mocks\MockHttpService\Models\IHttpMessage;

/**
 * Class UrlEncodedHttpBodyMatchChecker
 * @package PhpPact\Mocks\MockHttpService\Matchers
 */
class UrlEncodedHttpBodyMatchChecker implements IMatchChecker
{
    /**
     * {@inheritdoc}
     */
    public function match($path, $expected, $actual, $matchingRules = array())
    {
        if (!($expected instanceof IHttpMessage)) {
            throw new \Exception("Expected is not an instance of IHttpMessage: " . print_r($expected, true));
        }

        if (!($actual instanceof IHttpMessage)) {
            throw new \Exception("Actual is not an instance of IHttpMessage: " . print_r($actual, true));
        }

        //$actualBody = $actual->getBody();
        //$expectedBody = $expected->getBody();
        //todo
        return new MatcherResult(new SuccessfulMatcherCheck($path));
    }
}