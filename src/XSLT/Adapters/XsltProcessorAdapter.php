<?php
declare(strict_types = 1);

namespace Slothsoft\Core\XSLT\Adapters;

use DOMDocument;
use DOMImplementation;
use DOMText;
use Slothsoft\Core\IO\FileInfoFactory;
use SplFileInfo;
use XSLTProcessor;

/**
 * XSLT adapter backed by PHP's native XSLTProcessor extension.
 *
 * @author Daniel Schulz
 * @since 2018-02-21
 */
final class XsltProcessorAdapter extends GenericAdapter {
    
    /**
     * @param ?SplFileInfo $outputFile
     * @return SplFileInfo
     */
    public function writeFile(?SplFileInfo $outputFile = null): SplFileInfo {
        if (! $outputFile) {
            $outputFile = FileInfoFactory::createTempFile();
        }
        
        $xslt = new XSLTProcessor();
        foreach ($this->param as $key => $val) {
            $xslt->setParameter('', (string) $key, (string) $val);
        }
        
        $xslt->registerPHPFunctions();
        $xslt->importStylesheet($this->template->toDocument());
        
        $xslt->transformToUri($this->source->toDocument(), (string) $outputFile);
        
        return $outputFile;
    }
    
    /**
     * @return DOMDocument
     */
    public function writeDocument(): DOMDocument {
        $xslt = new XSLTProcessor();
        foreach ($this->param as $key => $val) {
            $xslt->setParameter('', (string) $key, (string) $val);
        }
        
        $xslt->registerPHPFunctions();
        $xslt->importStylesheet($this->template->toDocument());
        
        return $this->normalizeDocumentType($xslt->transformToDoc($this->source->toDocument()));
    }
    
    private function normalizeDocumentType(DOMDocument $document): DOMDocument {
        $documentElement = $document->documentElement;
        if ($documentElement === null) {
            return $document;
        }
        
        $documentTypeNode = null;
        foreach ($document->childNodes as $node) {
            if ($node->isSameNode($documentElement)) {
                break;
            }
            if ($node instanceof DOMText) {
                if ($documentTypeNode !== null) {
                    return $document;
                }
                $documentTypeNode = $node;
            }
        }
        if ($documentTypeNode === null) {
            return $document;
        }
        
        $probe = new DOMDocument();
        if (! $probe->loadXML($documentTypeNode->textContent . '<root/>', LIBXML_NONET | LIBXML_NOERROR)) {
            return $document;
        }
        if (! $probe->doctype) {
            return $document;
        }
        if ($probe->doctype->internalSubset) {
            return $document;
        }
        
        $replacement = (new DOMImplementation())->createDocumentType(
            $probe->doctype->name,
            $probe->doctype->publicId,
            $probe->doctype->systemId
        );
        if (! $replacement) {
            return $document;
        }
        $document->replaceChild($replacement, $documentTypeNode);
        return $document;
    }
}
