<?php

namespace iutnc\touiteur\touite;

class Touite {

    private string $text;
    private string $auteur;
    private string $date;
    private array $tags;
    private string $image;

    public function __construct(string $txt, string $a, string $d, array $t, string $img = "")
    {
        $this->text = $txt;
        $this->auteur = $a;
        $this->date = $d;
        $this->tags = $t;
        $this->image = $img;
    }

    public function __get(string $at) : mixed
    {
        if (property_exists($this, $at)) return $this->$at;
        throw new \Exception ("invalid property : $at");
    }

    public function __toString() : string
    {
        return json_encode(get_object_vars($this));
    }

    
}