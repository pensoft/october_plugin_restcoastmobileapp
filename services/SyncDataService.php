<?php namespace Pensoft\RestcoastMobileApp\Services;

use Illuminate\Support\Facades\Storage;
use Pensoft\RestcoastMobileApp\Models\SiteThreatImpactEntry;
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
                            'contentBlocks' => $this->convertContentBlocksData(
                                $outcome['content_blocks']
                            )
                        ];
                    }
                }

                $entryData = [
                    'data' => [
                        'code' => $threatDefinition->code,
                        'name' => $threatDefinition->name,
                        'image' => $threatDefinition->image,
                        'contentBlocks' => $this->convertContentBlocksData(
                            $threatImpactEntry->content_blocks
                        ),
                    ],
                    'measures' => $measuresData,
                    'measureCombinations' => $measureCombinationsData

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

    // TODO: Implements the rest of the endpoints

}
