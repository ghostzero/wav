<?php
/**
 * @author  Nikita Kolosov <anexroid@gmail.com>
 * @author  René Preuß <rene@preuss.io>
 * @license MIT License
 * @year    2016
 */

namespace GhostZero\Wav;

use GhostZero\Wav\File\DataSection;
use GhostZero\Wav\File\FormatSection;
use GhostZero\Wav\File\Header;

class Builder
{
    const DEFAULT_SAMPLE_RATE = 44100;

    const DEFAULT_VOLUME = 32768;

    /**
     * @var int
     */
    protected $audioFormat;

    /**
     * @var int
     */
    protected $numberOfChannels;

    /**
     * @var int
     */
    protected $sampleRate;

    /**
     * @var int
     */
    protected $byteRate;

    /**
     * @var int
     */
    protected $blockAlign;

    /**
     * @var int
     */
    protected $bitsPerSample;

    /**
     * @var Sample[]
     */
    protected $samples;

    /**
     * @param int $audioFormat
     *
     * @return $this
     */
    public function setAudioFormat($audioFormat = WaveFormat::PCM)
    {
        $this->audioFormat = $audioFormat;

        return $this;
    }

    /**
     * @param int $numberOfChannels
     *
     * @return $this
     */
    public function setNumberOfChannels($numberOfChannels = 1)
    {
        $this->numberOfChannels = $numberOfChannels;

        return $this;
    }

    /**
     * @param int $sampleRate
     *
     * @return $this
     */
    public function setSampleRate($sampleRate)
    {
        $this->sampleRate = $sampleRate;

        return $this;
    }

    /**
     * @param int $byteRate
     *
     * @return $this
     */
    public function setByteRate($byteRate)
    {
        $this->byteRate = $byteRate;

        return $this;
    }

    /**
     * @param int $blockAlign
     *
     * @return $this
     */
    public function setBlockAlign($blockAlign)
    {
        $this->blockAlign = $blockAlign;

        return $this;
    }

    /**
     * @param int $bitsPerSample
     *
     * @return $this
     */
    public function setBitsPerSample($bitsPerSample)
    {
        $this->bitsPerSample = $bitsPerSample;

        return $this;
    }

    /**
     * @param Sample[] $samples
     *
     * @return $this
     */
    public function setSamples(array $samples)
    {
        $this->samples = $samples;

        return $this;
    }

    /**
     * @return AudioFile
     */
    public function build()
    {
        $raw = '';

        foreach ($this->samples as $sample) {
            $raw .= $sample->getData();
        }

        $data = DataSection::createFromRaw($raw);

        $format = FormatSection::createFromParameters([
            'audioFormat' => $this->audioFormat,
            'numberOfChannels' => $this->numberOfChannels,
            'sampleRate' => $this->sampleRate,
            'byteRate' => $this->byteRate,
            'blockAlign' => $this->blockAlign,
            'bitsPerSample' => $this->bitsPerSample,
        ]);

        $header = Header::createFromDataSection($data);

        return new AudioFile($header, $format, $data);
    }

    public static function fromAudioFile(AudioFile $audioFile): Builder
    {
        return (new self)
            ->setAudioFormat($audioFile->getAudioFormat())
            ->setNumberOfChannels($audioFile->getNumberOfChannels())
            ->setSampleRate($audioFile->getSampleRate())
            ->setByteRate($audioFile->getByteRate())
            ->setBlockAlign($audioFile->getBlockAlign())
            ->setBitsPerSample($audioFile->getBitsPerSample())
            ->setSamples($audioFile->getAsSamples());
    }
}