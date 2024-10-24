<?php

namespace District5\S4S\Slim4\Enum;

enum Environment
{
    case Local;
    case Build;
    case Development;
    case Runway;
    case Staging;
    case Production;
}
