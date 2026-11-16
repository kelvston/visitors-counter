<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use RuntimeException;

class StaffMailBody
{
    private const ALLOWED_ELEMENTS = [
        'a', 'blockquote', 'br', 'em', 'h2', 'h3', 'li', 'ol', 'p', 's', 'strong', 'u', 'ul',
    ];

    public function sanitize(string $html): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $containerId = 'staff-mail-'.bin2hex(random_bytes(12));
        $previousErrorMode = libxml_use_internal_errors(true);

        try {
            $loaded = $document->loadHTML(
                '<?xml encoding="UTF-8"><!DOCTYPE html><html><body><div id="'.$containerId.'">'.$html.'</div></body></html>',
                LIBXML_HTML_NODEFDTD
            );
            if (! $loaded) {
                throw new RuntimeException('Unable to parse the formatted message.');
            }

            $container = $document->getElementById($containerId);
            if (! $container) {
                throw new RuntimeException('Unable to read the formatted message.');
            }

            $this->sanitizeChildren($container);

            $result = '';
            foreach ($container->childNodes as $child) {
                $result .= $document->saveHTML($child);
            }

            return $result;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previousErrorMode);
        }
    }

    private function sanitizeChildren(DOMNode $parent): void
    {
        foreach (iterator_to_array($parent->childNodes) as $child) {
            if (! $child instanceof DOMElement) {
                continue;
            }

            $tagName = strtolower($child->tagName);
            if (in_array($tagName, ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math'], true)) {
                $parent->removeChild($child);

                continue;
            }

            $this->sanitizeChildren($child);

            if (! in_array($tagName, self::ALLOWED_ELEMENTS, true)) {
                while ($child->firstChild) {
                    $parent->insertBefore($child->firstChild, $child);
                }
                $parent->removeChild($child);

                continue;
            }

            foreach (iterator_to_array($child->attributes) as $attribute) {
                if ($tagName !== 'a' || $attribute->name !== 'href' || ! $this->isSafeLink($attribute->value)) {
                    $child->removeAttribute($attribute->name);
                }
            }
        }
    }

    private function isSafeLink(string $url): bool
    {
        return preg_match('/^(https?:\/\/|mailto:)/i', trim($url)) === 1;
    }
}
