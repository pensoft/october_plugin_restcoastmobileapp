<?php namespace Pensoft\RestcoastMobileApp\Services;

use Config;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\Storage;
use Pensoft\RestcoastMobileApp\Controllers\AppSettings;
use Pensoft\RestcoastMobileApp\Controllers\MeasureDefinitions;
use Pensoft\RestcoastMobileApp\Controllers\Sites;
use Pensoft\RestcoastMobileApp\Controllers\SiteThreatImpactEntries;
use Pensoft\RestcoastMobileApp\Controllers\ThreatDefinitions;
use Pensoft\RestcoastMobileApp\Controllers\ThreatMeasureImpactEntries;
use Pensoft\RestcoastMobileApp\Models\AppSettings as AppSettingsModel;
use Pensoft\RestcoastMobileApp\Models\Site;
use Pensoft\RestcoastMobileApp\Models\SiteThreatImpactEntry;
use Pensoft\RestcoastMobileApp\Models\ThreatDefinition;
use RainLab\Translate\Models\Locale;

class SyncDataService
{
    protected $disk;
    private const ASSETS_PATH = '/u/assets';

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
     * @return void|bool
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
     * @param $asset
     * @return string|null
     */
    private function assetPath($asset): ?string
    {
        if (empty($asset)) {
            return null;
        }
        return self::ASSETS_PATH . $asset;
    }

    /**
     * @return void
     */
    public function syncAppSettings()
    {
        $sites = Site::query()
            ->select(
                'id',
                'name',
                'lat',
                'long',
                'country',
                'country_codes',
                'scale',
                'image_gallery'
            )
            ->get();
        $threats = ThreatDefinition::query()
            ->select('id', 'name', 'code', 'image', 'short_description')
            ->get();

        $languages = Locale::listAvailable();

        foreach ($languages as $lang => $label) {
            $sitesArray = [];
            $threatsArray = [];
            foreach ($sites as $site) {
                $site->translateContext($lang);
                $imageGallery = [];
                $countryCodes = [];
                if (!empty($site->image_gallery)) {
                    $imageGallery = array_map(function ($item) {
                        return $this->assetPath($item['image']);
                    }, $site->image_gallery);
                }
                if (!empty($site->country_codes)) {
                    $countryCodes = array_map(function ($item) {
                        return $item['code'];
                    }, $site->country_codes);
                }
                $sitesArray[] = [
                    'id' => $site->id,
                    'name' => $site->name,
                    'coordinates' => [
                        'lat' => $site->lat,
                        'long' => $site->long,
                    ],
                    'location' => $site->country,
                    'countryCodes' => $countryCodes,
                    'scale' => $site->scale,
                    'imageGallery' => $imageGallery
                ];
            }
            foreach ($threats as $threat) {
                $threat->translateContext($lang);
                $threatsArray[] = [
                    'name' => $threat->name,
                    'code' => $threat->code,
                    'thumbnail' => $this->assetPath($threat->image),
                    'definition' => $threat->short_description,
                ];
            }
            $homeData = [
                'data' => [
                    'countriesLayer' => $this->assetPath(
                        AppSettingsModel::get('home_map_kml_layer')
                    ),
                    'mapStyle' => $this->assetPath(
                        AppSettingsModel::get('home_map_style')
                    ),
                    'sites' => $sitesArray,
                    'threats' => $threatsArray
                ]
            ];

            // fileName is the endpoint in the CDN
            $fileName = "l/" . $lang . "/home.json";
            $this->uploadJson(
                $homeData,
                $fileName
            );
        }
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
        $languages = Locale::listAvailable();

        foreach ($languages as $lang => $label) {
            $threatsArray = [];
            foreach ($allThreatDefinitions as $threat) {
                $threat->translateContext($lang);
                $threatsArray[] = [
                    'id' => $threat->id,
                    'name' => $threat->name,
                    'image' => $this->assetPath($threat->image),
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

    /**
     * @return void
     */
    public function syncSites()
    {
        $sites = Site::query()
            ->select(
                'id',
                'name',
                'country',
                'content_blocks',
                'stakeholders',
                'country_codes',
                'image_gallery',
                'scale'
            )
            ->with('threat_impact_entries')
            ->get();
        $languages = Locale::listAvailable();
        foreach ($languages as $lang => $label) {
            foreach ($sites as $site) {
                $site->translateContext($lang);
                $threats = [];
                if (!empty($site->threat_impact_entries)) {
                    foreach ($site->threat_impact_entries as $threatEntry) {
                        $threat = ThreatDefinition::find(
                            $threatEntry->threat_definition_id
                        );
                        $threat->translateContext($lang);
                        if (!empty($threat)) {
                            $threats[] = [
                                'threatImpactId' => $threatEntry->id,
                                'code' => $threat->code,
                                'name' => $threat->name,
                                'thumbnail' => $this->assetPath($threat->image),
                                'definition' => $threat->short_description,
                            ];
                        }
                    }
                }

                $imageGallery = [];
                if (!empty($site->image_gallery)) {
                    $imageGallery = array_map(function ($item) {
                        return $this->assetPath($item['image']);
                    }, $site->image_gallery);
                }

                $countryCodes = [];
                if (!empty($site->country_codes)) {
                    $countryCodes = array_map(function ($item) {
                        return $item['code'];
                    }, $site->country_codes);
                }

                $stakeholders = [];
                if (!empty($site->stakeholders)) {
                    $stakeholders = array_map(function ($stakeholder) {
                        $stakeholder['image'] = $this->assetPath(
                            $stakeholder['image']
                        );
                        return $stakeholder;
                    }, $site->stakeholders);
                }

                $siteData = [
                    'data' => [
                        'id' => $site->id,
                        'name' => $site->name,
                        'countries' => $site->country,
                        'countryCodes' => $countryCodes,
                        'imageGallery' => $imageGallery,
                        'stakeholders' => $stakeholders,
                        'contentBlocks' => !empty($site->content_blocks) ?
                            $this->convertContentBlocksData(
                                $site->content_blocks
                            ) : [],
                        'threats' => $threats
                    ]
                ];

                // fileName is the endpoint in the CDN
                $fileName = "l/" . $lang . "/site/" . $site->id . ".json";
                $this->uploadJson(
                    $siteData,
                    $fileName
                );
            }
        }
    }


    /**
     * @return void
     */
    public function syncThreatImpactEntries()
    {
        $allThreatImpactEntries = SiteThreatImpactEntry::query()
            ->select(
                'id',
                'name',
                'outcomes',
                'content_blocks',
                'site_id',
                'threat_definition_id'
            )
            ->with('threat_definition', 'site')
            ->get();

        $languages = Locale::listAvailable();
        foreach ($languages as $lang => $label) {
            foreach ($allThreatImpactEntries as $threatImpactEntry) {
                $threatImpactEntry->translateContext($lang);

                $threatDefinition = $threatImpactEntry->threat_definition;
                $threatDefinition->translateContext($lang);

                $measureImpactEntries = $threatImpactEntry->measure_impact_entries;
                $measuresData = [];
                $measureCombinationsData = [];
                if (!empty($measureImpactEntries)) {
                    foreach ($measureImpactEntries as $measureImpactEntry) {
                        $measureImpactEntry->translateContext($lang);
                        $measureDefinition = $measureImpactEntry->measure_definition;
                        $measureDefinition->translateContext($lang);
                        $measuresData[] = [
                            'id' => $measureImpactEntry->id,
                            'name' => $measureDefinition->name,
                            'description' => $measureDefinition->short_description,
                        ];
                    }
                }
                $outcomes = $threatImpactEntry->outcomes;
                if (!empty($outcomes)) {
                    foreach ($outcomes as $outcome) {
                        $measureCombinationsData[] = [
                            'measures' => array_values($outcome['measures']),
                            'economicScore' => $outcome['economic_score'],
                            'environmentalScore' => $outcome['environmental_score'],
                            'contentBlocks' => !empty($outcome['content_blocks']) ?
                                $this->convertContentBlocksData(
                                    $outcome['content_blocks']
                                ) : [],
                        ];
                    }
                }

                $entryData = [
                    'data' => [
                        'code' => $threatDefinition->code,
                        'name' => $threatDefinition->name,
                        'image' => $this->assetPath($threatDefinition->image),
                        'contentBlocks' => !empty($threatImpactEntry->content_blocks) ?
                            $this->convertContentBlocksData(
                                $threatImpactEntry->content_blocks
                            ) : [],
                        'measures' => $measuresData,
                        'measureCombinations' => $measureCombinationsData
                    ],
                ];

                // fileName is the endpoint in the CDN
                $fileName = "l/" . $lang . "/site/" . $threatImpactEntry->site->id . "/threat/" . $threatDefinition->code . ".json";
                $this->uploadJson(
                    $entryData,
                    $fileName
                );

            }
        }
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
                    $newBlock['path'] = $this->assetPath($block['image']);
                    break;
                }

                case 'video':
                {
                    $newBlock['path'] = $this->assetPath($block['video']);
                    break;
                }

                case 'audio':
                {
                    $newBlock['path'] = $this->assetPath($block['audio']);
                    break;
                }

                case 'map':
                {
                    $newBlock['path'] = $this->assetPath($block['kml_file']);
                    $newBlock['styling'] = $this->assetPath($block['styling']);
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
        // Construct the desired path format
        $bucketFilePath = self::ASSETS_PATH . '/' . $filePath;

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
        if (!$this->checkIfConfigured()) {
            return false;
        }
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
