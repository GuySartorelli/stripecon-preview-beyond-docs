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
     * Send the PDF directly to be rendered inline in the browser
     *
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

    /**
     * Prepare a PDF with its content, name, options etc ready for outputting to file
     * or to the browser.
     */
    protected function preparePdf(): WkhtmltoPdf
    {
        $options = array_merge([
            'no-outline',
            'title' => $this->Name,
        ], $this->getPdfOptions());
        $pdfHandler = new WkhtmltoPdf($options);

        $pdfHandler->addPage($this->parseContent($this->Content ?? ''));

        return $pdfHandler;
    }

    /**
     * Get options to be passed into wkhtmltopdf (see https://wkhtmltopdf.org/usage/wkhtmltopdf.txt)
     * This is to be overridden in classes that use this trait.
     */
    public function getPdfOptions(): array
    {
        return [];
    }

    /**
     * Parse out and prepare raw content into its final HTML markup for wkhtml to process.
     */
    private function parseContent(string $content): string
    {
        // NOTE: Somewhere in here you'd likely have some custom logic to convert variables in the content
        // into concrete values from a given record. That goes well beyond the purpose of this demo though.
        $content = $this->encodeImages($this->Content);
        $content = ShortcodeParser::get_active()->parse($content);
        return HTTP::absoluteURLs($content);
    }

    /**
     * Rewrite [img] shortcodes into <img> tags with base64 encoded images.
     * This ensures that images are baked into the PDF instead of requiring internet access to view images within it.
     */
    private function encodeImages(string $content): string
    {
        // Note: You could do all this using a subclass of ImageShortcodeProvider but this was just simpler for this example.
        // There will be edge cases that aren't caught though so you should do it properly if you do this in a project.
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
