<?php

namespace iutnc\touiteur\model;

use iutnc\touiteur\exception\InvalidPropertyNameException;

class Touite
{
    private string $date;

    public function __construct(
        private string $pseudo,
        private string $texte,
        private array $tags = [],
        private ?string $image = null
    ) {
        $this->date = date('Y-m-d H:i:s');
    }

    public function __get(string $attribut): mixed
    {
        if (property_exists($this, $attribut)) {
            return $this->$attribut;
        }
        throw new InvalidPropertyNameException("$attribut : propriété invalide");
    }
}
