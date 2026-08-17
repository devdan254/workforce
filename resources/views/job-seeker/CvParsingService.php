<?php

namespace App\Services;

/**
 * Best-effort CV field extraction — genuinely best-effort, per the spec:
 * "NEVER blindly trust extracted CV data... fall back gracefully to manual
 * entry... not a hard dependency." This is NOT resume-parsing AI — it's
 * regex/heuristic extraction of the obvious fields (email, phone, a
 * plausible name), wrapped in a try/catch that returns an empty array on
 * ANY failure so a malformed or unusual CV never blocks the application.
 *
 * PDF via smalot/pdfparser (pure PHP, no external binary). DOCX via PHP's
 * built-in ZipArchive — a .docx is a zip containing word/document.xml,
 * so no extra package is needed for basic text extraction from it.
 */
class CvParsingService
{
    public function extract(string $absolutePath, string $mimeType): array
    {
        try {
            $text = match (true) {
                str_contains($mimeType, 'pdf') => $this->extractFromPdf($absolutePath),
                str_contains($mimeType, 'wordprocessingml') => $this->extractFromDocx($absolutePath),
                default => '',
            };
        } catch (\Throwable $e) {
            return []; // any parsing failure at all → graceful fallback, never a hard dependency
        }

        if (! $text) {
            return [];
        }

        return array_filter([
            'email' => $this->extractEmail($text),
            'phone' => $this->extractPhone($text),
            'full_name' => $this->guessName($text),
        ]);
    }

    private function extractFromPdf(string $path): string
    {
        if (! class_exists(\Smalot\PdfParser\Parser::class)) {
            return ''; // package not installed — fall back silently, not a hard dependency
        }

        $parser = new \Smalot\PdfParser\Parser();

        return $parser->parseFile($path)->getText();
    }

    private function extractFromDocx(string $path): string
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            return '';
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (! $xml) {
            return '';
        }

        $withBreaks = str_replace(['</w:p>', '</w:tab>'], ["\n", "\t"], $xml);

        return strip_tags($withBreaks);
    }

    private function extractEmail(string $text): ?string
    {
        preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text, $matches);

        return $matches[0] ?? null;
    }

    private function extractPhone(string $text): ?string
    {
        preg_match('/(\+?\d[\d\s\-\(\)]{7,}\d)/', $text, $matches);

        return isset($matches[0]) ? trim($matches[0]) : null;
    }

    /**
     * Very rough heuristic: the first line near the top of the document that
     * looks like "Firstname Lastname" (2-4 words, letters only, no @ symbol).
     * CVs vary wildly in layout — this catches the common case and silently
     * misses the rest, which is exactly the intended behavior here.
     */
    private function guessName(string $text): ?string
    {
        $lines = array_filter(array_map('trim', explode("\n", $text)));

        foreach (array_slice($lines, 0, 5) as $line) {
            if (preg_match('/^[A-Za-z]+(\s[A-Za-z]+){1,3}$/', $line) && ! str_contains($line, '@')) {
                return $line;
            }
        }

        return null;
    }
}
