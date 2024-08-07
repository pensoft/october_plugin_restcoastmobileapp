<?php

namespace Pensoft\RestcoastMobileApp\Extensions;

use October\Rain\Extension\ExtensionBase;

class JsonableModel extends ExtensionBase
{
    /**
     * @var \October\Rain\Database\Model Reference to the extended model.
     */
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function generateJson()
    {
        return [
            "data" => $this->model->attributesToArray()
        ];
    }
}
