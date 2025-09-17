<?php
declare(strict_types = 1);
/**
 * *********************************************************************
 * DOMHelper v1.00 11.07.2014 © Daniel Schulz
 *
 * Changelog:
 * v1.00 11.07.2014
 * initial release
 * *********************************************************************
 */
namespace Slothsoft\Core;

use Slothsoft\Core\IO\FileInfoFactory;
use Slothsoft\Core\XSLT\XsltFactory;
use Slothsoft\Core\XSLT\Adapters\AdapterInterface;
use DOMDocument;
use DOMDocumentFragment;
use DOMImplementation;
use DOMNode;
use DOMXPath;
use DomainException;
use Exception;
use RuntimeException;
use SplFileInfo;

class DOMHelper {
    
    public const NS_AMBER_AMBERDATA = 'http://schema.slothsoft.net/amber/amberdata';
    
    public const NS_CRON_INSTRUCTIONS = 'http://schema.slothsoft.net/cron/instructions';
    
    public const NS_FARAH_DICTIONARY = 'http://schema.slothsoft.net/farah/dictionary';
    
    public const NS_FARAH_MODULE = 'http://schema.slothsoft.net/farah/module';
    
    public const NS_FARAH_SITES = 'http://schema.slothsoft.net/farah/sitemap';
    
    public const NS_SAVEGAME_EDITOR = 'http://schema.slothsoft.net/savegame/editor';
    
    public const NS_SCHEMA_VERSIONING = 'http://schema.slothsoft.net/schema/versioning';
    
    private const SLOTHSOFT_NAMESPACES = [
        'saa' => self::NS_AMBER_AMBERDATA,
        'sci' => self::NS_CRON_INSTRUCTIONS,
        'sfd' => self::NS_FARAH_DICTIONARY,
        'sfm' => self::NS_FARAH_MODULE,
        'sfs' => self::NS_FARAH_SITES,
        'sse' => self::NS_SAVEGAME_EDITOR,
        'ssv' => self::NS_SCHEMA_VERSIONING
    ];
    
    public const NS_XML = 'http://www.w3.org/XML/1998/namespace';
    
    public const NS_HTML = 'http://www.w3.org/1999/xhtml';
    
    public const NS_XSL = 'http://www.w3.org/1999/XSL/Transform';
    
    public const NS_XSD = 'http://www.w3.org/2001/XMLSchema';
    
    public const NS_SVG = 'http://www.w3.org/2000/svg';
    
    public const NS_XLINK = 'http://www.w3.org/1999/xlink';
    
    public const NS_ATOM = 'http://www.w3.org/2005/Atom';
    
    public const NS_XINCLUDE = 'http://www.w3.org/2001/XInclude';
    
    private const W3C_NAMESPACES = [
        'html' => self::NS_HTML,
        'xml' => self::NS_XML,
        'xsl' => self::NS_XSL,
        'xsd' => self::NS_XSD,
        'svg' => self::NS_SVG,
        'xlink' => self::NS_XLINK,
        'atom' => self::NS_ATOM,
        'xi' => self::NS_XINCLUDE
    ];
    
    private const HTML_NAMESPACES = [
        'html' => self::NS_HTML
    ];
    
    public const NS_PHP = 'http://php.net/xpath';
    
    private const PHP_NAMESPACES = [
        'php' => self::NS_PHP
    ];
    
    public const NS_EM = 'http://www.mozilla.org/2004/em-rdf#';
    
    public const NS_GD = 'http://schemas.google.com/g/2005';
    
    public const NS_MEDIA = 'http://search.yahoo.com/mrss/';
    
    public const NS_SITEMAP = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    
    private const MISC_NAMESPACES = [
        'em' => self::NS_EM,
        'gd' => self::NS_GD,
        'media' => self::NS_MEDIA,
        'sitemap' => self::NS_SITEMAP
    ];
    
    public const XPATH_NS_ALL = - 1;
    
    public const XPATH_SLOTHSOFT = 1;
    
    public const XPATH_W3C = 2;
    
    public const XPATH_HTML = 4;
    
    public const XPATH_PHP = 8;
    
    public const XPATH_MISC = 16;
    
    public static function loadDocument(string $filePath, bool $asHTML = false): DOMDocument {
        $document = self::dom()->createDocument();
        if ($asHTML) {
            $document->loadHTMLFile($filePath, LIBXML_PARSEHUGE);
        } else {
            $document->load($filePath, LIBXML_PARSEHUGE);
        }
        return $document;
    }
    
    public static function loadXPath(DOMDocument $document, int $options = self::XPATH_HTML): DOMXPath {
        $xpath = new DOMXPath($document);
        $nsList = [];
        if ($options & self::XPATH_SLOTHSOFT) {
            $nsList += self::SLOTHSOFT_NAMESPACES;
        }
        if ($options & self::XPATH_W3C) {
            $nsList += self::W3C_NAMESPACES;
        }
        if ($options & self::XPATH_HTML) {
            $nsList += self::HTML_NAMESPACES;
        }
        if ($options & self::XPATH_PHP) {
            $nsList += self::PHP_NAMESPACES;
        }
        if ($options & self::XPATH_MISC) {
            $nsList += self::MISC_NAMESPACES;
        }
        foreach ($nsList as $prefix => $ns) {
            $xpath->registerNamespace($prefix, $ns);
        }
        if ($options & self::XPATH_PHP) {
            $xpath->registerPHPFunctions();
        }
        return $xpath;
    }
    
    public static function guessExtension(string $namespaceURI): string {
        switch ($namespaceURI) {
            case self::NS_HTML:
                return 'xhtml';
            case self::NS_SVG:
                return 'svg';
            default:
                return 'xml';
        }
    }
    
    private const HTML_FRAME = '<?xml version="1.0" encoding="UTF-8"?><html><body>%s</body></html>';
    
    private static function dom(): DOMImplementation {
        static $implementation = null;
        if ($implementation === null) {
            $implementation = new DOMImplementation();
        }
        return $implementation;
    }
    
    public function parse(string $xmlCode, DOMDocument $targetDoc = null, bool $asHTML = false): DOMDocumentFragment {
        if ($asHTML) {
            $parseDoc = self::dom()->createDocument();
            
            $ret = @$parseDoc->loadHTML(sprintf(self::HTML_FRAME, $xmlCode));
            if (! $ret) {
                ob_start();
                $parseDoc->loadHTML(sprintf(self::HTML_FRAME, $xmlCode));
                $error = ob_get_contents();
                ob_end_clean();
                throw new RuntimeException(sprintf('Error loading HTML:%s%s%s%s', PHP_EOL, substr($xmlCode, 0, 1024), PHP_EOL, substr($error, 0, 1024)));
            }
            $rootNode = $parseDoc->documentElement->lastChild;
            
            if ($targetDoc === null) {
                $targetDoc = $parseDoc;
            }
            
            $retFragment = $targetDoc->createDocumentFragment();
            
            $childNodeList = [];
            foreach ($rootNode->childNodes as $childNode) {
                $childNodeList[] = $childNode;
            }
            
            foreach ($childNodeList as $childNode) {
                if ($targetDoc === $parseDoc) {
                    $retFragment->appendChild($childNode);
                } else {
                    $retFragment->appendChild($targetDoc->importNode($childNode, true));
                }
            }
            if ($targetDoc === $parseDoc) {
                while ($targetDoc->hasChildNodes()) {
                    $targetDoc->removeChild($targetDoc->lastChild);
                }
            }
        } else {
            if ($targetDoc === null) {
                $targetDoc = self::dom()->createDocument();
            }
            
            $retFragment = $targetDoc->createDocumentFragment();
            
            if (strlen($xmlCode)) {
                $ret = @$retFragment->appendXML($xmlCode);
                if (! $ret) {
                    ob_start();
                    $retFragment->appendXML($xmlCode);
                    $error = ob_get_contents();
                    ob_end_clean();
                    throw new RuntimeException(sprintf('Error loading XML:%s%s%s%s', PHP_EOL, substr($xmlCode, 0, 1024), PHP_EOL, substr($error, 0, 1024)));
                }
            }
        }
        
        return $retFragment;
    }
    
    public function createDocument(string $namespaceURI, string $qualifiedName): DOMDocument {
        return self::dom()->createDocument($namespaceURI, $qualifiedName);
    }
    
    public function stringify(DOMNode $sourceNode): string {
        return $sourceNode->ownerDocument->saveXML($sourceNode);
    }
    
    public function load($url, bool $asHTML = false): DOMDocument {
        $doc = self::dom()->createDocument();
        if ($asHTML) {
            $doc->loadHTMLFile((string) $url);
        } else {
            $doc->load((string) $url);
        }
        return $doc;
    }
    
    private function transformToAdapter($source, $template, array $param = []): AdapterInterface {
        $source = XsltFactory::createInput($source);
        $template = XsltFactory::createInput($template);
        
        $templateDoc = $template->toDocument();
        if ($templateDoc->documentElement->namespaceURI !== self::NS_XSL) {
            throw new DomainException("Template file '{$template->toFile()}' is in namespace '{$templateDoc->documentElement->namespaceURI}, but should have been 'http://www.w3.org/1999/XSL/Transform'!");
        }
        $templateVersion = $templateDoc->documentElement->getAttribute('version');
        
        $adapter = XsltFactory::createAdapter(floatval($templateVersion));
        
        $adapter->setSource($source);
        $adapter->setTemplate($template);
        $adapter->setParameters($param);
        
        return $adapter;
    }
    
    public function transformToDocument($source, $template, array $param = []): DOMDocument {
        return $this->transformToAdapter($source, $template, $param)->writeDocument();
    }
    
    public function transformToFile($source, $template, array $param = [], SplFileInfo $output = null): SplFileInfo {
        if (! $output) {
            $output = FileInfoFactory::createFromTemp();
        }
        
        $adapter = $this->transformToAdapter($source, $template, $param);
        $adapter->writeFile($output);
        return $output;
    }
    
    public function transformToFragment($source, $template, array $param = [], DOMDocument $targetDoc = null): DOMDocumentFragment {
        $finalDoc = $this->transformToDocument($source, $template, $param);
        
        if ($targetDoc === null) {
            $targetDoc = self::dom()->createDocument();
        }
        $retNode = $targetDoc->createDocumentFragment();
        foreach ($finalDoc->childNodes as $node) {
            $retNode->appendChild($targetDoc->importNode($node, true));
        }
        return $retNode;
    }
    
    public function normalizeDocument(DOMDocument $dataDoc): DOMDocument {
        try {
            $retDoc = self::dom()->createDocument();
            
            $nsList = array_flip(self::$namespaceList);
            if (isset($nsList[$dataDoc->documentElement->namespaceURI])) {
                unset($nsList[$dataDoc->documentElement->namespaceURI]);
            }
            
            $this->normalizeNode($dataDoc, $retDoc, $nsList);
            
            $retDoc->loadXML($retDoc->saveXML(), LIBXML_NSCLEAN);
        } catch (Exception $e) {
            $retDoc = $dataDoc;
        }
        return $retDoc;
    }
    
    private function normalizeNode(DOMNode $sourceNode, DOMDocument $retDoc, array $nsList): DOMNode {
        $retNode = null;
        switch ($sourceNode->nodeType) {
            case XML_DOCUMENT_NODE:
                $retNode = $retDoc;
                break;
            case XML_ELEMENT_NODE:
                $tagName = isset($nsList[$sourceNode->namespaceURI]) ? $nsList[$sourceNode->namespaceURI] . ':' . $sourceNode->localName : $sourceNode->localName;
                $retNode = $retDoc->createElementNS($sourceNode->namespaceURI, $tagName);
                foreach ($sourceNode->attributes as $childNode) {
                    $tagName = strlen($childNode->prefix) ? $childNode->prefix . ':' . $childNode->localName : $childNode->localName;
                    $retNode->setAttributeNS($childNode->namespaceURI, $tagName, $childNode->value);
                }
                break;
            default:
                $retNode = $retDoc->importNode($sourceNode, false);
                break;
        }
        if ($retNode and $sourceNode->hasChildNodes()) {
            foreach ($sourceNode->childNodes as $childNode) {
                if ($node = $this->normalizeNode($childNode, $retDoc, $nsList)) {
                    $retNode->appendChild($node);
                }
            }
        }
        return $retNode;
    }
}