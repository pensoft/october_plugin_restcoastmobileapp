<?php

namespace Pensoft\RestcoastMobileApp\Services;

use Media\Classes\MediaLibrary;
use ValidationException;

class ValidateDataService
{

    /**
     * @throws ValidationException
     */
    public function validateContentBlocks($contentBlocks)
    {
        $configPath = plugins_path(
            'pensoft/restcoastmobileapp/config/content_blocks.yaml'
        );
        $formConfig = \Yaml::parseFile($configPath);
        $fieldsToValidate = [
            'audio' => 'audio',
            'video' => 'video',
            'kml_file' => 'map',
            'styling' => 'map',
            'image' => 'image'
        ];

        // Iterate through each content block
        if (!empty($contentBlocks)) {
            foreach ($contentBlocks as $block) {
                if ($block['_group'] === 'youtube') {
                    if (strlen($block['videoId']) !== 11) {
                        throw new ValidationException(
                            [
                                'youtube' => '"Content Blocks -> "YouTube"
                                        - the length of the video ID should be exactly 11 symbols.'
                            ]
                        );
                    }
                }

                foreach ($fieldsToValidate as $field => $group) {
                    if (isset($block[$field])) {
                        $allowedExtensions = $formConfig[$group]['fields'][$field]['allowedExtensions'] ?? [];
                        $mediaPath = $block[$field];
                        // Check if the file exists in the media library
                        $mediaLibrary = MediaLibrary::instance();
                        $file = $mediaLibrary->findFile($mediaPath);
                        if (!empty($file)) {
                            $extension = pathinfo($file->path, PATHINFO_EXTENSION);
                            if (!in_array(strtolower($extension), $allowedExtensions)) {
                                throw new ValidationException(
                                    [
                                        $field => '"Content Blocks -> "' . $formConfig[$group]['name'] . '"
                                            - the uploaded file type is not allowed. Only ' . implode(', ',
                                                $allowedExtensions) . ' files are allowed.'
                                    ]
                                );
                            }
                        } else {
                            throw new ValidationException([
                                $field => 'The uploaded file was not found.'
                            ]);
                        }
                    }
                }
            }
        }
    }
}
