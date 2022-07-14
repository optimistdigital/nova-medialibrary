<?php

namespace Outl1ne\NovaMediaHub\Nova\Fields;

use Laravel\Nova\Fields\Field;
use Outl1ne\NovaMediaHub\MediaHub;

class MediaHubField extends Field
{
    public $component = 'media-hub-field';

    protected $defaultCollectionName = null;

    public function __construct($name, $attribute, $defaultCollectionName = null)
    {
        $this->name = $name;
        $this->attribute = $attribute;

        $this->withMeta([
            'multiple' => false,
            'user_can_create_collections' => MediaHub::userCanCreateCollections(),
            'defaultCollectionName' => $defaultCollectionName,
        ]);

        // TODO Add index field
        $this->hideFromIndex();
    }

    public function defaultCollection($defaultCollectionName = null)
    {
        return $this->withMeta(['defaultCollectionName' => $defaultCollectionName]);
    }

    public function multiple($multiple = true)
    {
        return $this->withMeta(['multiple' => $multiple]);
    }

    public function jsonSerialize(): array
    {
        $jsonSerialized = parent::jsonSerialize();
        $jsonSerialized['media'] = [];

        $value = json_decode($jsonSerialized['value'], true);
        if (is_array($value)) {
            $jsonSerialized['media'] = MediaHub::getMediaModel()::findMany($value)->keyBy('id')->toArray();
        } else if (!empty($value)) {
            $jsonSerialized['media'][$value] = MediaHub::getMediaModel()::find($value);
        }

        return $jsonSerialized;
    }
}
