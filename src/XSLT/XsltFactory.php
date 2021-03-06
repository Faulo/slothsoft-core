<?php
declare(strict_types = 1);
namespace Slothsoft\Core\XSLT;

use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Core\IO\Writable\FileWriterInterface;
use Slothsoft\Core\XSLT\Adapters\AdapterInterface;
use Slothsoft\Core\XSLT\Adapters\CliAdapter;
use Slothsoft\Core\XSLT\Adapters\SaxonProcessorAdapter;
use Slothsoft\Core\XSLT\Adapters\XsltProcessorAdapter;
use Slothsoft\Core\XSLT\Inputs\DocumentInput;
use Slothsoft\Core\XSLT\Inputs\FileInput;
use Slothsoft\Core\XSLT\Inputs\InputInterface;
use DOMDocument;
use DomainException;
use SplFileInfo;

/**
 *
 * @author Daniel Schulz
 *        
 */
class XsltFactory {

    const PROCESSOR_PHP = 'php';

    const PROCESSOR_LIBXML = 'libxml';

    const PROCESSOR_EXSELT = 'exselt';

    const PROCESSOR_SAXON8 = 'saxon8';

    const PROCESSOR_SAXON9 = 'saxon9';

    const PROCESSOR_SAXONC = 'saxonc';

    // processor-specific stuff
    const PROCESSOR_LIBXML_ARGS = '%2$s %1$s >%3$s';

    const PROCESSOR_EXSELT_ARGS = '-xml:%1$s -xsl:%2$s -o:%3$s';

    const PROCESSOR_SAXON8_ARGS = '-s %1$s -o %3$s %2$s';

    const PROCESSOR_SAXON9_ARGS = '%1$s -xsl:%2$s -o:%3$s';

    private static $versionMapping = [
        '1.0' => self::PROCESSOR_PHP
    ];

    private static $processorConfiguration = [
        self::PROCESSOR_PHP => [],
        self::PROCESSOR_LIBXML => [
            'path' => ''
        ],
        self::PROCESSOR_EXSELT => [
            'path' => ''
        ],
        self::PROCESSOR_SAXON8 => [
            'path' => ''
        ],
        self::PROCESSOR_SAXON9 => [
            'path' => ''
        ],
        self::PROCESSOR_SAXONC => []
    ];

    public static function setProcessorForVersion(string $xsltVersion, string $processorId) {
        self::assertIsValidProcessor($processorId);
        self::$versionMapping[$xsltVersion] = $processorId;
    }

    public static function getProcessorForVersion(string $xsltVersion): string {
        self::assertIsValidVersion($xsltVersion);
        return self::$versionMapping[$xsltVersion];
    }

    public static function setConfigurationForProcessor(string $processorId, array $config) {
        self::assertIsValidProcessor($processorId);
        self::$processorConfiguration[$processorId] = $config;
    }

    public static function getConfigurationForProcessor(string $processorId): array {
        self::assertIsValidProcessor($processorId);
        return self::$processorConfiguration[$processorId];
    }

    private static function assertIsValidVersion(string $xsltVersion) {
        if (! isset(self::$versionMapping[$xsltVersion])) {
            throw new DomainException("XSLT version '$xsltVersion' is not supported by this implementation by default, you may enable it via XsltFactry:setProcessorForVersion.");
        }
    }

    private static function assertIsValidProcessor(string $processorId) {
        if (! isset(self::$processorConfiguration[$processorId])) {
            throw new DomainException("XSLT processor '$processorId' is not supported by this implementation.");
        }
    }

    public static function createAdapter(float $xsltVersion): AdapterInterface {
        $xsltVersion = sprintf('%1.1f', $xsltVersion);
        $processorId = self::getProcessorForVersion($xsltVersion);
        $config = self::getConfigurationForProcessor($processorId);

        switch ($processorId) {
            case self::PROCESSOR_PHP:
                return new XsltProcessorAdapter();
            case self::PROCESSOR_LIBXML:
                return new CliAdapter($config['path'], self::PROCESSOR_LIBXML_ARGS);
            case self::PROCESSOR_EXSELT:
                return new CliAdapter($config['path'], self::PROCESSOR_EXSELT_ARGS);
            case self::PROCESSOR_SAXON8:
                return new CliAdapter($config['path'], self::PROCESSOR_SAXON8_ARGS);
            case self::PROCESSOR_SAXON9:
                return new CliAdapter($config['path'], self::PROCESSOR_SAXON9_ARGS);
            case self::PROCESSOR_SAXONC:
                return new SaxonProcessorAdapter();
        }
    }

    public static function createInput($input): InputInterface {
        switch (true) {
            case is_string($input):
                return new FileInput(FileInfoFactory::createFromPath($input));
            case $input instanceof DOMDocument:
                return new DocumentInput($input);
            case $input instanceof DOMWriterInterface:
                return new DocumentInput($input->toDocument());
            case $input instanceof SplFileInfo:
                return new FileInput($input);
            case $input instanceof FileWriterInterface:
                return new FileInput($input->toFile());
            case is_object($input):
                throw new DomainException("input class not supported: " . get_class($input));
            default:
                throw new DomainException("input type not supported: " . gettype($input));
        }
    }
}

