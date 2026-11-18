<?php

namespace iutnc\touiteur\touite;

class Tag {

    protected string $text;

    public function __construct(string $txt)
    {
        $this->text = $txt;
    }

    public function __get(string $at)
    {
        if (property_exists($this, $at)) return $this->$at;
        throw new \Exception ("invalid property : $at");
    }

    public function __toString() : string
    {
        return json_encode(get_object_vars($this));
    }

}