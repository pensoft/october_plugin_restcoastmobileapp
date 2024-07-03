<?php namespace Pensoft\RestcoastMobileApp\Services;

use Illuminate\Support\Facades\Storage;
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
        // TODO: Sync all Sites data
    }

    // TODO: Implements the rest of the endpoints

}
