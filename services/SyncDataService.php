<?php namespace Pensoft\RestcoastMobileApp\Services;

use Illuminate\Support\Facades\Storage;
use Pensoft\RestcoastMobileApp\Models\Site;
use Pensoft\RestcoastMobileApp\Models\ThreatDefinition;
use RainLab\Translate\Models\Locale;

class SyncDataService
{
    protected $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('gcs');
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
                case 'heading': {
                    $newBlock['text'] = $block['heading'];
                    break;
                }

                case 'youtube': {
                    $newBlock['videoId'] = $block['videoId'];
                    break;
                }

                case 'richText': {
                    $newBlock['content'] = $block['text'];
                    break;
                }

                case 'image': {
                    $newBlock['path'] = $block['image'];
                    break;
                }

                case 'video': {
                    $newBlock['path'] = $block['video'];
                    break;
                }

                case 'audio': {
                    $newBlock['path'] = $block['audio'];
                    break;
                }

                case 'map': {
                    $newBlock['path'] = $block['kml_file'];
                    $newBlock['styling'] = $block['styling'];
                    break;
                }
            }
            $blocksData[] = $newBlock;
        }

        return $blocksData;
    }

    // TODO: Implements the rest of the endpoints

}
