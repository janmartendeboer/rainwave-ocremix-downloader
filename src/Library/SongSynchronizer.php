<?php

declare(strict_types=1);

namespace Johmanx10\Library;

use Johmanx10\Ocremix\Downloader;
use Johmanx10\Rainwave\Api\SongListenerInterface;
use Johmanx10\Rainwave\Music\Song;
use League\Flysystem\Filesystem;
use Symfony\Component\Console\Style\SymfonyStyle;

class SongSynchronizer implements SongListenerInterface
{
    public function __construct(
        private Downloader $downloader,
        private Filesystem $filesystem,
        private SymfonyStyle $output
    ) {}

    public function __invoke(Song $song): void
    {
        $remix = basename($song->url);
        $mainFile = sprintf('remixes/%s', $remix);

        $this->filesystem->createDirectory('remixes');
        $this->filesystem->createDirectory('albums');

        $this->output->section(
            sprintf(
                'Syncing %s %s',
                $remix,
                $song->url
            )
        );

        if (strpos($remix, 'OCR') !== 0) {
            $this->output->error('URL structure is not supported!');
            return;
        }

        if ($this->filesystem->fileExists($mainFile)) {
            $this->output->success(
                sprintf('Exists: %s', $mainFile)
            );
            return;
        }

        $audioFile = $this->downloader->downloadRemix($remix);

        if ($audioFile->getStatusCode() !== 200) {
            $this->output->warning('Could not download remix');
            return;
        }

        $mirrors = (array)$audioFile->getHeader(Downloader::HEADER_MIRRORS);

        if (count($mirrors) === 0) {
            $this->output->warning('No mirrors found');
            return;
        }

        [$url] = $mirrors;
        $fileName = basename($url);
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);

        $this->output->comment(
            sprintf('Preparing to add %s to the library', $fileName)
        );

        $fileContents = $audioFile->getBody()->getContents();

        foreach ($song->albums as $album) {
            $path = sprintf(
                'albums/%s/%s.%s',
                $album->name,
                $song->title,
                $extension
            );

            if ($this->filesystem->fileExists($path)) {
                $this->output->warning(
                    sprintf('Skipping pre-existing file: %s', $path)
                );
                continue;
            }

            $this->filesystem->createDirectory(dirname($path));
            $this->output->comment(sprintf('Writing to %s', $path));
            $this->filesystem->write($path, $fileContents);
        }

        $this->filesystem->write($mainFile, '');
        $this->output->success('Synced');
    }
}
