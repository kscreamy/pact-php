<?php

namespace PhpPact\Mocks\MockHttpService\Models;

use PhpPact\Mocks\MockHttpService\Matchers\JsonHttpBodyMatchChecker;
use PhpPact\Mocks\MockHttpService\Matchers\SerializeHttpBodyMatchChecker;
use PhpPact\Mocks\MockHttpService\Matchers\UrlEncodedHttpBodyMatchChecker;
use PhpPact\Mocks\MockHttpService\Matchers\XmlHttpBodyMatchChecker;
use PhpPact\Matchers\Rules\MatchingRule;

class ProviderServiceRequest implements \JsonSerializable, IHttpMessage
{
    private $_bodyWasSet;
    private $_body;
    private $_method; // use setMethod
    private $_path; //[JsonProperty(PropertyName = "path")]
    private $_headers; //[JsonProperty(PropertyName = "headers")] / [JsonConverter(typeof(PreserveCasingDictionaryConverter))]

    private $_bodyMatchers;
    private $_query; //[JsonProperty(PropertyName = "query")]
    private $_matchingRules;

    public function __construct($method, $path, $headers = null, $body = false, $matchingRules = array())
    {
        // enumerate over HttpVerb to set the value of the
        $verb = new HttpVerb();
        $this->_method = $verb->Enum($method);
        $this->_path = $path;
        if ($headers) {
            $this->_headers = $headers;
        }

        if ($body !== false) {
            $this->setBody($body);
        }


        $this->setMatchingRules($matchingRules);
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        if ($this->_bodyWasSet) {
            return $this->_body;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function setBody($body)
    {
        $this->_bodyWasSet = true;

        if (is_string($body) && strtolower($body) === "null") {
            $body = null;
        }

        $this->_body = $this->parseBodyMatchingRules($body);

        return false;
    }

    /**
     * @return int|mixed
     */
    public function getMethod()
    {
        return $this->_method;
    }


    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->_path;
    }


    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @return mixed
     */
    public function setHeaders($headers)
    {
        $this->_headers = $headers;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        if (isset($this->_query)) {
            return $this->_query;
        }

        return false;
    }

    /**
     * @param mixed $Query
     */
    public function setQuery($query)
    {
        $this->_query = $query;
    }

    /**
     * @return mixed
     */
    public function getBodyMatchers()
    {
        return $this->_bodyMatchers;
    }

    /**
     * @return array
     */
    public function getMatchingRules()
    {
        return $this->_matchingRules;
    }

    /**
     * @param array|false $matchingRules
     */
    public function setMatchingRules($matchingRules)
    {
        if (count($matchingRules) > 0) {
           foreach ($matchingRules as $matchingRule) {
                $this->addMatchingRule($matchingRule);
           }
        }
    }

    /**
     * Add a single matching rule
     *
     * @param MatchingRule $matchingRule
     */
    public function addMatchingRule(MatchingRule $matchingRule)
    {
        $this->_matchingRules[$matchingRule->getJsonPath()] = $matchingRule;
    }


    public function shouldSerializeBody()
    {
        return $this->_bodyWasSet;
    }

    public function pathWithQuery()
    {
        if (!$this->_path && !$this->_query) {
            throw new \RuntimeException("Query has been supplied, however Path has not. Please specify as Path.");
        }

        return !($this->_query) ?
            sprintf("%s?%s", $this->_path, $this->_query) :
            $this->_path;
    }

    private function parseBodyMatchingRules($body)
    {
        $this->_bodyMatchers = array();

        if ($this->getContentType() == "application/json") {
            $this->_bodyMatchers[] = new JsonHttpBodyMatchChecker(false);
        } elseif ($this->getContentType() == "text/plain") {
            $this->_bodyMatchers[] = new SerializeHttpBodyMatchChecker();
        } elseif ($this->getContentType() == "application/xml") {
            $this->_bodyMatchers[] = new XmlHttpBodyMatchChecker(false);
        } elseif ($this->getContentType() == "application/x-www-form-urlencoded") {
            $this->_bodyMatchers[] = new UrlEncodedHttpBodyMatchChecker(false);
        } else {
            // make JSON the default based on specification tests
            $this->_bodyMatchers[] = new JsonHttpBodyMatchChecker(false);
        }

        return $body;
    }

    /**
     * Return the header value for Content-Type
     *
     * False is returned if not set
     *
     * @return mixed|bool
     */
    public function getContentType()
    {
        $headers = $this->getHeaders();
        $key = 'Content-Type';
        if (is_object($headers) && isset($headers->$key)) {
            return $headers->$key;
        }

        if (is_array($headers) && isset($headers[$key])) {
            return $headers[$key];
        }
        return 'application/json';
    }


    public function jsonSerialize()
    {
        // this _should_ cascade to child classes
        $obj = new \stdClass();
        $obj->method = $this->_method;
        $obj->path = $this->_path;

        if ($this->_query) {
            $obj->query = $this->_query;
        }

        if ($this->_headers) {
            $header = $this->_headers;
            if (is_array($header)) {
                $header = (object)$header;
            }
            $obj->headers = $header;
        }

        if ($this->_body) {
            $obj->body = $this->_body;
        }

        if (count($this->_matchingRules) > 0) {
            $obj->matchingRules = new \stdClass();
            foreach($this->_matchingRules as $matchingRuleVo) {

                /**
                 * @var $matchingRuleVo MatchingRule
                 */
                $jsonPath = $matchingRuleVo->getJsonPath();
                $obj->matchingRules->$jsonPath = $matchingRuleVo->jsonSerialize();
            }
        }

        return $obj;
    }
}
