<?php

use nntmux\NZB;
use App\Models\User;
use nntmux\Releases;
use App\Models\Release;
use App\Models\Settings;
use nntmux\utility\Utility;
use App\Models\UserDownload;
use App\Models\UsersRelease;

$uid = 0;

// Page is accessible only by the rss token, or logged in users.
if (User::isLoggedIn()) {
    $uid = User::currentUserId();
    $maxDownloads = $page->userdata->role->downloadrequests;
    $rssToken = $page->userdata['rsstoken'];
    if (User::isDisabled($page->userdata['username'])) {
        Utility::showApiError(101);
    }
} else {
    if (! isset($_GET['i']) || ! isset($_GET['r'])) {
        Utility::showApiError(200);
    }

    $res = User::getByIdAndRssToken($_GET['i'], $_GET['r']);
    if (! $res) {
        Utility::showApiError(100);
    }

    $uid = $res['id'];
    $rssToken = $res['rsstoken'];
    $maxDownloads = $res->role->downloadrequests;
    if (User::isDisabled($res['username'])) {
        Utility::showApiError(101);
    }
}

// Remove any suffixed id with .nzb which is added to help weblogging programs see nzb traffic.
if (isset($_GET['id'])) {
    $_GET['id'] = str_ireplace('.nzb', '', $_GET['id']);
}
//
// A hash of the users ip to record against the download
//
$hosthash = '';
if ((int) Settings::settingValue('..storeuserips') === 1) {
    $hosthash = User::getHostHash($_SERVER['REMOTE_ADDR'], Settings::settingValue('..siteseed'));
}

// Check download limit on user role.
$requests = UserDownload::getDownloadRequests($uid);
if ($requests > $maxDownloads) {
    Utility::showApiError(501);
}

if (! isset($_GET['id'])) {
    Utility::showApiError(200, 'Parameter id is required');
}

// Remove any suffixed id with .nzb which is added to help weblogging programs see nzb traffic.
$_GET['id'] = str_ireplace('.nzb', '', $_GET['id']);

$rel = new Releases(['Settings' => $page->settings]);
// User requested a zip of guid,guid,guid releases.
if (isset($_GET['zip']) && $_GET['zip'] === '1') {
    $guids = explode(',', $_GET['id']);
    if ($requests['num'] + count($guids) > $maxDownloads) {
        Utility::showApiError(501);
    }

    $zip = $rel->getZipped($guids);
    if (strlen($zip) > 0) {
        User::incrementGrabs($uid, count($guids));
        foreach ($guids as $guid) {
            Release::updateGrab($guid);
            UserDownload::addDownloadRequest($uid, $guid);

            if (isset($_GET['del']) && (int) $_GET['del'] === 1) {
                UsersRelease::delCartByUserAndRelease($guid, $uid);
            }
        }

        header('Content-type: application/octet-stream');
        header('Content-disposition: attachment; filename='.date('Ymdhis').'.nzb.zip');
        exit($zip);
    } else {
        $page->show404();
    }
}

$nzbPath = (new NZB())->getNZBPath($_GET['id']);
if (! file_exists($nzbPath)) {
    Utility::showApiError(300, 'NZB file not found!');
}

$relData = Release::getByGuid($_GET['id']);
if ($relData) {
    Release::updateGrab($_GET['id']);
    UserDownload::addDownloadRequest($uid, $relData['id']);
    User::incrementGrabs($uid);
    if (isset($_GET['del']) && (int) $_GET['del'] === 1) {
        UsersRelease::delCartByUserAndRelease($_GET['id'], $uid);
    }
} else {
    Utility::showApiError(300, 'Release not found!');
}

// Start reading output buffer.
ob_start();
// De-gzip the NZB and store it in the output buffer.
readgzfile($nzbPath);

$cleanName = str_replace([',', ' ', '/'], '_', $relData['searchname']);

// Set the NZB file name.
header('Content-Disposition: attachment; filename='.$cleanName.'.nzb');
// Get the size of the NZB file.
header('Content-Length: '.ob_get_length());
header('Content-Type: application/x-nzb');
header('Expires: '.date('r', time() + 31536000));
// Set X-DNZB header data.
header('X-DNZB-Failure: '.$page->serverurl.'failed/'.'?guid='.$_GET['id'].'&userid='.$uid.'&rsstoken='.$rssToken);
header('X-DNZB-Category: '.$relData['category_name']);
header('X-DNZB-Details: '.$page->serverurl.'details/'.$_GET['id']);
if (! empty($relData['imdbid']) && $relData['imdbid'] > 0) {
    header('X-DNZB-MoreInfo: http://www.imdb.com/title/tt'.$relData['imdbid']);
} elseif (! empty($relData['tvdb']) && $relData['tvdb'] > 0) {
    header('X-DNZB-MoreInfo: http://www.thetvdb.com/?tab=series&id='.$relData['tvdb']);
}
header('X-DNZB-Name: '.$cleanName);
if ((int) $relData['nfostatus'] === 1) {
    header('X-DNZB-NFO: '.$page->serverurl.'nfo/'.$_GET['id']);
}
header('X-DNZB-RCode: 200');
header('X-DNZB-RText: OK, NZB content follows.');

// Print buffer and flush it.
ob_end_flush();
