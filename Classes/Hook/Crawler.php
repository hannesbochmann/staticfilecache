<?php
/**
 * Crawler hook
 *
 * @author Tim Lochmüller
 * @author Daniel Poetzinger
 */

declare(strict_types=1);

namespace SFC\Staticfilecache\Hook;

use SFC\Staticfilecache\Service\CacheService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Crawler hook
 */
class Crawler extends AbstractHook
{

    /**
     * (Hook-function called from TypoScriptFrontend, see ext_localconf.php for configuration)
     *
     * @param array $parameters Parameters delivered by TypoScriptFrontend
     * @param TypoScriptFrontendController $pObj The calling parent object (TypoScriptFrontend)
     *
     * @returnvoid
     */
    public function clearStaticFile(array $parameters, TypoScriptFrontendController $pObj)
    {
        if (!ExtensionManagementUtility::isLoaded('crawler')) {
            return;
        }
        if ($pObj->applicationData['tx_crawler']['running'] && in_array(
            'tx_staticfilecache_clearstaticfile',
            $pObj->applicationData['tx_crawler']['parameters']['procInstructions']
        )
        ) {
            $pageId = $GLOBALS['TSFE']->id;
            if (is_numeric($pageId)) {
                GeneralUtility::makeInstance(CacheService::class)->clearByPageId($pageId);
                $pObj->applicationData['tx_crawler']['log'][] = 'EXT:staticfilecache cleared static file';
            } else {
                $pObj->applicationData['tx_crawler']['log'][] = 'EXT:staticfilecache skipped';
            }
        }
    }
}
