<?php

namespace Blacklight\processing\post;

use FFMpeg\FFMpeg;
use Blacklight\Nfo;
use Blacklight\NZB;
use FFMpeg\FFProbe;
use Blacklight\NNTP;
use App\Models\Group;
use App\Models\Release;
use App\Models\Category;
use App\Models\Settings;
use Blacklight\ColorCLI;
use Blacklight\Releases;
use Blacklight\NameFixer;
use Blacklight\Categorize;
use App\Models\ReleaseFile;
use Blacklight\ReleaseExtra;
use Blacklight\ReleaseImage;
use Blacklight\SphinxSearch;
use FFMpeg\Format\Video\Ogg;
use Mhor\MediaInfo\MediaInfo;
use Blacklight\utility\Utility;
use dariusiii\rarinfo\Par2Info;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Format\Audio\Vorbis;
use FFMpeg\Coordinate\Dimension;
use dariusiii\rarinfo\ArchiveInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use FFMpeg\Filters\Video\ResizeFilter;

class ProcessAdditional
{
    /**
     * How many compressed (rar/zip) files to check.
     *
     * @var int
     */
    public const maxCompressedFilesToCheck = 20;

    /**
     * @var \PDO
     */
    public $pdo;

    /**
     * @var bool
     */
    protected $_echoDebug;

    /**
     * @var
     */
    protected $_releases;

    /**
     * Count of releases to work on.
     * @var int
     */
    protected $_totalReleases;

    /**
     * @var
     */
    protected $_release;

    /**
     * @var \Blacklight\NZB
     */
    protected $_nzb;

    /**
     * List of files with sizes/etc contained in the NZB.
     * @var array
     */
    protected $_nzbContents;

    /**
     * @var \dariusiii\rarinfo\Par2Info
     */
    protected $_par2Info;

    /**
     * @var \dariusiii\rarinfo\ArchiveInfo
     */
    protected $_archiveInfo;

    /**
     * @var bool|null|string
     */
    protected $_innerFileBlacklist;

    /**
     * @var int
     */
    protected $_maxNestedLevels;

    /**
     * @var null|string
     */
    protected $_7zipPath;

    /**
     * @var null|string
     */
    protected $_unrarPath;

    /**
     * @var string
     */
    protected $_killString;

    /**
     * @var bool|string
     */
    protected $_showCLIReleaseID;

    /**
     * @var int
     */
    protected $_queryLimit;

    /**
     * @var int
     */
    protected $_segmentsToDownload;

    /**
     * @var int
     */
    protected $_maximumRarSegments;

    /**
     * @var int
     */
    protected $_maximumRarPasswordChecks;

    /**
     * @var string
     */
    protected $_maxSize;

    /**
     * @var string
     */
    protected $_minSize;

    /**
     * @var bool
     */
    protected $_processThumbnails;

    /**
     * @var string
     */
    protected $_audioSavePath;

    /**
     * @var string
     */
    protected $_supportFileRegex;

    /**
     * @var bool
     */
    protected $_echoCLI;

    /**
     * @var \Blacklight\NNTP
     */
    protected $_nntp;

    /**
     * @var \Blacklight\Categorize
     */
    protected $_categorize;

    /**
     * @var \Blacklight\NameFixer
     */
    protected $_nameFixer;

    /**
     * @var \Blacklight\ReleaseExtra
     */
    protected $_releaseExtra;

    /**
     * @var \Blacklight\ReleaseImage
     */
    protected $_releaseImage;

    /**
     * @var \Blacklight\Nfo
     */
    protected $_nfo;

    /**
     * @var bool
     */
    protected $_extractUsingRarInfo;

    /**
     * @var bool
     */
    protected $_alternateNNTP;

    /**
     * @var int
     */
    protected $_ffMPEGDuration;

    /**
     * @var bool
     */
    protected $_addPAR2Files;

    /**
     * @var bool
     */
    protected $_processVideo;

    /**
     * @var bool
     */
    protected $_processJPGSample;

    /**
     * @var bool
     */
    protected $_processAudioSample;

    /**
     * @var bool
     */
    protected $_processMediaInfo;

    /**
     * @var bool
     */
    protected $_processAudioInfo;

    /**
     * @var bool
     */
    protected $_processPasswords;

    /**
     * @var string
     */
    protected $_audioFileRegex;

    /**
     * @var string
     */
    protected $_ignoreBookRegex;

    /**
     * @var string
     */
    protected $_videoFileRegex;

    /**
     * Have we created a video file for the current release?
     * @var bool
     */
    protected $_foundVideo;

    /**
     * Have we found MediaInfo data for a Video for the current release?
     * @var bool
     */
    protected $_foundMediaInfo;

    /**
     * Have we found MediaInfo data for a Audio file for the current release?
     * @var bool
     */
    protected $_foundAudioInfo;

    /**
     * Have we created a short Audio file sample for the current release?
     * @var bool
     */
    protected $_foundAudioSample;

    /**
     * Extension of the found audio file (MP3/FLAC/etc).
     * @var string
     */
    protected $_AudioInfoExtension;

    /**
     * Have we downloaded a JPG file for the current release?
     * @var bool
     */
    protected $_foundJPGSample;

    /**
     * Have we created a Video JPG image sample for the current release?
     * @var bool
     */
    protected $_foundSample;

    /**
     * Have we found PAR2 info on this release?
     * @var bool
     */
    protected $_foundPAR2Info;

    /**
     * Message ID's for found content to download.
     * @var array
     */
    protected $_sampleMessageIDs;
    protected $_JPGMessageIDs;
    protected $_MediaInfoMessageIDs;
    protected $_AudioInfoMessageIDs;
    protected $_RARFileMessageIDs;

    /**
     * Password status of the current release.
     * @var array
     */
    protected $_passwordStatus;

    /**
     * Does the current release have a password?
     * @var bool
     */
    protected $_releaseHasPassword;

    /**
     * Does the current release have an NFO file?
     * @var bool
     */
    protected $_releaseHasNoNFO;

    /**
     * Name of the current release's usenet group.
     * @var string
     */
    protected $_releaseGroupName;

    /**
     * Number of file information added to DB (from rar/zip/par2 contents).
     * @var int
     */
    protected $_addedFileInfo;

    /**
     * Number of file information we found from RAR/ZIP.
     * (if some of it was already in DB, this count goes up, while the count above does not).
     * @var int
     */
    protected $_totalFileInfo;

    /**
     * How many compressed (rar/zip) files have we checked.
     * @var int
     */
    protected $_compressedFilesChecked;

    /**
     * Should we download the last rar?
     * @var bool
     */
    protected $_fetchLastFiles;

    /**
     * Are we downloading the last rar?
     * @var bool
     */
    protected $_reverse;

    /**
     * @var \Blacklight\SphinxSearch
     */
    protected $sphinx;

    /**
     * @var \FFMpeg\FFMpeg
     */
    private $ffmpeg;

    /**
     * @var \FFMpeg\FFProbe
     */
    private $ffprobe;

    /**
     * @var \Mhor\MediaInfo\MediaInfo
     */
    private $mediaInfo;

    /**
     * ProcessAdditional constructor.
     *
     * @param array $options
     * @throws \Exception
     */
    public function __construct(array $options = [])
    {
        $defaults = [
            'Echo'         => false,
            'Categorize'   => null,
            'Groups'       => null,
            'NameFixer'    => null,
            'Nfo'          => null,
            'NNTP'         => null,
            'NZB'          => null,
            'ReleaseExtra' => null,
            'ReleaseImage' => null,
            'Settings'     => null,
            'SphinxSearch' => null,
        ];
        $options += $defaults;

        $this->_echoCLI = ($options['Echo'] && config('nntmux.echocli') && (strtolower(PHP_SAPI) === 'cli'));

        $this->_nntp = $options['NNTP'] instanceof NNTP ? $options['NNTP'] : new NNTP(['Echo' => $this->_echoCLI]);

        $this->_nzb = $options['NZB'] instanceof NZB ? $options['NZB'] : new NZB();
        $this->_archiveInfo = new ArchiveInfo();
        $this->_categorize = $options['Categorize'] instanceof Categorize ? $options['Categorize'] : new Categorize();
        $this->_nameFixer = $options['NameFixer'] instanceof NameFixer ? $options['NameFixer'] : new NameFixer(['Echo' =>$this->_echoCLI, 'Groups' => null, 'Categorize' => $this->_categorize]);
        $this->_releaseExtra = $options['ReleaseExtra'] instanceof ReleaseExtra ? $options['ReleaseExtra'] : new ReleaseExtra();
        $this->_releaseImage = $options['ReleaseImage'] instanceof ReleaseImage ? $options['ReleaseImage'] : new ReleaseImage();
        $this->_par2Info = new Par2Info();
        $this->_nfo = $options['Nfo'] instanceof Nfo ? $options['Nfo'] : new Nfo();
        $this->sphinx = $options['SphinxSearch'] instanceof SphinxSearch ? $options['SphinxSearch'] : new SphinxSearch();
        $this->ffmpeg = FFMpeg::create(['timeout' => Settings::settingValue('..timeoutseconds')]);
        $this->ffprobe = FFProbe::create();
        $this->mediaInfo = new MediaInfo();
        $this->mediaInfo->setConfig('use_oldxml_mediainfo_output_format', true);
        $this->mediaInfo->setConfig('command', Settings::settingValue('apps..mediainfopath'));

        $this->_innerFileBlacklist = Settings::settingValue('indexer.ppa.innerfileblacklist') === '' ? false : Settings::settingValue('indexer.ppa.innerfileblacklist');
        $this->_maxNestedLevels = (int) Settings::settingValue('..maxnestedlevels') === 0 ? 3 : (int) Settings::settingValue('..maxnestedlevels');
        $this->_extractUsingRarInfo = (int) Settings::settingValue('..extractusingrarinfo') !== 0;
        $this->_fetchLastFiles = (int) Settings::settingValue('archive.fetch.end') !== 0;

        $this->_7zipPath = false;
        $this->_unrarPath = false;

        // Pass the binary extractors to ArchiveInfo.
        $clients = [];
        if (Settings::settingValue('apps..unrarpath') !== '') {
            $clients += [ArchiveInfo::TYPE_RAR => Settings::settingValue('apps..unrarpath')];
            $this->_unrarPath = Settings::settingValue('apps..unrarpath');
        }
        if (Settings::settingValue('apps..zippath') !== '') {
            $clients += [ArchiveInfo::TYPE_ZIP => Settings::settingValue('apps..zippath')];
            $this->_7zipPath = Settings::settingValue('apps..zippath');
        }
        $this->_archiveInfo->setExternalClients($clients);

        $this->_killString = '"';
        if (Settings::settingValue('apps..timeoutpath') !== '' && (int) Settings::settingValue('..timeoutseconds') > 0) {
            $this->_killString = (
                '"'.Settings::settingValue('apps..timeoutpath').
                '" --foreground --signal=KILL '.
                Settings::settingValue('..timeoutseconds').' "'
            );
        }

        $this->_showCLIReleaseID = (PHP_BINARY.' '.__DIR__.DS.'ProcessAdditional.php ReleaseID: ');

        // Maximum amount of releases to fetch per run.
        $this->_queryLimit =
            (Settings::settingValue('..maxaddprocessed') !== '') ? (int) Settings::settingValue('..maxaddprocessed') : 25;

        // Maximum message ID's to download per file type in the NZB (video, jpg, etc).
        $this->_segmentsToDownload =
            (Settings::settingValue('..segmentstodownload') !== '') ? (int) Settings::settingValue('..segmentstodownload') : 2;

        // Maximum message ID's to download for a RAR file.
        $this->_maximumRarSegments =
            (Settings::settingValue('..maxpartsprocessed') !== '') ? (int) Settings::settingValue('..maxpartsprocessed') : 3;

        // Maximum RAR files to check for a password before stopping.
        $this->_maximumRarPasswordChecks =
            (Settings::settingValue('..passchkattempts') !== '') ? (int) Settings::settingValue('..passchkattempts') : 1;

        $this->_maximumRarPasswordChecks = ($this->_maximumRarPasswordChecks < 1 ? 1 : $this->_maximumRarPasswordChecks);

        // Maximum size of releases in GB.
        $this->_maxSize =
            (Settings::settingValue('..maxsizetopostprocess') !== '') ? (int) Settings::settingValue('..maxsizetopostprocess') : 100;
        $this->_maxSize = ($this->_maxSize > 0 ? ('AND r.size < '.($this->_maxSize * 1073741824)) : '');
        // Minimum size of releases in MB.
        $this->_minSize =
            (Settings::settingValue('..minsizetopostprocess') !== '') ? (int) Settings::settingValue('..minsizetopostprocess') : 100;
        $this->_minSize = ($this->_minSize > 0 ? ('AND r.size > '.($this->_minSize * 1048576)) : '');

        // Use the alternate NNTP provider for downloading Message-ID's ?
        $this->_alternateNNTP = (int) Settings::settingValue('..alternate_nntp') === 1;

        $this->_ffMPEGDuration = Settings::settingValue('..ffmpeg_duration') !== '' ? (int) Settings::settingValue('..ffmpeg_duration') : 5;

        $this->_addPAR2Files = (int) Settings::settingValue('..addpar2') !== 0;

        if (! Settings::settingValue('apps..ffmpegpath')) {
            $this->_processAudioSample = $this->_processThumbnails = $this->_processVideo = false;
        } else {
            $this->_processAudioSample = (int) Settings::settingValue('..saveaudiopreview') !== 0;
            $this->_processThumbnails = (int) Settings::settingValue('..processthumbnails') !== 0;
            $this->_processVideo = (int) Settings::settingValue('..processvideos') !== 0;
        }

        $this->_processJPGSample = (int) Settings::settingValue('..processjpg') !== 0;
        $this->_processMediaInfo = Settings::settingValue('apps..mediainfopath') !== '';
        $this->_processAudioInfo = $this->_processMediaInfo;
        $this->_processPasswords = (
            ((int) Settings::settingValue('..checkpasswordedrar') !== 0) &&
            Settings::settingValue('apps..unrarpath') !== ''
        );

        $this->_audioSavePath = NN_COVERS.'audiosample'.DS;

        $this->_audioFileRegex = '\.(AAC|AIFF|APE|AC3|ASF|DTS|FLAC|MKA|MKS|MP2|MP3|RA|OGG|OGM|W64|WAV|WMA)';
        $this->_ignoreBookRegex = '/\b(epub|lit|mobi|pdf|sipdf|html)\b.*\.rar(?!.{20,})/i';
        $this->_supportFileRegex = '/\.(vol\d{1,3}\+\d{1,3}|par2|srs|sfv|nzb';
        $this->_videoFileRegex = '\.(AVI|F4V|IFO|M1V|M2V|M4V|MKV|MOV|MP4|MPEG|MPG|MPGV|MPV|OGV|QT|RM|RMVB|TS|VOB|WMV)';
    }

    /**
     * Clear out the main temp path when done.
     */
    public function __destruct()
    {
        $this->_clearMainTmpPath();
    }

    /**
     * @param string $groupID
     * @param string $guidChar
     *
     * @throws \Exception
     */
    public function start($groupID = '', $guidChar = ''): void
    {
        $this->_setMainTempPath($guidChar, $groupID);

        // Fetch all the releases to work on.
        $this->_fetchReleases($groupID, $guidChar);

        // Check if we have releases to work on.
        if ($this->_totalReleases > 0) {
            // Echo start time and process description.
            $this->_echoDescription();

            $this->_processReleases();
        }
    }

    /**
     * @var string Main temp path to work on.
     */
    protected $_mainTmpPath;

    /**
     * @var string Temp path for current release.
     */
    protected $tmpPath;

    /**
     * @param $guidChar
     * @param string $groupID
     * @throws \RuntimeException
     * @throws \Exception
     */
    protected function _setMainTempPath(&$guidChar, &$groupID = ''): void
    {
        // Set up the temporary files folder location.
        $this->_mainTmpPath = (string) Settings::settingValue('..tmpunrarpath');

        // Check if it ends with a dir separator.
        if (! preg_match('/[\/\\\\]$/', $this->_mainTmpPath)) {
            $this->_mainTmpPath .= DS;
        }

        // If we are doing per group, use the groupID has a inner path, so other scripts don't delete the files we are working on.
        if ($groupID !== '') {
            $this->_mainTmpPath .= ($groupID.DS);
        } elseif ($guidChar !== '') {
            $this->_mainTmpPath .= ($guidChar.DS);
        }

        if (! is_dir($this->_mainTmpPath)) {
            $old = umask(0777);
            if (! File::makeDirectory($this->_mainTmpPath, 0777, true, true) && ! is_dir($this->_mainTmpPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->_mainTmpPath));
            }
            @chmod($this->_mainTmpPath, 0777);
            @umask($old);
        }

        if (! is_dir($this->_mainTmpPath)) {
            throw new \RuntimeException('Could not create the tmpunrar folder ('.$this->_mainTmpPath.')');
        }

        $this->_clearMainTmpPath();

        $this->tmpPath = $this->_mainTmpPath;
    }

    /**
     * Clear out old folders/files from the main temp folder.
     */
    protected function _clearMainTmpPath(): void
    {
        if ($this->_mainTmpPath !== '') {
            $this->_recursivePathDelete(
                $this->_mainTmpPath,
                // These are folders we don't want to delete.
                [
                    // This is the actual temp folder.
                    $this->_mainTmpPath,
                ]
            );
        }
    }

    /**
     * Get all releases that need to be processed.
     *
     * @param int|string $groupID
     * @param string     $guidChar
     *
     * @void
     */
    protected function _fetchReleases($groupID, &$guidChar): void
    {
        $this->_releases = DB::select(
            sprintf(
                '
				SELECT r.id, r.id AS releases_id, r.guid, r.name, r.size, r.groups_id, r.nfostatus,
					r.fromname, r.completion, r.categories_id, r.searchname, r.predb_id,
					c.disablepreview
				FROM releases r
				LEFT JOIN categories c ON c.id = r.categories_id
				WHERE r.nzbstatus = 1
				%s %s %s %s
				AND r.passwordstatus BETWEEN -6 AND -1
				AND r.haspreview = -1
				AND c.disablepreview = 0
				ORDER BY r.passwordstatus ASC, r.postdate DESC
				LIMIT %d',
                $this->_maxSize,
                $this->_minSize,
                ($groupID === '' ? '' : 'AND r.groups_id = '.$groupID),
                ($guidChar === '' ? '' : 'AND r.leftguid = '.escapeString($guidChar)),
                $this->_queryLimit
            )
        );

        $this->_totalReleases = \count($this->_releases);
    }

    /**
     * Output the description and start time.
     *
     * @void
     */
    protected function _echoDescription(): void
    {
        if ($this->_totalReleases > 1 && $this->_echoCLI) {
            $this->_echo(
                PHP_EOL.
                'Additional post-processing, started at: '.
                now()->format('D M d, Y G:i a').
                PHP_EOL.
                'Downloaded: (xB) = yEnc article, f= Failed ;Processing: z = ZIP file, r = RAR file'.
                PHP_EOL.
                'Added: s = Sample image, j = JPEG image, A = Audio sample, a = Audio MediaInfo, v = Video sample'.
                PHP_EOL.
                'Added: m = Video MediaInfo, n = NFO, ^ = File details from inside the RAR/ZIP',
                'header'
            );
        }
    }

    /**
     * Loop through the releases, processing them 1 at a time.
     *
     * @throws \RuntimeException
     * @throws \Exception
     */
    protected function _processReleases(): void
    {
        foreach ($this->_releases as $this->_release) {
            $this->_echo(
                PHP_EOL.'['.$this->_release->id.']['.
                human_filesize($this->_release->size, 1).']',
                'primaryOver'
            );

            cli_set_process_title($this->_showCLIReleaseID.$this->_release->id);

            // Create folder to store temporary files.
            if ($this->_createTempFolder() === false) {
                continue;
            }

            // Get NZB contents.
            if ($this->_getNZBContents() === false) {
                continue;
            }

            // Reset the current release variables.
            $this->_resetReleaseStatus();

            // Go through the files in the NZB, get the amount of book files.
            $totalBooks = $this->_processNZBContents();

            // Check if this NZB is a large collection of books.
            $bookFlood = false;
            if ($totalBooks > 80 && ($totalBooks * 2) >= \count($this->_nzbContents)) {
                $bookFlood = true;
            }

            if ($this->_processPasswords === true ||
                $this->_processThumbnails === true ||
                $this->_processMediaInfo === true ||
                $this->_processAudioInfo === true ||
                $this->_processVideo === true
            ) {

                // Process usenet Message-ID downloads.
                $this->_processMessageIDDownloads();

                // Process compressed (RAR/ZIP) files inside the NZB.
                if ($bookFlood === false && $this->_NZBHasCompressedFile) {
                    // Download the RARs/ZIPs, extract the files inside them and insert the file info into the DB.
                    $this->_processNZBCompressedFiles();

                    // Download rar/zip in reverse order, to get the last rar or zip file.
                    if ($this->_fetchLastFiles === true) {
                        $this->_processNZBCompressedFiles(true);
                    }

                    if ($this->_releaseHasPassword === false) {
                        // Process the extracted files to get video/audio samples/etc.
                        $this->_processExtractedFiles();
                    }
                }
            }

            // Update the release to say we processed it.
            $this->_finalizeRelease();

            // Delete all files / folders for this release.
            $this->_recursivePathDelete($this->tmpPath);
        }
        if ($this->_echoCLI) {
            echo PHP_EOL;
        }
    }

    /**
     * Deletes files and folders recursively.
     *
     * @param string $path          Path to a folder or file.
     * @param string[] $ignoredFolders array with paths to folders to ignore.
     *
     * @void
     */
    protected function _recursivePathDelete($path, $ignoredFolders = []): void
    {
        if (is_dir($path)) {
            if (\in_array($path, $ignoredFolders, false)) {
                return;
            }
            foreach (File::files($path) as $file) {
                $this->_recursivePathDelete($file, $ignoredFolders);
            }

            File::deleteDirectory($path);
        } elseif (is_file($path)) {
            File::delete($path);
        }
    }

    /**
     * Create a temporary storage folder for the current release.
     *
     * @return bool
     * @throws \RuntimeException
     */
    protected function _createTempFolder(): bool
    {
        // Per release defaults.
        $this->tmpPath = $this->_mainTmpPath.$this->_release->guid.DS;
        if (! is_dir($this->tmpPath)) {
            $old = umask(0777);
            if (! mkdir($this->tmpPath) && ! is_dir($this->tmpPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->tmpPath));
            }
            @chmod($this->tmpPath, 0777);
            @umask($old);

            if (! \is_dir($this->tmpPath)) {
                $this->_echo('Unable to create directory: '.$this->tmpPath, 'warning');

                return $this->_decrementPasswordStatus();
            }
        }

        return true;
    }

    /**
     * Get list of contents inside a release's NZB file.
     *
     * @return bool
     */
    protected function _getNZBContents(): bool
    {
        $nzbPath = $this->_nzb->NZBPath($this->_release->guid);
        if ($nzbPath === false) {
            $this->_echo('NZB not found for GUID: '.$this->_release->guid, 'warning');

            return $this->_decrementPasswordStatus();
        }

        $nzbContents = Utility::unzipGzipFile($nzbPath);
        if (! $nzbContents) {
            $this->_echo('NZB is empty or broken for GUID: '.$this->_release->guid, 'warning');

            return $this->_decrementPasswordStatus();
        }

        // Get a list of files in the nzb.
        $this->_nzbContents = $this->_nzb->nzbFileList($nzbContents, ['no-file-key' => false, 'strip-count' => true]);
        if (\count($this->_nzbContents) === 0) {
            $this->_echo('NZB is potentially broken for GUID: '.$this->_release->guid, 'warning');

            return $this->_decrementPasswordStatus();
        }
        // Sort keys.
        ksort($this->_nzbContents, SORT_NATURAL);

        return true;
    }

    /**
     * Decrement password status for the current release.
     *
     * @param bool $return Return value.
     *
     * @return bool
     */
    protected function _decrementPasswordStatus($return = false): bool
    {
        Release::query()->where('id', $this->_release->id)->decrement('passwordstatus');

        return $return;
    }

    /**
     * Current file we are working on inside a NZB.
     * @var array
     */
    protected $_currentNZBFile;

    /**
     * Does the current NZB contain a compressed (RAR/ZIP) file?
     * @var bool
     */
    protected $_NZBHasCompressedFile;

    /**
     * Process the files inside the NZB, find Message-ID's to download.
     * If we find files with book extensions, return the amount.
     *
     * @return int
     */
    protected function _processNZBContents(): int
    {
        $totalBookFiles = 0;
        foreach ($this->_nzbContents as $this->_currentNZBFile) {

            // Check if it's not a nfo, nzb, par2 etc...
            if (preg_match($this->_supportFileRegex.'|nfo\b|inf\b|ofn\b)($|[ ")\]-])(?!.{20,})/i', $this->_currentNZBFile['title'])) {
                continue;
            }

            // Check if it's a rar/zip.
            if ($this->_NZBHasCompressedFile === false &&
                preg_match(
                    '/\.(part\d+|r\d+|rar|0+|0*10?|zipr\d{2,3}|zipx?)(\s*\.rar)*($|[ ")\]-])|"[a-f0-9]{32}\.[1-9]\d{1,2}".*\(\d+\/\d{2,}\)$/i',
                    $this->_currentNZBFile['title']
                )
            ) {
                $this->_NZBHasCompressedFile = true;
            }

            // Look for a video sample, make sure it's not an image.
            if ($this->_processThumbnails === true && empty($this->_sampleMessageIDs) && stripos($this->_currentNZBFile['title'], 'sample') !== false && ! preg_match('/\.jpe?g$/i', $this->_currentNZBFile['title']) && isset($this->_currentNZBFile['segments'])
            ) {
                // Get the amount of segments for this file.
                $segCount = (\count($this->_currentNZBFile['segments']) - 1);
                // If it's more than 1 try to get up to the site specified value of segments.
                for ($i = 0; $i < $this->_segmentsToDownload; $i++) {
                    if ($i > $segCount) {
                        break;
                    }
                    $this->_sampleMessageIDs[] = (string) $this->_currentNZBFile['segments'][$i];
                }
            }

            // Look for a JPG picture, make sure it's not a CD cover.
            if ($this->_processJPGSample === true && empty($this->_JPGMessageIDs) && ! preg_match('/flac|lossless|mp3|music|inner-sanctum|sound/i', $this->_releaseGroupName) && preg_match('/\.jpe?g[. ")\]]/i', $this->_currentNZBFile['title']) && isset($this->_currentNZBFile['segments'])
            ) {
                // Get the amount of segments for this file.
                $segCount = (\count($this->_currentNZBFile['segments']) - 1);
                // If it's more than 1 try to get up to the site specified value of segments.
                for ($i = 0; $i < $this->_segmentsToDownload; $i++) {
                    if ($i > $segCount) {
                        break;
                    }
                    $this->_JPGMessageIDs[] = (string) $this->_currentNZBFile['segments'][$i];
                }
            }

            // Look for a video file, make sure it's not a sample, for MediaInfo.
            if ($this->_processMediaInfo === true && empty($this->_MediaInfoMessageIDs) && stripos($this->_currentNZBFile['title'], 'sample') !== false && preg_match('/'.$this->_videoFileRegex.'[. ")\]]/i', $this->_currentNZBFile['title']) && isset($this->_currentNZBFile['segments'][0])
            ) {
                $this->_MediaInfoMessageIDs = (string) $this->_currentNZBFile['segments'][0];
            }

            // Look for a audio file.
            if ($this->_processAudioInfo === true && empty($this->_AudioInfoMessageIDs) && preg_match('/'.$this->_audioFileRegex.'[. ")\]]/i', $this->_currentNZBFile['title'], $type) && isset($this->_currentNZBFile['segments'])
            ) {
                // Get the extension.
                $this->_AudioInfoExtension = $type[1];
                $this->_AudioInfoMessageIDs = (string) $this->_currentNZBFile['segments'][0];
            }

            // Some releases contain many books, increment this to ignore them later.
            if (preg_match($this->_ignoreBookRegex, $this->_currentNZBFile['title'])) {
                $totalBookFiles++;
            }
        }

        return $totalBookFiles;
    }

    /**
     * List of message-id's we have tried for rar/zip files.
     * @var array
     */
    protected $_triedCompressedMids = [];

    /**
     * @param bool $reverse
     * @throws \Exception
     */
    protected function _processNZBCompressedFiles($reverse = false): void
    {
        $this->_reverse = $reverse;

        if ($this->_reverse) {
            if (! krsort($this->_nzbContents)) {
                return;
            }
        } else {
            $this->_triedCompressedMids = [];
        }

        $failed = $downloaded = 0;
        // Loop through the files, attempt to find if password-ed and files. Starting with what not to process.
        foreach ($this->_nzbContents as $nzbFile) {
            // TODO change this to max calculated size, as segments vary in size greatly.
            if ($downloaded >= $this->_maximumRarSegments) {
                break;
            }

            if ($failed >= $this->_maximumRarPasswordChecks) {
                break;
            }

            if ($this->_releaseHasPassword === true) {
                $this->_echo('Skipping processing of rar '.$nzbFile['title'].' it has a password.', 'primaryOver');
                break;
            }

            // Probably not a rar/zip.
            if (! preg_match(
                '/\.(part\d+|r\d+|rar|0+|0*10?|zipr\d{2,3}|zipx?)(\s*\.rar)*($|[ ")\]-])|"[a-f0-9]{32}\.[1-9]\d{1,2}".*\(\d+\/\d{2,}\)$/i',
                $nzbFile['title']
            )
            ) {
                continue;
            }

            // Get message-id's for the rar file.
            $segCount = (\count($nzbFile['segments']) - 1);
            $mID = [];
            for ($i = 0; $i < $this->_maximumRarSegments; $i++) {
                if ($i > $segCount) {
                    break;
                }
                $segment = (string) $nzbFile['segments'][$i];
                if (! $this->_reverse) {
                    $this->_triedCompressedMids[] = $segment;
                } elseif (\in_array($segment, $this->_triedCompressedMids, false)) {
                    // We already downloaded this file.
                    continue 2;
                }
                $mID[] = $segment;
            }
            // Nothing to download.
            if (empty($mID)) {
                continue;
            }

            // Download the article(s) from usenet.
            $fetchedBinary = $this->_nntp->getMessages($this->_releaseGroupName, $mID, $this->_alternateNNTP);
            if ($this->_nntp->isError($fetchedBinary)) {
                $fetchedBinary = false;
            }

            if ($fetchedBinary !== false) {

                // Echo we downloaded compressed file.
                if ($this->_echoCLI) {
                    $this->_echo('(cB)', 'primaryOver');
                }

                $downloaded++;

                // Process the compressed file.
                $decompressed = $this->_processCompressedData($fetchedBinary);

                if ($decompressed === true || $this->_releaseHasPassword === true) {
                    break;
                }
            } else {
                $failed++;
                if ($this->_echoCLI) {
                    $this->_echo('f('.$failed.')', 'warningOver');
                }
            }
        }
    }

    /**
     * Check if the data is a ZIP / RAR file, extract files, get file info.
     *
     * @param string $compressedData
     *
     * @return bool
     * @throws \Exception
     */
    protected function _processCompressedData(&$compressedData): bool
    {
        $this->_compressedFilesChecked++;
        // Give the data to archive info so it can check if it's a rar.
        if ($this->_archiveInfo->setData($compressedData, true) === false) {
            $this->_debug('Data is probably not RAR or ZIP.');

            return false;
        }

        // Check if there's an error.
        if ($this->_archiveInfo->error !== '') {
            $this->_debug('ArchiveInfo Error: '.$this->_archiveInfo->error);

            return false;
        }

        // Get a summary of the compressed file.
        $dataSummary = $this->_archiveInfo->getSummary(true);

        // Check if the compressed file is encrypted.
        if (! empty($this->_archiveInfo->isEncrypted) || (isset($dataSummary['is_encrypted']) && (int) $dataSummary['is_encrypted'] !== 0)) {
            $this->_debug('ArchiveInfo: Compressed file has a password.');
            $this->_releaseHasPassword = true;
            $this->_passwordStatus[] = Releases::PASSWD_RAR;

            return false;
        }

        switch ($dataSummary['main_type']) {
            case ArchiveInfo::TYPE_RAR:
                if ($this->_echoCLI) {
                    $this->_echo('r', 'primaryOver');
                }

                if ($this->_extractUsingRarInfo === false && $this->_unrarPath !== false) {
                    $fileName = $this->tmpPath.uniqid('', true).'.rar';
                    file_put_contents($fileName, $compressedData);
                    runCmd(
                        $this->_killString.$this->_unrarPath.
                        '" e -ai -ep -c- -id -inul -kb -or -p- -r -y "'.
                        $fileName.'" "'.$this->tmpPath.'unrar/"'
                    );
                    unlink($fileName);
                }
                break;
            case ArchiveInfo::TYPE_ZIP:
                if ($this->_echoCLI) {
                    $this->_echo('z', 'primaryOver');
                }

                if ($this->_extractUsingRarInfo === false && $this->_7zipPath !== false) {
                    $fileName = $this->tmpPath.uniqid('', true).'.zip';
                    file_put_contents($fileName, $compressedData);
                    runCmd(
                        $this->_killString.$this->_7zipPath.'" x "'.
                        $fileName.'" -bd -y -o"'.$this->tmpPath.'unzip/"'
                    );
                    unlink($fileName);
                }
                break;
            default:
                return false;
        }

        return $this->_processCompressedFileList();
    }

    /**
     * Get a list of all files in the compressed file, add the file info to the DB.
     *
     * @return bool
     * @throws \Exception
     */
    protected function _processCompressedFileList(): bool
    {
        // Get a list of files inside the Compressed file.
        $files = $this->_archiveInfo->getArchiveFileList();
        if (! \is_array($files) || \count($files) === 0) {
            return false;
        }

        // Loop through the files.
        foreach ($files as $file) {
            if ($this->_releaseHasPassword === true) {
                break;
            }

            if (isset($file['name'])) {
                if (isset($file['error'])) {
                    $this->_debug("Error: {$file['error']} (in: {$file['source']})");
                    continue;
                }

                if (isset($file['pass']) && $file['pass'] === true) {
                    $this->_releaseHasPassword = true;
                    $this->_passwordStatus[] = Releases::PASSWD_RAR;
                    break;
                }

                if ($this->_innerFileBlacklist !== false && preg_match($this->_innerFileBlacklist, $file['name'])) {
                    $this->_releaseHasPassword = true;
                    $this->_passwordStatus[] = Releases::PASSWD_POTENTIAL;
                    break;
                }

                $fileName = [];
                if (preg_match('/[^\/\\\\]*\.[a-zA-Z0-9]*$/', $file['name'], $fileName)) {
                    $fileName = $fileName[0];
                } else {
                    $fileName = '';
                }

                if ($this->_extractUsingRarInfo === true) {
                    // Extract files from the rar.
                    if (isset($file['compressed']) && (int) $file['compressed'] === 0) {
                        @file_put_contents(
                            $this->tmpPath.random_int(10, 999999).'_'.$fileName,
                            $this->_archiveInfo->getFileData($file['name'], $file['source'])
                        );
                    } // If the files are compressed, use a binary extractor.
                    else {
                        $this->_archiveInfo->extractFile($file['name'], $this->tmpPath.random_int(10, 999999).'_'.$fileName);
                    }
                }
            }
            $this->_addFileInfo($file);
        }
        if ($this->_addedFileInfo > 0) {
            $this->sphinx->updateRelease($this->_release->id);
        }

        return $this->_totalFileInfo > 0;
    }

    /**
     * @param $file
     * @throws \Exception
     */
    protected function _addFileInfo(&$file): void
    {
        // Don't add rar/zip files to the DB.
        if (! isset($file['error']) && isset($file['source']) &&
            ! preg_match($this->_supportFileRegex.'|part\d+|r\d{1,3}|zipr\d{2,3}|\d{2,3}|zipx|zip|rar)(\s*\.rar)?$/i', $file['name'])
        ) {

            // Cache the amount of files we find in the RAR or ZIP, return this to say we did find RAR or ZIP content.
            // This is so we don't download more RAR or ZIP files for no reason.
            $this->_totalFileInfo++;

            /* Check if we already have the file or not.
             * Also make sure we don't add too many files, some releases have 100's of files, like PS3 releases.
             */
            if ($this->_addedFileInfo < 11 && ReleaseFile::query()->where(['releases_id' => $this->_release->id, 'name' => $file['name'], 'size' => $file
                ['size'], ])->first() === null) {
                if (ReleaseFile::addReleaseFiles($this->_release->id, $file['name'], '', $file['size'], $file['date'], $file['pass'])) {
                    $this->_addedFileInfo++;

                    if ($this->_echoCLI) {
                        $this->_echo('^', 'primaryOver');
                    }

                    // Check for "codec spam"
                    if (preg_match('/alt\.binaries\.movies($|\.divx$)/', $this->_releaseGroupName) &&
                        preg_match('/[\/\\\\]Codec[\/\\\\]Setup\.exe$/i', $file['name'])
                    ) {
                        $this->_debug('Codec spam found, setting release to potentially passworded.');
                        $this->_releaseHasPassword = true;
                        $this->_passwordStatus[] = Releases::PASSWD_POTENTIAL;
                    } //Run a PreDB filename check on insert to try and match the release
                    elseif (strpos($file['name'], '.') !== 0 && \strlen($file['name']) > 0) {
                        $this->_release['filename'] = $file['name'];
                        $this->_release['releases_id'] = $this->_release->id;
                        $this->_nameFixer->matchPreDbFiles($this->_release, 1, 1, true);
                    }
                }
            }
        }
    }

    /**
     * Go through all the extracted files in the temp folder and process them.
     *
     * @throws \Exception
     */
    protected function _processExtractedFiles(): void
    {
        $nestedLevels = 0;

        // Go through all the files in the temp folder, look for compressed files, extract them and the nested ones.
        while ($nestedLevels < $this->_maxNestedLevels) {

            // Break out if we checked more than x compressed files.
            if ($this->_compressedFilesChecked >= self::maxCompressedFilesToCheck) {
                break;
            }

            $foundCompressedFile = false;

            // Get all the compressed files in the temp folder.
            $files = $this->_getTempDirectoryContents('/.*\.([rz]\d{2,}|rar|zipx?|0{0,2}1)($|[^a-z0-9])/i');

            if ($files instanceof \Traversable) {
                foreach ($files as $file) {

                    // Check if the file exists.
                    if (is_file($file[0])) {
                        $rarData = @file_get_contents($file[0]);
                        if ($rarData !== false) {
                            $this->_processCompressedData($rarData);
                            $foundCompressedFile = true;
                        }
                        @unlink($file[0]);
                    }
                }
            }

            // If we found no compressed files, break out.
            if ($foundCompressedFile === false) {
                break;
            }

            $nestedLevels++;
        }

        $fileType = [];

        // Get all the remaining files in the temp dir.
        $files = $this->_getTempDirectoryContents();
        if ($files instanceof \Traversable) {
            foreach ($files as $file) {
                $file = (string) $file;

                // Skip /. and /..
                if (preg_match('/[\/\\\\]\.{1,2}$/', $file)) {
                    continue;
                }

                if (is_file($file)) {

                    // Process PAR2 files.
                    if ($this->_foundPAR2Info === false && preg_match('/\.par2$/', $file)) {
                        $this->_siftPAR2Info($file);
                    } // Process NFO files.
                    elseif ($this->_releaseHasNoNFO === true && preg_match('/(\.(nfo|inf|ofn)|info\.txt)$/i', $file)) {
                        $this->_processNfoFile($file);
                    } // Process audio files.
                    elseif (
                        ($this->_foundAudioInfo === false ||
                            $this->_foundAudioSample === false) &&
                        preg_match('/(.*)'.$this->_audioFileRegex.'$/i', $file, $fileType)
                    ) {
                        // Try to get audio sample/audio media info.
                        @rename($file, $this->tmpPath.'audiofile.'.$fileType[2]);
                        $this->_getAudioInfo($this->tmpPath.'audiofile.'.$fileType[2], $fileType[2]);
                        @unlink($this->tmpPath.'audiofile.'.$fileType[2]);
                    } // Process JPG files.
                    elseif ($this->_foundJPGSample === false && preg_match('/\.jpe?g$/i', $file)) {
                        $this->_getJPGSample($file);
                        @unlink($file);
                    } // Video sample // video clip // video media info.
                    elseif (($this->_foundSample === false || $this->_foundVideo === false || $this->_foundMediaInfo === false) &&
                        preg_match('/(.*)'.$this->_videoFileRegex.'$/i', $file)
                    ) {
                        $this->_processVideoFile($file);
                    }

                    // Check file's magic info.
                    else {
                        $output = Utility::fileInfo($file);
                        if (! empty($output)) {
                            switch (true) {

                                case $this->_foundJPGSample === false && preg_match('/^JPE?G/i', $output):
                                    $this->_getJPGSample($file);
                                    @unlink($file);
                                    break;

                                case
                                    ($this->_foundMediaInfo === false || $this->_foundSample === false || $this->_foundVideo === false)
                                    && preg_match('/Matroska data|MPEG v4|MPEG sequence, v2|\WAVI\W/i', $output):
                                    $this->_processVideoFile($file);
                                    break;

                                case
                                    ($this->_foundAudioSample === false || $this->_foundAudioInfo === false) &&
                                    preg_match('/^FLAC|layer III|Vorbis audio/i', $output, $fileType):
                                    switch ($fileType[0]) {
                                        case 'FLAC':
                                            $fileType = 'FLAC';
                                            break;
                                        case 'layer III':
                                            $fileType = 'MP3';
                                            break;
                                        case 'Vorbis audio':
                                            $fileType = 'OGG';
                                            break;
                                    }
                                    @rename($file, $this->tmpPath.'audiofile.'.$fileType);
                                    $this->_getAudioInfo($this->tmpPath.'audiofile.'.$fileType, $fileType);
                                    @unlink($this->tmpPath.'audiofile.'.$fileType);
                                    break;

                                case $this->_foundPAR2Info === false && stripos($output, 'Parity') === 0:
                                    $this->_siftPAR2Info($file);
                                    break;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Download all binaries from usenet and form samples / get media info / etc from them.
     *
     * @void
     * @throws \Exception
     */
    protected function _processMessageIDDownloads(): void
    {
        $this->_processSampleMessageIDs();
        $this->_processMediaInfoMessageIDs();
        $this->_processAudioInfoMessageIDs();
        $this->_processJPGMessageIDs();
    }

    /**
     * Download and process binaries for sample videos.
     *
     * @void
     * @throws \Exception
     */
    protected function _processSampleMessageIDs(): void
    {
        // Download and process sample image.
        if ($this->_foundSample === false || $this->_foundVideo === false) {
            if (! empty($this->_sampleMessageIDs)) {

                // Download it from usenet.
                $sampleBinary = $this->_nntp->getMessages($this->_releaseGroupName, $this->_sampleMessageIDs, $this->_alternateNNTP);
                if ($this->_nntp->isError($sampleBinary)) {
                    $sampleBinary = false;
                }

                if ($sampleBinary !== false) {
                    if ($this->_echoCLI) {
                        $this->_echo('(sB)', 'primaryOver');
                    }

                    // Check if it's more than 40 bytes.
                    if (\strlen($sampleBinary) > 40) {
                        $fileLocation = $this->tmpPath.'sample_'.random_int(0, 99999).'.avi';
                        // Try to create the file.
                        @file_put_contents($fileLocation, $sampleBinary);

                        // Try to get a sample picture.
                        if ($this->_foundSample === false) {
                            $this->_foundSample = $this->_getSample($fileLocation);
                        }

                        // Try to get a sample video.
                        if ($this->_foundVideo === false) {
                            $this->_foundVideo = $this->_getVideo($fileLocation);
                        }
                    }
                } elseif ($this->_echoCLI) {
                    $this->_echo('f', 'warningOver');
                }
            }
        }
    }

    /**
     * Download and process binaries for media info from videos.
     *
     * @void
     * @throws \Exception
     */
    protected function _processMediaInfoMessageIDs(): void
    {
        // Download and process mediainfo. Also try to get a sample if we didn't get one yet.
        if ($this->_foundMediaInfo === false || $this->_foundSample === false || $this->_foundVideo === false) {
            if ($this->_foundMediaInfo === false && ! empty($this->_MediaInfoMessageIDs)) {

                // Try to download it from usenet.
                $mediaBinary = $this->_nntp->getMessages($this->_releaseGroupName, $this->_MediaInfoMessageIDs, $this->_alternateNNTP);
                if ($this->_nntp->isError($mediaBinary)) {
                    // If error set it to false.
                    $mediaBinary = false;
                }

                if ($mediaBinary !== false) {
                    if ($this->_echoCLI) {
                        $this->_echo('(mB)', 'primaryOver');
                    }

                    // If it's more than 40 bytes...
                    if (\strlen($mediaBinary) > 40) {
                        $fileLocation = $this->tmpPath.'media.avi';
                        // Create a file on the disk with it.
                        @file_put_contents($fileLocation, $mediaBinary);

                        // Try to get media info.
                        if ($this->_foundMediaInfo === false) {
                            $this->_foundMediaInfo = $this->_getMediaInfo($fileLocation);
                        }

                        // Try to get a sample picture.
                        if ($this->_foundSample === false) {
                            $this->_foundSample = $this->_getSample($fileLocation);
                        }

                        // Try to get a sample video.
                        if ($this->_foundVideo === false) {
                            $this->_foundVideo = $this->_getVideo($fileLocation);
                        }
                    }
                } elseif ($this->_echoCLI) {
                    $this->_echo('f', 'warningOver');
                }
            }
        }
    }

    /**
     * Download and process binaries for media info from songs.
     *
     * @void
     * @throws \Exception
     */
    protected function _processAudioInfoMessageIDs(): void
    {
        // Download audio file, use media info to try to get the artist / album.
        if ($this->_foundAudioInfo === false || $this->_foundAudioSample === false) {
            if (! empty($this->_AudioInfoMessageIDs)) {
                // Try to download it from usenet.
                $audioBinary = $this->_nntp->getMessages($this->_releaseGroupName, $this->_AudioInfoMessageIDs, $this->_alternateNNTP);
                if ($this->_nntp->isError($audioBinary)) {
                    $audioBinary = false;
                }

                if ($audioBinary !== false) {
                    if ($this->_echoCLI) {
                        $this->_echo('(aB)', 'primaryOver');
                    }

                    $fileLocation = $this->tmpPath.'audio.'.$this->_AudioInfoExtension;
                    // Create a file with it.
                    @file_put_contents($fileLocation, $audioBinary);

                    // Try to get media info / sample of the audio file.
                    $this->_getAudioInfo($fileLocation, $this->_AudioInfoExtension);
                } elseif ($this->_echoCLI) {
                    $this->_echo('f', 'warningOver');
                }
            }
        }
    }

    /**
     * Download and process binaries for JPG pictures.
     *
     * @void
     * @throws \Exception
     */
    protected function _processJPGMessageIDs(): void
    {
        // Download JPG file.
        if ($this->_foundJPGSample === false && ! empty($this->_JPGMessageIDs)) {

            // Try to download it.
            $jpgBinary = $this->_nntp->getMessages($this->_releaseGroupName, $this->_JPGMessageIDs, $this->_alternateNNTP);
            if ($this->_nntp->isError($jpgBinary)) {
                $jpgBinary = false;
            }

            if ($jpgBinary !== false) {
                if ($this->_echoCLI) {
                    $this->_echo('(jB)', 'primaryOver');
                }

                // Try to create a file with it.
                @file_put_contents($this->tmpPath.'samplepicture.jpg', $jpgBinary);

                // Try to resize and move it.
                $this->_foundJPGSample = (
                $this->_releaseImage->saveImage(
                    $this->_release->guid.'_thumb',
                    $this->tmpPath.'samplepicture.jpg',
                    $this->_releaseImage->jpgSavePath,
                    650,
                    650
                ) === 1
                );

                if ($this->_foundJPGSample !== false) {
                    // Update the DB to say we got it.
                    Release::query()->where('id', $this->_release->id)->update(['jpgstatus' => 1]);

                    if ($this->_echoCLI) {
                        $this->_echo('j', 'primaryOver');
                    }
                }

                @unlink($this->tmpPath.'samplepicture.jpg');
            } elseif ($this->_echoCLI) {
                $this->_echo('f', 'warningOver');
            }
        }
    }

    /**
     * Update the release to say we processed it.
     */
    protected function _finalizeRelease(): void
    {
        $vSQL = $jSQL = '';
        $iSQL = ', haspreview = 0';

        // If samples exist from previous runs, set flags.
        if (is_file($this->_releaseImage->imgSavePath.$this->_release->guid.'_thumb.jpg')) {
            $iSQL = ', haspreview = 1';
        }

        if (is_file($this->_releaseImage->vidSavePath.$this->_release->guid.'.ogv')) {
            $vSQL = ', videostatus = 1';
        }

        if (is_file($this->_releaseImage->jpgSavePath.$this->_release->guid.'_thumb.jpg')) {
            $jSQL = ', jpgstatus = 1';
        }

        // Get the amount of files we found inside the RAR/ZIP files.

        $releaseFilesCount = ReleaseFile::query()->where('releases_id', $this->_release->id)->count('releases_id');

        if ($releaseFilesCount === null) {
            $releaseFilesCount = 0;
        }

        $this->_passwordStatus = max($this->_passwordStatus);

        // Set the release to no password if password processing is off.
        if ($this->_processPasswords === false) {
            $this->_releaseHasPassword = false;
        }

        // If we failed to get anything from the RAR/ZIPs, decrement the passwordstatus, if the rar/zip has no password.
        if ($this->_releaseHasPassword === false && $this->_NZBHasCompressedFile && $releaseFilesCount === 0) {
            $query = sprintf(
                'UPDATE releases
				SET passwordstatus = passwordstatus - 1, rarinnerfilecount = %d %s %s %s
				WHERE id = %d',
                $releaseFilesCount,
                $iSQL,
                $vSQL,
                $jSQL,
                $this->_release->id
            );
        } // Else update the release with the password status (if the admin enabled the setting).
        else {
            $query = sprintf(
                'UPDATE releases
				SET passwordstatus = %d, rarinnerfilecount = %d %s %s %s
				WHERE id = %d',
                ($this->_processPasswords === true ? $this->_passwordStatus : Releases::PASSWD_NONE),
                $releaseFilesCount,
                $iSQL,
                $vSQL,
                $jSQL,
                $this->_release->id
            );
        }

        DB::update($query);
    }

    /**
     * @param string $pattern
     * @param string $path
     * @return false|\RecursiveIteratorIterator|\RegexIterator
     */
    protected function _getTempDirectoryContents($pattern = '', $path = '')
    {
        if ($path === '') {
            $path = $this->tmpPath;
        }
        try {
            if ($pattern !== '') {
                return new \RegexIterator(
                    new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($path)
                    ),
                    $pattern,
                    \RecursiveRegexIterator::GET_MATCH
                );
            }

            return new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path)
            );
        } catch (\Throwable $e) {
            $this->_debug('ERROR: Could not open temp dir: '.$e->getMessage());

            return false;
        }
    }

    /**
     * @param $fileLocation
     * @param $fileExtension
     * @return bool
     * @throws \Exception
     */
    protected function _getAudioInfo($fileLocation, $fileExtension): bool
    {
        // Return values.
        $retVal = $audVal = false;

        // Check if audio sample fetching is on.
        if ($this->_processAudioSample === false) {
            $audVal = true;
        }

        // Check if media info fetching is on.
        if ($this->_processAudioInfo === false) {
            $retVal = true;
        }

        // Make sure the category is music or other.
        $rQuery = DB::selectOne(
            sprintf(
                'SELECT searchname, fromname,  categories_id AS id, groups_id FROM releases WHERE proc_pp = 0 AND id = %d',
                $this->_release->id
            )
        );

        $musicParent = (string) Category::MUSIC_ROOT;
        if ($rQuery === null || ! preg_match(
                sprintf(
                    '/%d\d{3}|%d|%d|%d/',
                    $musicParent[0],
                    Category::OTHER_MISC,
                    Category::MOVIE_OTHER,
                    Category::TV_OTHER
                ),
                $rQuery->id
            )
        ) {
            return false;
        }

        if (is_file($fileLocation)) {

            // Check if media info is enabled.
            if ($retVal === false) {

                // Get the media info for the file.
                $xmlArray = $this->mediaInfo->getInfo($fileLocation, false);

                if ($xmlArray !== null) {
                    foreach ($xmlArray->getAudios() as $track) {
                        if ($track->get('album') !== null && $track->get('performer') !== null) {
                            if ((int) $this->_release->predb_id === 0 && config('nntmux.rename_music_mediainfo')) {
                                // Make the extension upper case.
                                $ext = strtoupper($fileExtension);

                                // Form a new search name.
                                if (! empty($track->get('recorded_date')) && preg_match('/(?:19|20)\d\d/', $track->get('recorded_date')->getFullname(), $Year)) {
                                    $newName = $track->get('performer')->getFullName().' - '.$track->get('album')->getFullName().' ('.$Year[0].') '.$ext;
                                } else {
                                    $newName = $track->get('performer')->getFullName().' - '.$track->get('album')->getFullName().' '.$ext;
                                }

                                // Get the category or try to determine it.
                                if ($ext === 'MP3') {
                                    $newCat = Category::MUSIC_MP3;
                                } elseif ($ext === 'FLAC') {
                                    $newCat = Category::MUSIC_LOSSLESS;
                                } else {
                                    $newCat = $this->_categorize->determineCategory($rQuery->groups_id, $newName, $rQuery->fromname);
                                }

                                $newTitle = escapeString(substr($newName, 0, 255));
                                // Update the search name.
                                DB::update(
                                        sprintf(
                                            '
											UPDATE releases
											SET searchname = %s, categories_id = %d, iscategorized = 1, isrenamed = 1, proc_pp = 1
											WHERE id = %d',
                                            $newTitle,
                                            $newCat,
                                            $this->_release->id
                                        )
                                    );
                                $this->sphinx->updateRelease($this->_release->id);

                                // Echo the changed name.
                                if ($this->_echoCLI) {
                                    NameFixer::echoChangedReleaseName(
                                            [
                                                'new_name' => $newName,
                                                'old_name' => $rQuery['searchname'],
                                                'new_category' => $newCat,
                                                'old_category' => $rQuery['id'],
                                                'group' => $rQuery['groups_id'],
                                                'releases_id' => $this->_release->id,
                                                'method' => 'ProcessAdditional->_getAudioInfo',
                                            ]
                                        );
                                }
                            }

                            // Add the media info.
                            $this->_releaseExtra->addFromXml($this->_release->id, $xmlArray);

                            $retVal = true;
                            $this->_foundAudioInfo = true;
                            if ($this->_echoCLI) {
                                $this->_echo('a', 'primaryOver');
                            }
                            break;
                        }
                    }
                }
            }

            // Check if creating audio samples is enabled.
            if ($audVal === false) {

                // File name to store audio file.
                $audioFileName = ($this->_release->guid.'.ogg');

                // Create an audio sample.
                if ($this->ffprobe->isValid($fileLocation)) {
                    try {
                        $audioSample = $this->ffmpeg->open($fileLocation);
                        $format = new Vorbis();
                        $audioSample->clip(TimeCode::fromSeconds(30), TimeCode::fromSeconds(30));
                        $audioSample->save($format, $this->tmpPath.$audioFileName);
                    } catch (\InvalidArgumentException $e) {
                        //We do nothing, just prevent displaying errors because the file cannot be open(corrupted or incomplete file)
                    }
                }

                // Check if the new file was created.
                if (is_file($this->tmpPath.$audioFileName)) {

                    // Try to move the temp audio file.
                    $renamed = rename($this->tmpPath.$audioFileName, $this->_audioSavePath.$audioFileName);

                    if (! $renamed) {
                        // Try to copy it if it fails.
                        $copied = copy($this->tmpPath.$audioFileName, $this->_audioSavePath.$audioFileName);

                        // Delete the old file.
                        unlink($this->tmpPath.$audioFileName);

                        // If it didn't copy continue.
                        if (! $copied) {
                            return false;
                        }
                    }

                    // Try to set the file perms.
                    @chmod($this->_audioSavePath.$audioFileName, 0764);

                    // Update DB to said we got a audio sample.
                    Release::query()->where('id', $this->_release->id)->update(['audiostatus' => 1]);

                    $audVal = $this->_foundAudioSample = true;

                    if ($this->_echoCLI) {
                        $this->_echo('A', 'primaryOver');
                    }
                }
            }
        }

        return $retVal && $audVal;
    }

    /**
     * Try to get JPG picture, resize it and store it on disk.
     *
     * @param string $fileLocation
     */
    protected function _getJPGSample($fileLocation): void
    {
        // Try to resize/move the image.
        $this->_foundJPGSample = (
        $this->_releaseImage->saveImage(
            $this->_release->guid.'_thumb',
            $fileLocation,
            $this->_releaseImage->jpgSavePath,
            650,
            650
        ) === 1
        );

        // If it's successful, tell the DB.
        if ($this->_foundJPGSample !== false) {
            Release::query()->where('id', $this->_release->id)->update(['jpgstatus' => 1]);
        }
    }

    /**
     * @param string $videoLocation
     *
     * @return string
     */
    private function getVideoTime($videoLocation): string
    {
        // Get the real duration of the file.
        if ($this->ffprobe->isValid($videoLocation)) {
            $time = $this->ffprobe->format($videoLocation)->get('duration');
        }

        if (empty($time) || ! preg_match('/time=(\d{1,2}:\d{1,2}:)?(\d{1,2})\.(\d{1,2})\s*bitrate=/i', $time, $numbers)) {
            return '';
        }

        // Reduce the last number by 1, this is to make sure we don't ask avconv/ffmpeg for non existing data.
        if ($numbers[3] > 0) {
            $numbers[3] -= 1;
        } elseif ($numbers[1] > 0) {
            $numbers[2] -= 1;
            $numbers[3] = '99';
        }

        // Manually pad the numbers in case they are 1 number. to get 02 for example instead of 2.
        return '00:00:'.str_pad($numbers[2], 2, '0', STR_PAD_LEFT).'.'.str_pad($numbers[3], 2, '0', STR_PAD_LEFT);
    }

    /**
     * @param string $fileLocation
     * @return bool
     * @throws \Exception
     */
    protected function _getSample(string $fileLocation): bool
    {
        if (! $this->_processThumbnails) {
            return false;
        }

        if (is_file($fileLocation)) {

            // Create path to temp file.
            $fileName = ($this->tmpPath.'zzzz'.random_int(5, 12).random_int(5, 12).'.jpg');

            $time = $this->getVideoTime($fileLocation);

            // Create the image.
            if ($this->ffprobe->isValid($fileLocation)) {
                try {
                    $this->ffmpeg->open($fileLocation)->frame(TimeCode::fromString($time === '' ? '00:00:03:00' : $time))->save($fileName);
                } catch (\RuntimeException $runtimeException) {
                    //We show no error at all, we failed to save the frame and move on
                } catch (\InvalidArgumentException $e) {
                    //We do nothing, just prevent displaying errors because the file cannot be open(corrupted or incomplete file)
                } catch (\Throwable $e) {
                    //Again we do nothing, we just want to catch the error
                }
            }

            // Check if the file exists.
            if (is_file($fileName)) {

                // Try to resize/move the image.
                $saved = $this->_releaseImage->saveImage(
                    $this->_release->guid.'_thumb',
                    $fileName,
                    $this->_releaseImage->imgSavePath,
                    800,
                    600
                );

                // Delete the temp file we created.
                @unlink($fileName);

                // Check if it saved.
                if ($saved === 1) {
                    if ($this->_echoCLI) {
                        $this->_echo('s', 'primaryOver');
                    }

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $fileLocation
     * @return bool
     * @throws \Exception
     */
    protected function _getVideo(string $fileLocation): bool
    {
        if (! $this->_processVideo) {
            return false;
        }

        // Try to find an avi file.
        if (is_file($fileLocation)) {

            // Create a filename to store the temp file.
            $fileName = ($this->tmpPath.'zzzz'.$this->_release->guid.'.ogv');

            $newMethod = false;
            // If wanted sample length is less than 60, try to get sample from the end of the video.
            if ($this->_ffMPEGDuration < 60) {
                // Get the real duration of the file.
                $time = $this->getVideoTime($fileLocation);

                if ($time !== '' && preg_match('/(\d{2}).(\d{2})/', $time, $numbers)) {
                    $newMethod = true;
                    // Get the lowest time we can start making the video at based on how many seconds the admin wants the video to be.
                    if ($numbers[1] <= $this->_ffMPEGDuration) {
                        // If the clip is shorter than the length we want.
                        // The lowest we want is 0.
                        $lowestLength = '00:00:00.00';
                    } else {
                        // If the clip is longer than the length we want.
                        // The lowest we want is the the difference between the max video length and our wanted total time.
                        $lowestLength = ($numbers[1] - $this->_ffMPEGDuration);
                        // Form the time string.
                        $end = '.'.$numbers[2];
                        switch (\strlen($lowestLength)) {
                            case 1:
                                $lowestLength = ('00:00:0'.$lowestLength.$end);
                                break;
                            case 2:
                                $lowestLength = ('00:00:'.$lowestLength.$end);
                                break;
                            default:
                                $lowestLength = '00:00:60.00';
                        }
                    }

                    // Try to get the sample (from the end instead of the start).
                    if ($this->ffprobe->isValid($fileLocation)) {
                        try {
                            $video = $this->ffmpeg->open($fileLocation);
                            $videoSample = $video->clip(TimeCode::fromString($lowestLength), TimeCode::fromSeconds($this->_ffMPEGDuration));
                            $format = new Ogg();
                            $format->setAudioCodec(new Vorbis());
                            $videoSample->filters()->resize(new Dimension(320, -1), ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
                            $videoSample->save($format, $fileName);
                        } catch (\InvalidArgumentException $e) {
                            //We do nothing, just prevent displaying errors because the file cannot be open(corrupted or incomplete file)
                        }
                    }
                }
            }

            if ($newMethod === false) {
                // If longer than 60 or we could not get the video length, run the old way.
                if ($this->ffprobe->isValid($fileLocation)) {
                    try {
                        $video = $this->ffmpeg->open($fileLocation);
                        $videoSample = $video->clip(TimeCode::fromSeconds(0), TimeCode::fromSeconds($this->_ffMPEGDuration));
                        $format = new Ogg();
                        $format->setAudioCodec(new Vorbis());
                        $videoSample->filters()->resize(new Dimension(320, -1), ResizeFilter::RESIZEMODE_SCALE_HEIGHT);
                        $videoSample->save($format, $fileName);
                    } catch (\InvalidArgumentException $e) {
                        //We do nothing, just prevent displaying errors because the file cannot be open(corrupted or incomplete file)
                    }
                }
            }

            // Until we find the video file.
            if (is_file($fileName)) {

                // Create a path to where the file should be moved.
                $newFile = ($this->_releaseImage->vidSavePath.$this->_release->guid.'.ogv');

                // Try to move the file to the new path.
                $renamed = @rename($fileName, $newFile);

                // If we couldn't rename it, try to copy it.
                if (! $renamed) {
                    $copied = @copy($fileName, $newFile);

                    // Delete the old file.
                    @unlink($fileName);

                    // If it didn't copy, continue.
                    if (! $copied) {
                        return false;
                    }
                }

                // Change the permissions.
                @chmod($newFile, 0764);

                // Update query to say we got the video.
                Release::query()->where('guid', $this->_release->guid)->update(['videostatus' => 1]);
                if ($this->_echoCLI) {
                    $this->_echo('v', 'primaryOver');
                }

                return true;
            }
        }

        return false;
    }

    /**
     * @param $fileLocation
     * @return bool
     * @throws \Exception
     */
    protected function _getMediaInfo($fileLocation): bool
    {
        if (! $this->_processMediaInfo) {
            return false;
        }

        // Look for the video file.
        if (is_file($fileLocation)) {
            $xmlArray = $this->mediaInfo->getInfo($fileLocation, false);

            // Check if we got it.

            if ($xmlArray === null) {
                return false;
            }

            // Insert it into the DB.
            $this->_releaseExtra->addFull($this->_release->id, $xmlArray);
            $this->_releaseExtra->addFromXml($this->_release->id, $xmlArray);

            if ($this->_echoCLI) {
                $this->_echo('m', 'primaryOver');
            }

            return true;
        }

        return false;
    }

    /**
     * @param $fileLocation
     * @throws \Exception
     */
    protected function _siftPAR2Info($fileLocation): void
    {
        $this->_par2Info->open($fileLocation);

        if ($this->_par2Info->error) {
            return;
        }

        $releaseInfo = DB::selectOne(
            sprintf(
                '
				SELECT UNIX_TIMESTAMP(postdate) AS postdate, proc_pp
				FROM releases
				WHERE id = %d',
                $this->_release->id
            )
        );

        if ($releaseInfo === null) {
            return;
        }

        // Only get a new name if the category is OTHER.
        $foundName = true;
        if ((int) $releaseInfo->proc_pp === 0 && config('nntmux.rename_par2') &&
            \in_array(
                (int) $this->_release->categories_id,
                Category::OTHERS_GROUP,
                false
            )
        ) {
            $foundName = false;
        }

        $filesAdded = 0;

        $files = $this->_par2Info->getFileList();
        foreach ($files as $file) {
            if (! isset($file['name'])) {
                continue;
            }

            // If we found a name and added 10 files, stop.
            if ($foundName === true && $filesAdded > 10) {
                break;
            }

            // Add to release files.
            if ($this->_addPAR2Files) {
                if ($filesAdded < 11 && ReleaseFile::query()->where(['releases_id' => $this->_release->id, 'name' => $file['name']])->first() === null
                ) {

                    // Try to add the files to the DB.
                    if (ReleaseFile::addReleaseFiles($this->_release->id, $file['name'], $file['hash_16K'], $file['size'], $releaseInfo->postdate, 0)) {
                        $filesAdded++;
                    }
                }
            } else {
                $filesAdded++;
            }

            // Try to get a new name.
            if ($foundName === false) {
                $this->_release->textstring = $file['name'];
                $this->_release->releases_id = $this->_release->id;
                if ($this->_nameFixer->checkName($this->_release, ($this->_echoCLI ? true : false), 'PAR2, ', 1, 1) === true) {
                    $foundName = true;
                }
            }
        }
        // Update the file count with the new file count + old file count.
        Release::query()->where('id', $this->_release->id)->increment('rarinnerfilecount', $filesAdded);
        $this->_foundPAR2Info = true;
    }

    /**
     * @param $fileLocation
     * @throws \Exception
     */
    protected function _processNfoFile($fileLocation): void
    {
        $data = @file_get_contents($fileLocation);
        if ($data !== false && $this->_nfo->isNFO($data, $this->_release->guid) === true && $this->_nfo->addAlternateNfo($data, (array) $this->_release, $this->_nntp) === true) {
            $this->_releaseHasNoNFO = false;
        }
    }

    /**
     * @param $fileLocation
     * @throws \Exception
     */
    protected function _processVideoFile($fileLocation): void
    {
        // Try to get a sample with it.
        if ($this->_foundSample === false) {
            $this->_foundSample = $this->_getSample($fileLocation);
        }

        /* Try to get a video with it.
         * Don't get it here if _sampleMessageIDs is empty
         * or has 1 message-id (Saves downloading another part).
         */
        if ($this->_foundVideo === false && \count($this->_sampleMessageIDs) < 2) {
            $this->_foundVideo = $this->_getVideo($fileLocation);
        }

        // Try to get media info with it.
        if ($this->_foundMediaInfo === false) {
            $this->_foundMediaInfo = $this->_getMediaInfo($fileLocation);
        }
    }

    /**
     * Comparison function for uSort, for sorting NZB files.
     *
     * @param array|null|string $a
     * @param array|null|string $b
     *
     * @return int
     */
    protected function _sortNZB($a, $b): int
    {
        $pos = 0;
        $af = $bf = false;
        $a = preg_replace('/\d+[- ._]?(\/|\||[o0]f)[- ._]?\d+?(?![- ._]\d)/i', ' ', $a['title']);
        $b = preg_replace('/\d+[- ._]?(\/|\||[o0]f)[- ._]?\d+?(?![- ._]\d)/i', ' ', $b['title']);

        if (preg_match('/\.(part\d+|r\d+)(\s*\.rar)*($|[ ")\]-])/i', $a)) {
            $af = true;
        }
        if (preg_match('/\.(part\d+|r\d+)(\s*\.rar)*($|[ ")\]-])/i', $b)) {
            $bf = true;
        }

        if (! $af && preg_match('/\.rar($|[ ")\]-])/i', $a)) {
            $a = preg_replace('/\.rar(?:$|[ ")\]-])/i', '.*rar', $a);
            $af = true;
        }
        if (! $bf && preg_match('/\.rar($|[ ")\]-])/i', $b)) {
            $b = preg_replace('/\.rar(?:$|[ ")\]-])/i', '.*rar', $b);
            $bf = true;
        }

        if (! $af && ! $bf) {
            return strnatcasecmp($a, $b);
        }

        if (! $bf) {
            return -1;
        }

        if (! $af) {
            return 1;
        }

        if ($af && $bf) {
            return strnatcasecmp($a, $b);
        }

        if ($af) {
            return -1;
        }

        if ($bf) {
            return 1;
        }

        return $pos;
    }

    /**
     * Reset some variables for the current release.
     */
    protected function _resetReleaseStatus(): void
    {
        // Only process for samples, previews and images if not disabled.
        $this->_foundVideo = $this->_processVideo ? false : true;
        $this->_foundMediaInfo = $this->_processMediaInfo ? false : true;
        $this->_foundAudioInfo = $this->_processAudioInfo ? false : true;
        $this->_foundAudioSample = $this->_processAudioSample ? false : true;
        $this->_foundJPGSample = $this->_processJPGSample ? false : true;
        $this->_foundSample = $this->_processThumbnails ? false : true;
        $this->_foundPAR2Info = false;

        $this->_passwordStatus = [Releases::PASSWD_NONE];
        $this->_releaseHasPassword = false;

        $this->_releaseGroupName = Group::getNameByID($this->_release->groups_id);

        $this->_releaseHasNoNFO = false;
        // Make sure we don't already have an nfo.
        if ((int) $this->_release->nfostatus !== 1) {
            $this->_releaseHasNoNFO = true;
        }

        $this->_NZBHasCompressedFile = false;

        $this->_sampleMessageIDs = $this->_JPGMessageIDs = $this->_MediaInfoMessageIDs = [];
        $this->_AudioInfoMessageIDs = $this->_RARFileMessageIDs = [];
        $this->_AudioInfoExtension = '';

        $this->_addedFileInfo = 0;
        $this->_totalFileInfo = 0;
        $this->_compressedFilesChecked = 0;
    }

    /**
     * Echo a string to CLI.
     *
     * @param string $string  String to echo.
     * @param string $type    Method type.
     *
     * @void
     */
    protected function _echo($string, $type): void
    {
        if ($this->_echoCLI) {
            ColorCLI::$type($string);
        }
    }

    /**
     * Echo a string to CLI. For debugging.
     *
     * @param string $string
     *
     * @void
     */
    protected function _debug($string): void
    {
        $this->_echo('DEBUG: '.$string, 'debug');
    }
}
