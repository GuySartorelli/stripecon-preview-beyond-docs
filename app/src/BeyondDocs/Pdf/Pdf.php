<?php

namespace App\BeyondDocs\Pdf;

use mikehaertl\wkhtmlto\Pdf as WkhtmltoPdf;
use SilverStripe\Assets\Image;
use SilverStripe\Control\HTTP;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\View\Parsers\ShortcodeParser;
use RuntimeException;

/**
 * @property string $Name
 * @property DBHTMLText|string $Content
 */
trait Pdf
{
    /**
     * @throws RuntimeException if the pdf couldn't render
     */
    public function sendToBrowser()
    {
        $pdfHandler = $this->preparePdf();
        $sent = $pdfHandler->send($this->Name . '.pdf', true);
        if (!$sent) {
            throw new RuntimeException('Error when rendering PDF: ' . $pdfHandler->getError());
        }
    }

    protected function preparePdf(): WkhtmltoPdf
    {
        $options = array_merge([
            'no-outline',
            'title' => $this->Name,
        ], $this->getPdfOptions());
        $pdfHandler = new WkhtmltoPdf($options);

        $pdfHandler->addPage($this->parseContent($this->Content));

        return $pdfHandler;
    }

    public function getPdfOptions(): array
    {
        return [];
    }

    private function parseContent(string $content): string
    {
        // NOTE: Somewhere in here you'd likely have some custom logic to convert variables in the content
        // into concrete values from a given record. That goes well beyond the purpose of this demo though.
        $content = $this->encodeImages($this->Content);
        $content = ShortcodeParser::get_active()->parse($content);
        return HTTP::absoluteURLs($content);
    }

    private function encodeImages(string $content): string
    {
        // Note: You could do all this using a subclass of ImageShortcodeProvider but this was just simpler for this example.
        return preg_replace_callback('/\[image\s+[^\]]*?id="(?<id>\d+)".*?\]/i', function($match) {
            $id = (int)$match['id'];
            /** @var Image $image */
            $image = Image::get_by_id($id);
            $encodedImage = 'data:' . $image->getMimeType() . ';base64,' . base64_encode($image->getString());

            // Remove ID so it isn't present in the final markup
            $markup = preg_replace('/id="' . $id . '"/i', '', $match[0]);
            // Convert shortcode to HTML img tag
            $markup = preg_replace('/^\[image/i', '<img', $markup);
            $markup = preg_replace('/\]$/', '>', $markup);

            // Add in the base64 encoded image
            if (str_contains(strtolower($markup), 'src="')) {
                return preg_replace('/src="[^"]*"/i', 'src="' . $encodedImage . '"', $markup);
            }
            return preg_replace('/^<img /', '<img src="' . $encodedImage . '"', $markup);

        }, $content);
    }
}
