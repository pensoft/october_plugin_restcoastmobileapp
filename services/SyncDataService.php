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
use Pensoft\RestcoastMobileApp\Models\ThreatMeasureImpactEntry;
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
     *
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
     * @param $fileName
     *
     * @return false|void
     */
    public function deleteJson($fileName)
    {
        if (!$this->checkIfConfigured()) {
            return false;
        }
        if ($this->disk->exists($fileName)) {
            $this->disk->delete($fileName);
        }
    }

    /**
     * @param $asset
     *
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
        $appSettings = AppSettingsModel::first();
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
                    'id' => $threat->id,
                    'name' => $threat->name,
                    'code' => $threat->code,
                    'thumbnail' => $this->assetPath($threat->image),
                    'definition' => $threat->short_description,
                ];
            }
            $homeData = [
                'data' => [
                    'sites' => $sitesArray,
                    'threats' => $threatsArray,
                    'countriesLayer' => !empty($appSettings) ?
                        $this->assetPath($appSettings->home_map_kml_layer) : null,
                    'mapStyle' => !empty($appSettings) ?
                        $this->assetPath($appSettings->home_map_style) : null
                ]
            ];

            if (!empty($appSettings)) {
                $appSettings->translateContext($lang);
            }

            $this->uploadJson(
                [
                    'data' => [
                        'privacyPolicy' => !empty($appSettings) ? $appSettings->privacy_policy : null
                    ]
                ],
                'l/' . $lang . '/privacy-policy.json'
            );
            $this->uploadJson(
                [
                    'data' => [
                        'aboutUs' => !empty($appSettings) ? $appSettings->about : null
                    ]
                ],
                'l/' . $lang . '/about-us.json'
            );
            $this->uploadJson(
                [
                    'data' => [
                        'euDisclaimer' => !empty($appSettings) ? $appSettings->eu_disclaimer : null
                    ]
                ],
                'l/' . $lang . '/eu-disclaimer.json'
            );
            $this->uploadJson(
                $homeData,
                "l/" . $lang . "/home.json"
            );
        }

        // The social links don't have translations,
        // so upload them after looping the languages
        $this->uploadJson(
            [
                'data' => [
                    'socials' => [
                        'facebook' => !empty($appSettings) ? $appSettings->facebook_url : null,
                        'youtube' => !empty($appSettings) ? $appSettings->youtube_url : null,
                        'instagram' => !empty($appSettings) ? $appSettings->instagram_url : null,
                        'x' => !empty($appSettings) ? $appSettings->x_url : null,
                        'linkedin' => !empty($appSettings) ? $appSettings->linkedin_url : null,
                        'website' => !empty($appSettings) ? $appSettings->website_url : null
                    ],
                ]
            ],
            'u/find-us.json'
        );
    }

    /**
     * @param int|null $threatDefinitionId
     * @return void
     */
    public function syncThreatDefinitions(int $threatDefinitionId = null)
    {
        $threatDefinitions = ThreatDefinition::query()
            ->select(
                'id',
                'image',
                'name',
                'code',
                'short_description',
                'content_blocks',
                'outcome_name',
                'outcome_image',
                'base_outcome'
            );
        if (!empty($threatDefinitionId)) {
            $threatDefinitions->where(
                'id',
                '=',
                $threatDefinitionId
            );
        }
        $threatDefinitions = $threatDefinitions->get();
        $languages = Locale::listAvailable();

        foreach ($languages as $lang => $label) {
            foreach ($threatDefinitions as $threatDefinition) {
                $sites = [];
                $threatDefinitionSites = Site::query()
                    ->select(
                        'rcm_sites.id',
                        'rcm_sites.name',
                        'rcm_sites.image'
                    )
                    ->join(
                        'rcm_site_threat_impact_entries as threat_impact_entries',
                        'rcm_sites.id',
                        '=',
                        'threat_impact_entries.site_id'
                    )
                    ->join(
                        'rcm_threat_definitions as threat_definitions',
                        'threat_impact_entries.threat_definition_id',
                        '=',
                        'threat_definitions.id'
                    )
                    ->where(
                        'threat_definitions.id',
                        '=',
                        $threatDefinition->id
                    )
                    ->groupBy('rcm_sites.id')
                    ->get()
                    ->map(function ($site) use ($lang, &$sites) {
                        $site->translateContext($lang);
                        $sites[] = [
                            'id' => $site->id,
                            'image' => $this->assetPath($site->image),
                            'name' => $site->name,
                        ];
                        return $site;
                    });

                $threatDefinition->translateContext($lang);
                $threatDefinitionsArray = [
                    'data' => [
                        'id' => $threatDefinition->id,
                        'code' => $threatDefinition->code,
                        'name' => $threatDefinition->name,
                        'image' => $this->assetPath($threatDefinition->image),
                        'contentBlocks' => !empty($threatDefinition->content_blocks) ?
                            $this->convertContentBlocksData(
                                $threatDefinition->content_blocks
                            ) : [],
                        'sites' => $sites,
                        'outcome' => !empty($threatDefinition->base_outcome)
                    ]
                ];
                $fileName = "l/" . $lang . "/threat-definition/" . $threatDefinition->id . ".json";
                $this->uploadJson(
                    $threatDefinitionsArray,
                    $fileName
                );

                // Outcome file
                $outcomeData = [
                    'data' => [
                        'name' => $threatDefinition->outcome_name,
                        'image' => $this->assetPath(
                            $threatDefinition->outcome_image
                        ),
                        'contentBlocks' => !empty($threatDefinition->base_outcome) ?
                            $this->convertContentBlocksData(
                                $threatDefinition->base_outcome
                            ) : [],

                    ]
                ];
                $outcomeFileName = "l/" . $lang . "/threat-definition/" . $threatDefinition->id . "/base_outcome.json";
                $this->uploadJson(
                    $outcomeData,
                    $outcomeFileName
                );
            }
        }
    }

    /**
     * @param int|null $siteId
     * @return void
     */
    public function syncSites(int $siteId = null)
    {
        $sites = Site::query()
            ->select(
                'id',
                'name',
                'country',
                'content_blocks',
                'lat',
                'long',
                'stakeholders',
                'country_codes',
                'image_gallery',
                'scale'
            )
            ->with('threat_impact_entries');
        if (!empty($siteId)) {
            $sites->where(
                'id',
                '=',
                $siteId
            );
        }
        $sites = $sites->get();

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
                        'coordinates' => [
                            'lat' => $site->lat,
                            'long' => $site->long,
                        ],
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
     * @param int $siteId
     *
     * @return void
     */
    public function deleteSite(int $siteId)
    {
        $languages = Locale::listAvailable();
        foreach ($languages as $lang => $label) {
            // fileName is the endpoint in the CDN
            $fileName = "l/" . $lang . "/site/" . $siteId . ".json";
            $this->deleteJson($fileName);
        }
    }


    /**
     * @param int $siteThreatImpactEntryId
     * @param int $siteId
     * @return void
     */
    public function deleteSiteThreatImpactEntry(
        int $siteThreatImpactEntryId,
        int $siteId
    ) {
        $languages = Locale::listAvailable();
        foreach ($languages as $lang => $label) {
            $fileName = "l/" . $lang . "/site/" . $siteId . "/threat/" . $siteThreatImpactEntryId . ".json";
            $this->deleteJson($fileName);
        }
    }

    /**
     * @param int $threatMeasureImpactEntryId
     * @param int $siteThreatImpactEntryId
     * @param int $siteId
     * @return void
     */
    public function deleteThreatMeasureImpactEntry(
        int $threatMeasureImpactEntryId,
        int $siteThreatImpactEntryId,
        int $siteId
    ) {
        $languages = Locale::listAvailable();
        foreach ($languages as $lang => $label) {
            $fileName = sprintf(
                'l/%s/site/%d/threat/%d/measure/%d.json',
                $lang,
                $siteId,
                $siteThreatImpactEntryId,
                $threatMeasureImpactEntryId
            );
            $this->deleteJson($fileName);
        }
    }

    /**
     * @return void
     */
    public function syncThreatImpactEntries(
        int $siteThreatImpactEntryId = null
    ) {
        $siteThreatImpactEntries = SiteThreatImpactEntry::query()
            ->select(
                'id',
                'name',
                'outcomes',
                'content_blocks',
                'site_id',
                'threat_definition_id'
            )
            ->with('threat_definition', 'site');
        if (!empty($siteThreatImpactEntry)) {
            $siteThreatImpactEntries->where(
                'id',
                '=',
                $siteThreatImpactEntryId
            );
        }
        $siteThreatImpactEntries = $siteThreatImpactEntries->get();
        $languages = Locale::listAvailable();
        foreach ($languages as $lang => $label) {
            foreach ($siteThreatImpactEntries as $threatImpactEntry) {

                // If there is no Site assigned to this entry, skip it
                if (empty($threatImpactEntry->site)) {
                    continue;
                }
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
                    foreach ($outcomes as $outcomeIndex => $outcome) {
                        $selectedMeasuresIds = array_map(function ($measure) {
                            return $measure;
                        }, $outcome['measures']);

                        $selectedMeasuresObject = array_map(function ($measureId) {
                            $measureObject = ThreatMeasureImpactEntry::query()
                                ->where('id', '=', $measureId)
                                ->select('name', 'measure_definition_id')
                                ->with('measure_definition')
                                ->first();

                            return [
                                'id' => $measureId,
                                'name' => $measureObject->measure_definition->name
                            ];
                        }, $outcome['measures']);

                        $measureCombinationsData[] = $selectedMeasuresIds;
                        $outcomeData = [
                            'data' => [
                                'measures' => $selectedMeasuresObject,
                                'scores' => $outcome['scores'] ?? [],
                                'contentBlocks' => !empty($outcome['content_blocks']) ?
                                    $this->convertContentBlocksData(
                                        $outcome['content_blocks']
                                    ) : [],
                            ]
                        ];
                        $outcomeFileName = sprintf(
                            'l/%s/site/%d/threat/%d/outcome/%d.json',
                            $lang,
                            $threatImpactEntry->site->id,
                            $threatImpactEntry->id,
                            $outcomeIndex
                        );
                        $this->uploadJson(
                            $outcomeData,
                            $outcomeFileName
                        );
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
                $fileName = sprintf(
                    'l/%s/site/%d/threat/%d.json',
                    $lang,
                    $threatImpactEntry->site->id,
                    $threatImpactEntry->id
                );
                $this->uploadJson(
                    $entryData,
                    $fileName
                );
            }
        }
    }

    /**
     * @return void
     */
    public function syncMeasureImpactEntries()
    {
        $measureImpactEntries = ThreatMeasureImpactEntry::query()
            ->select(
                'id',
                'name',
                'content_blocks',
                'site_threat_impact_id',
                'measure_definition_id'
            )
            ->with('site_threat_impact', 'measure_definition')
            ->get();

        $languages = Locale::listAvailable();
        foreach ($languages as $lang => $label) {
            foreach ($measureImpactEntries as $measureImpactEntry) {
                $measureImpactEntry->translateContext($lang);
                $measureData = [
                    'data' => [
                        'id' => $measureImpactEntry->id,
                        'name' => $measureImpactEntry->measure_definition->name,
                        'contentBlocks' => !empty($measureImpactEntry->content_blocks) ?
                            $this->convertContentBlocksData(
                                $measureImpactEntry->content_blocks
                            ) : [],
                    ]
                ];
                $fileName = sprintf(
                    'l/%s/site/%d/threat/%d/measure/%d.json',
                    $lang,
                    $measureImpactEntry->site_threat_impact->site->id,
                    $measureImpactEntry->site_threat_impact->id,
                    $measureImpactEntry->id
                );
                $this->uploadJson(
                    $measureData,
                    $fileName
                );
            }

        }
    }

    /**
     * @param $contentBlocks
     *
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
     *
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
     *
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
