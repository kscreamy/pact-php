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
    public function getPactFilePath($consumerName, $providerName, $version)
    {
        return sprintf('%s/pact%s.%s.%s.json',
            sys_get_temp_dir(),
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

