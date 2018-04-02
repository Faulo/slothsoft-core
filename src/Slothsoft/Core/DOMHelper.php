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

use Slothsoft\Core\IO\HTTPFile;
use Slothsoft\Core\XSLT\XsltFactory;
use Slothsoft\Core\XSLT\Adapters\AdapterInterface;
use DOMDocument;
use DOMDocumentFragment;
use DOMImplementation;
use DOMNode;
use DOMXPath;
use Exception;
use OutOfRangeException;
use RuntimeException;

class DOMHelper
{

    const NS_FARAH_MODULE = 'http://schema.slothsoft.net/farah/module';

    const NS_FARAH_DICTIONARY = 'http://schema.slothsoft.net/farah/dictionary';

    const NS_FARAH_SITES = 'http://schema.slothsoft.net/farah/sitemap';

    const NS_SAVEGAME_EDITOR = 'http://schema.slothsoft.net/savegame/editor';
    
    const NS_AMBER_AMBERDATA = 'http://schema.slothsoft.net/amber/amberdata';
    
    const NS_SCHEMA_VERSIONING = 'http://schema.slothsoft.net/schema/versioning';

    const NS_XML = 'http://www.w3.org/XML/1998/namespace';

    const NS_HTML = 'http://www.w3.org/1999/xhtml';

    const NS_XSL = 'http://www.w3.org/1999/XSL/Transform';

    const NS_XSD = 'http://www.w3.org/2001/XMLSchema';

    const NS_SVG = 'http://www.w3.org/2000/svg';

    const NS_XLINK = 'http://www.w3.org/1999/xlink';

    const NS_ATOM = 'http://www.w3.org/2005/Atom';

    const NS_PHP = 'http://php.net/xpath';

    const NS_EM = 'http://www.mozilla.org/2004/em-rdf#';

    const NS_GD = 'http://schemas.google.com/g/2005';

    const NS_MEDIA = 'http://search.yahoo.com/mrss/';

    const NS_SITEMAP = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    protected static $namespaceList = [
        'sfm' => self::NS_FARAH_MODULE,
        'sfd' => self::NS_FARAH_DICTIONARY,
        'sfs' => self::NS_FARAH_SITES,
        
        'sse' => self::NS_SAVEGAME_EDITOR,
        
        'saa' => self::NS_AMBER_AMBERDATA,
        
        'xml' => self::NS_XML,
        'html' => self::NS_HTML,
        'xsl' => self::NS_XSL,
        'xsd' => self::NS_XSD,
        'svg' => self::NS_SVG,
        'xlink' => self::NS_XLINK,
        'atom' => self::NS_ATOM,
        'php' => self::NS_PHP,
        'em' => self::NS_EM,
        'gd' => self::NS_GD,
        'media' => self::NS_MEDIA,
        'sitemap' => self::NS_SITEMAP
    ];

    const XPATH_NS_ALL = 1;

    // loadXPath loads all known namespaces
    const XPATH_HTML = 2;

    // loadXPath loads HTML namespace
    const XPATH_PHP = 4;

    // loadXPath loads PHP functions
    const XPATH_SLOTHSOFT = 8;

    // loadXPath loads Slothsoft namespaces
    public static function loadDocument($filePath, $asHTML = false): DOMDocument
    {
        $document = new DOMDocument();
        if ($asHTML) {
            $document->loadHTMLFile($filePath, LIBXML_PARSEHUGE);
        } else {
            $document->load($filePath, LIBXML_PARSEHUGE);
        }
        return $document;
    }

    public static function loadXPath(DOMDocument $document, int $options = self::XPATH_HTML): DOMXPath
    {
        $xpath = new DOMXPath($document);
        $nsList = [];
        if ($options & self::XPATH_NS_ALL) {
            foreach (self::$namespaceList as $prefix => $ns) {
                $nsList[$prefix] = $ns;
            }
        }
        if ($options & self::XPATH_HTML) {
            $nsList['html'] = self::NS_HTML;
        }
        if ($options & self::XPATH_PHP) {
            $nsList['php'] = self::NS_PHP;
        }
        if ($options & self::XPATH_SLOTHSOFT) {
            $nsList['sfm'] = self::NS_FARAH_MODULE;
            $nsList['sfd'] = self::NS_FARAH_DICTIONARY;
            $nsList['sfs'] = self::NS_FARAH_SITES;
            $nsList['sse'] = self::NS_SAVEGAME_EDITOR;
            $nsList['saa'] = self::NS_AMBER_AMBERDATA;
        }
        foreach ($nsList as $prefix => $ns) {
            $xpath->registerNamespace($prefix, $ns);
        }
        if ($options & self::XPATH_PHP) {
            $xpath->registerPHPFunctions();
        }
        return $xpath;
    }

    const HTML_FRAME = '<?xml version="1.0" encoding="UTF-8"?><html><body>%s</body></html>';

    protected static $impl;

    protected static function _implementation()
    {
        if (! self::$impl) {
            self::$impl = new DOMImplementation();
        }
        return self::$impl;
    }

    // returns DOMDocumentFragment
    public function parse($xmlCode, DOMDocument $targetDoc = null, $asHTML = false)
    {
        if ($asHTML) {
            $parseDoc = self::_implementation()->createDocument();
            
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
                $targetDoc = self::_implementation()->createDocument();
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

    public function createDocument(string $namespaceURI, string $qualifiedName): DOMDocument
    {
        return self::_implementation()->createDocument($namespaceURI, $qualifiedName);
    }

    // returns string
    public function stringify(DOMNode $sourceNode)
    {
        return $sourceNode->ownerDocument->saveXML($sourceNode);
    }

    public function load($url, $asHTML = false)
    {
        $doc = new DOMDocument();
        if ($asHTML) {
            $doc->loadHTMLFile((string) $url);
        } else {
            $doc->load((string) $url);
        }
        return $doc;
    }

    private function transformToAdapter($source, $template, array $param = []): AdapterInterface
    {
        $source = XsltFactory::createInput($source);
        $template = XsltFactory::createInput($template);
        
        $templateDoc = $template->toDocument();
        $templateVersion = $templateDoc->documentElement->getAttribute('version');
        
        $adapter = XsltFactory::createAdapter(floatval($templateVersion));
        
        $adapter->setSource($source);
        $adapter->setTemplate($template);
        $adapter->setParameters($param);
        
        return $adapter;
    }

    public function transformToDocument($source, $template, array $param = []): DOMDocument
    {
        return $this->transformToAdapter($source, $template, $param)->writeDocument();
    }

    public function transformToFile($source, $template, array $param = [], HTTPFile $output = null): HTTPFile
    {
        if (! $output) {
            $output = HTTPFile::createFromTemp();
        }
        
        $adapter = $this->transformToAdapter($source, $template, $param);
        $adapter->writeFile($output);
        return $output;
    }

    public function transformToFragment($source, $template, array $param = [], DOMDocument $targetDoc): DOMDocumentFragment
    {
        $finalDoc = $this->transformToDocument($source, $template, $param);
        
        $retNode = $targetDoc->createDocumentFragment();
        foreach ($finalDoc->childNodes as $node) {
            $retNode->appendChild($targetDoc->importNode($node, true));
        }
        return $retNode;
    }

    public function normalizeDocument(DOMDocument $dataDoc)
    {
        try {
            $retDoc = new DOMDocument();
            
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

    protected function normalizeNode(DOMNode $sourceNode, DOMDocument $retDoc, array $nsList)
    {
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

    private function newSaxonProcessor(int $xsltVersion)
    {
        if (isset(CORE_DOMHELPER_XSLT_USAGE[$xsltVersion])) {
            $class = CORE_DOMHELPER_XSLT_USAGE[$xsltVersion];
            return new $class();
        }
        throw new OutOfRangeException("DOMHelper does not support XSLT version $xsltVersion! (add it to CORE_DOMHELPER_XSLT_USAGE)");
    }
}