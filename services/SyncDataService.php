<?php namespace Pensoft\RestcoastMobileApp\Services;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use October\Rain\Support\Facades\Config;
use Pensoft\RestcoastMobileApp\Controllers\AppSettings;
use Pensoft\RestcoastMobileApp\Controllers\MeasureDefinitions;
use Pensoft\RestcoastMobileApp\Controllers\Sites;
use Pensoft\RestcoastMobileApp\Controllers\SiteThreatImpactEntries;
use Pensoft\RestcoastMobileApp\Controllers\ThreatDefinitions;
use Pensoft\RestcoastMobileApp\Controllers\ThreatMeasureImpactEntries;
use Pensoft\RestcoastMobileApp\Models\Site;
use Pensoft\RestcoastMobileApp\Models\ThreatDefinition;
use RainLab\Translate\Models\Locale;

class SyncDataService
{
    protected $disk;

    private $assetsPathPrefix;

    public function __construct()
    {
        $this->disk = Storage::disk('gcs');
        $this->assetsPathPrefix = '/u/assets';
    }

    public function checkIfConfigured(): bool
    {
        try {
            $files = $this->disk->files();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param array $content
     * @param $fileName
     * @return void
     */
    public function uploadJson(array $content, $fileName)
    {
        if (!$this->checkIfConfigured()) {
            return false;
        }
        $jsonContent = json_encode($content);
        $options = [
            'name' => $fileName,
            'metadata' => [
                'contentType' => 'application/json'
            ],
        ];
        $this->disk->put($fileName, $jsonContent, $options);
    }

    /**
     * Uploads .json files (for each language) containing information
     * ('id', 'image', 'name', 'short_description') about all Threat Definitions.
     *
     * @return void
     */
    public function syncThreatsDefinitions()
    {
        $allThreatDefinitions = ThreatDefinition::query()
            ->select('id', 'image', 'name', 'short_description')
            ->get();
        // TODO: make sure Locale exists
        $languages = Locale::listAvailable();

        foreach ($languages as $lang => $label) {
            $threatsArray = [];
            foreach ($allThreatDefinitions as $threat) {

                // TODO: How to handle untranslated (empty) values in other languages.
                // TODO: Currently, the english values are used.
                $threat->translateContext($lang);
                $threatsArray[] = [
                    'id' => $threat->id,
                    'name' => $threat->name,
                    'image' => $threat->image,
                    'short_description' => $threat->short_description,
                ];
            }
            // fileName is the endpoint in the CDN
            $fileName = "l/" . $lang . "/threats.json";
            $this->uploadJson(
                $threatsArray,
                $fileName
            );
        }
    }

    public function syncSites()
    {
        $allSites = Site::query()
            ->select('id', 'content_blocks')
            ->get();
        foreach ($allSites as $site) {
            $contentBlocks = $this->convertContentBlocksData($site['content_blocks']);
            $contentBlocks = json_encode($contentBlocks);
        }

        // TODO: Sync all Sites data
    }

    /**
     * @param $contentBlocks
     * @return array
     */
    public function convertContentBlocksData($contentBlocks): array
    {
        $blocksData = [];
        foreach ($contentBlocks as $block) {
            $newBlock = [
                'type' => $block['_group']
            ];
            switch ($block['_group']) {
                case 'heading':
                {
                    $newBlock['text'] = $block['heading'];
                    break;
                }

                case 'youtube':
                {
                    $newBlock['videoId'] = $block['videoId'];
                    break;
                }

                case 'richText':
                {
                    $newBlock['content'] = $block['text'];
                    break;
                }

                case 'image':
                {
                    $newBlock['path'] = $block['image'];
                    break;
                }

                case 'video':
                {
                    $newBlock['path'] = $block['video'];
                    break;
                }

                case 'audio':
                {
                    $newBlock['path'] = $block['audio'];
                    break;
                }

                case 'map':
                {
                    $newBlock['path'] = $block['kml_file'];
                    $newBlock['styling'] = $block['styling'];
                    break;
                }

                case 'separator':
                {
                    break;
                }
            }
            $blocksData[] = $newBlock;
        }

        return $blocksData;
    }

    /**
     * @param $filePath
     * @param string $action
     * @param null $newFilePath
     * @return void
     * @throws FileNotFoundException
     */
    public function syncMediaFile(
        $filePath,
        string $action = 'upload',
        $newFilePath = null
    ) {
        $filePath = ltrim($filePath, '/');
        // Get the relative media folder path dynamically
        $mediaFolder = Config::get(
            'system.storage.media.folder',
            'media'
        ); // Default media folder
        $relativeFilePath = $mediaFolder . '/' . $filePath;
        $file = null;
        if ($action === 'upload') {
            $file = Storage::disk('local')->readStream($relativeFilePath);
        }
        $storagePath = '/storage/app/' . $relativeFilePath;
        // Construct the desired path format
        $bucketFilePath = $this->assetsPathPrefix . $storagePath;

        switch ($action) {
            case 'upload':
            {
                $this->disk->put($bucketFilePath, $file);
                break;
            }
            case 'delete':
            {
                if ($this->disk->exists($bucketFilePath)) {
                    $this->disk->delete($bucketFilePath);
                }
                break;
            }
            case 'rename':
            {
                if ($this->disk->exists($bucketFilePath)) {
                    $newFilePath = ltrim($newFilePath, '/');
                    // Find the file in the bucket, replace its name
                    // and move the file to the new location.
                    $newBucketFilePath = str_replace(
                        $filePath,
                        $newFilePath,
                        $bucketFilePath
                    );
                    $this->disk->move($bucketFilePath, $newBucketFilePath);
                }
                break;
            }
        }
    }

    /**
     * @param $widget
     * @return bool
     */
    public function shouldSyncWithBucket($widget): bool
    {
        $controller = $widget->getController();
        $controllersToSync = [
            Sites::class,
            MeasureDefinitions::class,
            ThreatDefinitions::class,
            SiteThreatImpactEntries::class,
            ThreatMeasureImpactEntries::class,
            AppSettings::class
        ];
        return in_array(get_class($controller), $controllersToSync);
    }

}
