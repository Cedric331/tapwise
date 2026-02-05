<?php

namespace App\Http\Controllers;

use App\Models\Bar;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\ImageRendererInterface;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class QrCodeController extends Controller
{
    /**
     * Display the QR code page for a bar.
     */
    public function show(Request $request, Bar $bar): Response
    {
        $this->authorize('view', $bar);

        $publicUrl = $bar->public_url;
        $subscriptionStatus = $bar->subscriptionStatus();
        $billingUser = $bar->billingUser();
        $trialEndsAt = $subscriptionStatus === 'trial' ? $billingUser?->trial_ends_at : null;
        $frames = $this->availableFrames();
        $defaultFrame = $frames[0]['id'] ?? null;
        $style = $request->string('style')->toString() ?: 'simple';
        $frame = $request->string('frame')->toString() ?: $defaultFrame;
        if ($frame === 'none') {
            $frame = null;
        }
        $logoRequested = $request->boolean('logo', true);
        $logoAvailable = (bool) $bar->logo_path;
        $includeLogo = $logoAvailable && $logoRequested;

        if ($style === 'simple') {
            $frame = null;
        }

        $qrCodeSvg = $this->buildQrCodeSvg(
            $bar,
            $publicUrl,
            640,
            $style,
            $frame,
            $includeLogo,
            true
        );

        return Inertia::render('Bars/QrCode', [
            'bar' => $bar,
            'publicUrl' => $publicUrl,
            'qrCodeSvg' => $qrCodeSvg,
            'qrFrames' => $frames,
            'qrOptions' => [
                'style' => $style,
                'frame' => $frame,
                'includeLogo' => $includeLogo,
                'logoAvailable' => $logoAvailable,
            ],
            'subscription' => [
                'status' => $subscriptionStatus,
                'trialEndsAt' => $trialEndsAt?->toDateString(),
                'canDownload' => $bar->hasActiveAccess(),
            ],
        ]);
    }

    /**
     * Generate and download QR code as SVG.
     */
    public function download(Request $request, Bar $bar): HttpResponse
    {
        $this->authorize('view', $bar);

        if (! $bar->hasActiveAccess()) {
            abort(403);
        }

        $publicUrl = $bar->public_url;
        $frames = $this->availableFrames();
        $frameIds = array_column($frames, 'id');
        $style = $request->string('style')->toString() ?: 'simple';
        $frame = $request->string('frame')->toString();
        if ($frame === 'none') {
            $frame = null;
        }
        $logoRequested = $request->boolean('logo', true);
        $logoAvailable = (bool) $bar->logo_path;
        $includeLogo = $logoAvailable && $logoRequested;

        if ($style === 'simple' || !in_array($frame, $frameIds, true)) {
            $frame = null;
        }

        $qrCodeSvg = $this->buildQrCodeSvg(
            $bar,
            $publicUrl,
            1200,
            $style,
            $frame,
            $includeLogo,
            false
        );

        return response($qrCodeSvg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => "attachment; filename=\"qr-code-{$bar->slug}.svg\"",
        ]);
    }

    /**
     * Update the QR code availability.
     */
    public function updateStatus(Request $request, Bar $bar): RedirectResponse
    {
        $this->authorize('updateSettings', $bar);

        $data = $request->validate([
            'qr_enabled' => ['required', 'boolean'],
        ]);

        $bar->update([
            'qr_enabled' => $data['qr_enabled'],
        ]);

        return redirect()->back();
    }

    private function buildQrCodeSvg(
        Bar $bar,
        string $publicUrl,
        int $canvasSize,
        string $style,
        ?string $frameId,
        bool $includeLogo,
        bool $responsive
    ): string
    {
        $frameData = null;
        if ($style === 'framed' && $frameId) {
            $framePath = $this->framePath($frameId);
            $frameData = $framePath ? $this->toDataUri($framePath) : null;
        }

        $logoData = null;
        if ($includeLogo && $bar->logo_path) {
            $logoData = $this->toDataUri(Storage::disk('public')->path($bar->logo_path));
        }

        $qrSize = (int) round($canvasSize * 0.55);
        $qrOffset = (int) round(($canvasSize - $qrSize) / 2);
        $padding = (int) round($qrSize * 0.05);
        $logoSize = (int) round($qrSize * 0.22);
        $logoOffset = (int) round(($canvasSize - $logoSize) / 2);

        $renderer = new ImageRenderer(
            (new RendererStyle($qrSize))->withMargin(0),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $qrSvg = $writer->writeString($publicUrl);
        $qrContent = $this->extractSvgContent($qrSvg);

        $elements = [];
        if ($frameData) {
            $elements[] = sprintf(
                '<image href="%s" x="0" y="0" width="%d" height="%d" />',
                $frameData,
                $canvasSize,
                $canvasSize
            );
        } else {
            $elements[] = sprintf(
                '<rect x="0" y="0" width="%d" height="%d" fill="#FDFDFC" />',
                $canvasSize,
                $canvasSize
            );
        }

        $elements[] = sprintf(
            '<rect x="%d" y="%d" width="%d" height="%d" rx="%d" fill="#ffffff" opacity="0.98" />',
            $qrOffset - $padding,
            $qrOffset - $padding,
            $qrSize + ($padding * 2),
            $qrSize + ($padding * 2),
            (int) round($qrSize * 0.08)
        );

        $elements[] = sprintf('<g transform="translate(%d %d)">%s</g>', $qrOffset, $qrOffset, $qrContent);

        if ($logoData) {
            $logoPadding = (int) round($logoSize * 0.18);
            $elements[] = sprintf(
                '<rect x="%d" y="%d" width="%d" height="%d" rx="%d" fill="#ffffff" />',
                $logoOffset - $logoPadding,
                $logoOffset - $logoPadding,
                $logoSize + ($logoPadding * 2),
                $logoSize + ($logoPadding * 2),
                (int) round($logoSize * 0.2)
            );
            $elements[] = sprintf(
                '<image href="%s" x="%d" y="%d" width="%d" height="%d" />',
                $logoData,
                $logoOffset,
                $logoOffset,
                $logoSize,
                $logoSize
            );
        }

        $width = $responsive ? '100%' : (string) $canvasSize;
        $height = $responsive ? '100%' : (string) $canvasSize;

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="%s" height="%s" viewBox="0 0 %d %d">%s</svg>',
            $width,
            $height,
            $canvasSize,
            $canvasSize,
            implode('', $elements)
        );
    }

    private function availableFrames(): array
    {
        return [
            [
                'id' => 'botanical',
                'label' => 'Cadre botanique',
                'path' => 'assets/illustration-qr-frame.png',
            ],
            [
                'id' => 'bubble',
                'label' => 'Cadre bulle',
                'path' => 'assets/illustration-qr-frame2.png',
            ],
        ];
    }

    private function framePath(string $frameId): ?string
    {
        foreach ($this->availableFrames() as $frame) {
            if ($frame['id'] === $frameId) {
                return public_path($frame['path']);
            }
        }

        return null;
    }

    private function extractSvgContent(string $svg): string
    {
        $svg = preg_replace('/<\?xml.*?\?>/s', '', $svg);
        if (!$svg) {
            return '';
        }

        if (preg_match('/<svg[^>]*>(.*?)<\/svg>/s', $svg, $matches)) {
            return trim($matches[1]);
        }

        return $svg;
    }

    private function toDataUri(string $path): ?string
    {
        if (!is_file($path)) {
            return null;
        }

        $mime = mime_content_type($path);
        if (!$mime) {
            return null;
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            return null;
        }

        return 'data:' . $mime . ';base64,' . base64_encode($contents);
    }
}

