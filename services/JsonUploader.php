<?php namespace Pensoft\Restcoast\Services;


use Illuminate\Support\Facades\Storage;

class JsonUploader
{

    protected $bucket;
    protected $adapter;

    public function __construct()
    {
        $this->adapter = Storage::disk('gcs')->getAdapter();
        $this->bucket = $this->adapter->getBucket();
    }

    /**
     * @param $jsonContent
     * @param $fileName
     * @return void
     */
    public function uploadJson($jsonContent, $fileName)
    {
        $options = [
            'name' => $fileName,
            'metadata' => [
                'contentType' => 'application/json'
            ],
        ];
        $bucketName = $this->bucket->name();
        $this->bucket->upload($jsonContent, $options);
    }

//    public function fetchFiles()
//    {
//        $data = $this->adapter->read('anothertest.json');
//        echo '<pre>';
//        print_r($data);
//
//    }
}
