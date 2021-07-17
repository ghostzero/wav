<?php
/**
 * @author  Nikita Kolosov <anexroid@gmail.com>
 * @license MIT License
 * @year    2016
 */

namespace GhostZero\Wav;

use GhostZero\Wav\Exception\UnknownGenerator;
use GhostZero\Wav\Generator\AcousticGuitar;
use GhostZero\Wav\Generator\Generator;
use GhostZero\Wav\Generator\Organ;
use GhostZero\Wav\Generator\Piano;

class GeneratorFactory
{
    /**
     * @return Piano
     */
    public static function getPianoGenerator()
    {
        return new Piano();
    }

    /**
     * @return AcousticGuitar
     */
    public static function getAcousticGuitarGenerator()
    {
        return new AcousticGuitar();
    }

    /**
     * @return Organ
     */
    public static function getOrganGenerator()
    {
        return new Organ();
    }

    /**
     * @param $name
     *
     * @return Generator
     * @throws UnknownGenerator
     */
    public static function getGenerator($name)
    {
        switch ($name) {
            case Piano::NAME:
                return self::getPianoGenerator();
            case AcousticGuitar::NAME:
                return self::getAcousticGuitarGenerator();
            case Organ::NAME:
                return self::getOrganGenerator();
        }

        throw new UnknownGenerator('Unknown generator "' . $name . '"');
    }
}