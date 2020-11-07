<?php

namespace App\Http\Resolvers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class Artist implements Resolver
{
    public $id;
    public $name;
    public $members;

    public static function getArtist($needle)
    {
        $fileContents = Storage::disk()->get('fakedata.json');
        $data = json_decode($fileContents, true);
        if (!empty($data)) {
            foreach($data['artists'] as $k => $value) {
                if ($value['id'] === $needle) {
                    return $value;
                }
            }
        }
        return [];
    }

    public function resolve($artist, $args, $context, $info)
    {
        $selectionSet = $info->getFieldSelection(1);
        foreach($selectionSet['artist'] as $fieldName => $val) {
            $method = 'resolve' . ucfirst($fieldName);
            if (method_exists($this, $method)) {
                $this->{$fieldName} = $this->{$method}($artist, $args, $context, $info);
            } else {
                if (property_exists($this, $fieldName)) {
                    $this->{$fieldName} = $artist->{$fieldName};
                }
            }
        }

        return $this;
    }
}
