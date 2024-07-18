<?php namespace Pensoft\Restcoast\Services;

use Google\Cloud\Core\Exception\GoogleException;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Support\Facades\Storage;

class JsonUploader {
    protected $storageClient;
    protected $bucketName;

    /**
     * @throws GoogleException
     */
    public function __construct() {
        $keyPath = Storage::path("gcp-key.json");
        $this->storageClient = new StorageClient( [
            'keyFilePath' => $keyPath, // Path to your service account key file
        ] );
        $this->bucketName    = 'restcoast-dev-bucket'; // Replace with your bucket name
    }

    public function uploadJson( $jsonContent, $fileName ) {
        $bucket = $this->storageClient->bucket( $this->bucketName );
        $object = $bucket->upload( $jsonContent, [
            'name'     => $fileName,
            'metadata' => [ 'contentType' => 'application/json' ],
        ] );

        return $object->info();
    }
}
