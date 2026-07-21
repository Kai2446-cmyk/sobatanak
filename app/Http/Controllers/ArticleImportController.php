<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use ZipArchive;

class ArticleImportController extends Controller
{
    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'article_file' => ['required', 'file', 'max:10240', 'mimes:docx,txt,md,markdown,html,htm'],
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('article_file');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            $text = $extension === 'docx'
                ? $this->readDocx($file->getRealPath())
                : (string) file_get_contents($file->getRealPath());
        } catch (\Throwable $error) {
            report($error);

            return response()->json([
                'ok' => false,
                'message' => 'Dokumen belum bisa dibaca. Pastikan file tidak rusak atau terkunci.',
            ], 422);
        }

        $content = $this->toMarkdown($text);
        if (mb_strlen(trim($content)) < 10) {
            return response()->json([
                'ok' => false,
                'message' => 'Isi dokumen tidak ditemukan atau terlalu sedikit untuk diimpor.',
            ], 422);
        }

        $firstLine = collect(preg_split('/\R/u', $content))
            ->map(fn ($line) => trim((string) $line, " #\t"))
            ->first(fn ($line) => $line !== '');

        return response()->json([
            'ok' => true,
            'title' => mb_substr((string) $firstLine, 0, 180),
            'content' => $content,
        ]);
    }

    private function readDocx(string $path): string
    {
        if (!class_exists(ZipArchive::class)) {
            throw new \RuntimeException('Ekstensi ZIP PHP belum aktif.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('File DOCX tidak dapat dibuka.');
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            throw new \RuntimeException('Isi utama DOCX tidak ditemukan.');
        }

        $xml = str_replace(
            ['</w:p>', '</w:tr>', '<w:tab/>', '<w:br/>', '<w:cr/>'],
            ["\n", "\n", "\t", "\n", "\n"],
            $xml
        );

        return html_entity_decode(strip_tags($xml), ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function toMarkdown(string $text): string
    {
        $text = preg_replace('/\r\n?|\x{2028}|\x{2029}/u', "\n", $text) ?? $text;
        $lines = preg_split('/\n/u', $text) ?: [];
        $clean = [];

        foreach ($lines as $line) {
            $line = trim(preg_replace('/[ \t]+/u', ' ', strip_tags((string) $line)) ?? '');
            if ($line === '' && end($clean) === '') {
                continue;
            }
            $clean[] = $line;
        }

        return trim(implode("\n", $clean));
    }
}
