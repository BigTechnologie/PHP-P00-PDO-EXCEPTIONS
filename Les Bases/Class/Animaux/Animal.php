<?php

namespace Animaux;

abstract class Animal implements EtreVivant {
    protected int $faim = 0;
    protected int $fatigue = 0;
}