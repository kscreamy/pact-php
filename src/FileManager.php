<?php
namespace PhpPact;

use PhpPact\Mocks\MockHttpService\Mappers\ProviderServicePactMapper;
use PhpPact\Mocks\MockHttpService\Models\ProviderServicePactFile;

/**
 * Class FileManager
 * @package PhpPact
 */
class FileManager
{
    /**
     * @var string
     */
    private $directory;

    /**
     * FileManager constructor.
     * @param $directory
     */
    public function __construct($directory)
    {
        $this->directory = $directory;
    }

    public function getPactFilePath($consumerName, $providerName, $version)
    {
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0777, true);
        }

        $this->directory = realpath($this->directory);

        return sprintf('%s/pact%s.%s.%s.json',
            $this->directory,
            $consumerName,
            $providerName,
            $version);
    }

    public function saveFile(ProviderServicePactFile $file, $path)
    {
        $jsonPact = \json_encode(
            $file,
            JSON_PRETTY_PRINT
        );

        file_put_contents(
            $path,
            $jsonPact
        );
    }

    public function loadPactFile($path)
    {
        $json = \json_decode(file_get_contents($path));
        return (new ProviderServicePactMapper())
            ->convert($json);
    }
}

