<?php 

namespace Vehicules;

class Vehicule {
    public string $marque;
    const TVA = "20%";

    function __construct(string $marque)
    {
        $this->marque = $marque;
    }
    public function setMarque(string $marque)
    {
        $this->marque = $marque;
    }
    public function getMarque(): string
    {
        return $this->marque;
    }

    function __toString()
    {
        return $this->marque;
    }
}