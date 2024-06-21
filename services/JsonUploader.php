<?php namespace Pensoft\Restcoast\Services;

use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Storage\StorageClient;

class JsonUploader
{
    protected $storageClient;
    protected $bucketName;

    /**
     * @throws GoogleException
     */
    public function __construct()
    {
        $this->storageClient = new StorageClient([
            'keyFilePath' => __DIR__ . '/gcp-key.json', // Path to your service account key file
        ]);
        $this->bucketName = 'restcoast-dev-bucket'; // Replace with your bucket name
    }

    public function generateJson()
    {
        // Your logic to generate the JSON data
        $data = [
            'example' => 'This is a JSON example.',
            'timestamp' => now()->toDateTimeString(),
        ];

        return json_encode($data, JSON_PRETTY_PRINT);
    }

    public function uploadJson($jsonContent, $fileName)
    {
        $bucket = $this->storageClient->bucket($this->bucketName);
        $object = $bucket->upload($jsonContent, [
            'name' => $fileName,
            'metadata' => ['contentType' => 'application/json'],
        ]);

        return $object->info();
    }
}
