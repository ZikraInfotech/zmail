<?php

namespace Acelle\Library\HtmlHandler;

use League\Pipeline\StageInterface;
use Acelle\Library\StringHelper;

class AddPreheader implements StageInterface
{
    public $preheader;

    public function __construct($preheader)
    {
        $this->preheader = trim($preheader);
    }

    public function __invoke($html)
    {
        if (empty($this->preheader)) {
            return $html;
        }

        return StringHelper::updateHtml($html, function ($dom) {
            $body = $dom->getElementsByTagName('body')->item(0);
            $e = $dom->createElement('div', $this->preheader);
            $e->setAttribute('style', 'display:none;font-size:1px;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden');

            if ($body->hasChildNodes()) {
                $body->insertBefore($e, $body->firstChild);
            } else {
                $body->appendChild($e);
            }
        });
    }
}
