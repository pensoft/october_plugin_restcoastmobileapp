<?php

namespace Pensoft\RestcoastMobileApp\Models;

trait JsonableFieldsHandler
{
    /**
     * Process JSON fields before saving.
     */
    public function processJsonFields()
    {
        if (property_exists($this, 'jsonable') && is_array($this->jsonable)) {
            foreach ($this->jsonable as $jsonField) {
                if (empty($this->{$jsonField}) || !is_array($this->{$jsonField})) {
                    $this->{$jsonField} = null; // Set to null if not valid
                }
            }
        }
    }

    /**
     * Hook into the model's beforeSave event.
     */
    public function beforeSave()
    {
        $this->processJsonFields();
    }
}
