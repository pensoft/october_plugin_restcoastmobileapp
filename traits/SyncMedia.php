<?php

namespace Pensoft\RestcoastMobileApp\Traits;

use Illuminate\Support\Collection;

trait SyncMedia
{
    public function getAllMediaPaths(): Collection
    {
        $mediaPaths = collect();

        // 1. Flat media fields
        foreach ($this->getMediaPathFields() as $field) {
            $path = $this->{$field} ?? null;
            if ($path) {
                $mediaPaths->push($path);
            }
        }

        // 2. Grouped content block repeaters
        foreach ($this->getGroupedContentBlockRepeaters() as $repeaterField) {
            foreach ($this->{$repeaterField} ?? [] as $block) {
                if (isset($block['_group'])) {
                    $this->extractFromContentBlock($block, $mediaPaths);
                }
            }
        }

        // 3. Plain repeaters with known mediafinder fields
        foreach ($this->getPlainMediaRepeaters() as $config) {
            foreach ($this->{$config['repeater']} ?? [] as $item) {
                foreach ($config['fields'] as $field) {
                    if (!empty($item[$field])) {
                        $mediaPaths->push($item[$field]);
                    }
                }
            }
        }

        // 4. Nested grouped content blocks (e.g. outcomes > content_blocks)
        foreach ($this->getNestedContentBlockRepeaters() as $config) {
            $outerField = $config['repeater'];
            $innerField = $config['content_blocks'] ?? 'content_blocks';

            foreach ($this->{$outerField} ?? [] as $outer) {
                foreach ($outer[$innerField] ?? [] as $block) {
                    if (isset($block['_group'])) {
                        $this->extractFromContentBlock($block, $mediaPaths);
                    }
                }
            }
        }

        return $mediaPaths->filter()->unique();
    }

    protected function extractFromContentBlock(array $block, Collection &$mediaPaths): void
    {
        if (empty($block['_group'])) {
            return;
        }

        switch ($block['_group']) {
            case 'image':
                if (!empty($block['image'])) {
                    $mediaPaths->push($block['image']);
                }
                break;

            case 'video':
                if (!empty($block['video'])) {
                    $mediaPaths->push($block['video']);
                }
                break;

            case 'audio':
                if (!empty($block['audio'])) {
                    $mediaPaths->push($block['audio']);
                }
                break;

            case 'map':
                if (!empty($block['kml_file'])) {
                    $mediaPaths->push($block['kml_file']);
                }
                if (!empty($block['styling'])) {
                    $mediaPaths->push($block['styling']);
                }
                break;
        }
    }

    /**
     * Override in model to define mediafinder fields.
     */
    public function getMediaPathFields(): array
    {
        return [];
    }

    /**
     * Override in model to define repeaters with content blocks.
     */
    public function getGroupedContentBlockRepeaters(): array
    {
        return [];
    }

    /**
     * Override in model to define nested repeaters with content blocks.
     * Return array like: [['repeater' => 'outcomes', 'content_blocks' => 'content_blocks']]
     */
    public function getNestedContentBlockRepeaters(): array
    {
        return [];
    }

    /**
     * Override in models to return an array of repeaters with fixed media fields.
     */
    public function getPlainMediaRepeaters(): array
    {
        return [];
    }
}
