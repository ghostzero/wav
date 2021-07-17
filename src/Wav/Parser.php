<?php
/**
 * @author  Nikita Kolosov <anexroid@gmail.com>
 * @author  René Preuß <rene@preuss.io>
 * @license MIT License
 * @year    2016
 */

namespace GhostZero\Wav;

use GhostZero\Binary\Helper;
use GhostZero\Wav\Exception\FileIsNotExistsException;
use GhostZero\Wav\Exception\FileIsNotReadableException;
use GhostZero\Wav\Exception\FileIsNotWavFileException;
use GhostZero\Wav\Exception\InvalidWavDataException;
use GhostZero\Wav\File\DataSection;
use GhostZero\Wav\File\FormatSection;
use GhostZero\Wav\File\Header;

class Parser
{
    /**
     * @param string $filename path to wav-file
     *
     * @return AudioFile
     * @throws FileIsNotExistsException
     * @throws FileIsNotReadableException
     * @throws FileIsNotWavFileException
     * @throws InvalidWavDataException
     */
    public static function fromFile(string $filename): AudioFile
    {
        if (!file_exists($filename)) {
            throw new FileIsNotExistsException('File "' . $filename . '" is not exists.');
        }

        if (!is_readable($filename)) {
            throw new FileIsNotReadableException('File "' . $filename . '" is not readable"');
        }

        if (is_dir($filename)) {
            throw new FileIsNotWavFileException('File "' . $filename . '" is not a wav-file');
        }

        $size = filesize($filename);
        if ($size < AudioFile::HEADER_LENGTH) {
            throw new FileIsNotWavFileException('File "' . $filename . '" is not a wav-file');
        }

        $handle = fopen($filename, 'rb');

        return self::fromStream($handle);
    }

    /**
     * @param resource $handle resource to wav-file
     *
     * @return AudioFile
     * @throws InvalidWavDataException
     */
    public static function fromStream($handle): AudioFile
    {
        try {
            $header = Header::createFromArray(self::parseHeader($handle));
            $formatSection = FormatSection::createFromArray(self::parseFormatSection($handle));
            $dataSection = DataSection::createFromArray(self::parseDataSection($handle));
        } finally {
            fclose($handle);
        }

        return new AudioFile($header, $formatSection, $dataSection);
    }

    /**
     * @param resource $handle
     * @return array
     */
    protected static function parseHeader($handle): array
    {
        return [
            'id' => Helper::readString($handle, 4),
            'size' => Helper::readLong($handle),
            'format' => Helper::readString($handle, 4),
        ];
    }

    /**
     * @param resource $handle
     * @return array
     */
    protected static function parseFormatSection($handle): array
    {
        return [
            'id' => Helper::readString($handle, 4),
            'size' => Helper::readLong($handle),
            'audioFormat' => Helper::readWord($handle),
            'numberOfChannels' => Helper::readWord($handle),
            'sampleRate' => Helper::readLong($handle),
            'byteRate' => Helper::readLong($handle),
            'blockAlign' => Helper::readWord($handle),
            'bitsPerSample' => Helper::readWord($handle),
        ];
    }

    /**
     * @param resource $handle
     *
     * @return array
     */
    protected static function parseDataSection($handle): array
    {
        $data = [
            'id' => Helper::readString($handle, 4),
            'size' => Helper::readLong($handle),
        ];

        if ($data['size'] > 0) {
            $data['raw'] = fread($handle, $data['size']);
        }

        return $data;
    }
}