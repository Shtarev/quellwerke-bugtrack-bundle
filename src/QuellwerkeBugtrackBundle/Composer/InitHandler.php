<?php
namespace Quellwerke\QuellwerkeBugtrackBundle\Composer;

use Pimcore\Version;

class InitHandler
{

    public static function init()
    {
        $pimVersion = (int) strtok(ltrim(Version::getVersion(), 'v'), '.');
        $projectRoot = dirname(__DIR__, 6); // vendor/quellwerke/quellwerke-bugtrack-bundle/src/QuellwerkeBugtrackBundle/Composer

        $res = self::bundleAdd($projectRoot);
        if($res === false) {
            return;
        }

        self::versionAdapter($projectRoot, $pimVersion);

        /* HERE:>>> If needed, also add any additional configurations. */

    }

    /**
     * Register the bundle in config/bundles.php
     * @param string $projectRoot
     * @return bool
     */
    private static function bundleAdd($projectRoot): bool
    {
        $result = true;
        $bundlesInclude = $projectRoot . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bundles.php';
        $content = file_get_contents($bundlesInclude);
        $bundleLine = "Quellwerke\QuellwerkeBugtrackBundle\QuellwerkeBugtrackBundle::class => ['all' => true],";
;
        if (strpos($content, $bundleLine) === false) {
            $content = str_replace(<<<REP
return [
REP, <<<LACE
return [
    $bundleLine
LACE, $content);
            file_put_contents($bundlesInclude, $content);
            echo "[INFO] Bundle QuellwerkeBugtrackBundle added in config/bundles.php\n";
        } else {
            $result = false;
        }
        return $result;
    }

    private static function versionAdapter($projectRoot, $pimVersion): void
    {
        $source = $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'quellwerke' . DIRECTORY_SEPARATOR . 'quellwerke-bugtrack-bundle' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'QuellwerkeBugtrackBundle' . DIRECTORY_SEPARATOR . 'Version' . DIRECTORY_SEPARATOR . $pimVersion . DIRECTORY_SEPARATOR;
        $target = $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'quellwerke' . DIRECTORY_SEPARATOR . 'quellwerke-bugtrack-bundle' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'QuellwerkeBugtrackBundle' . DIRECTORY_SEPARATOR;
        
        copy($source . 'QuellwerkeBugtrackBundle.php', $target . 'QuellwerkeBugtrackBundle.php');
        copy($source . 'bugtrack.js', $target . 'Resources' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'bugtrack.js');
        copy($source . 'quellwerke_bugtrack_bundle.yaml', $projectRoot . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'quellwerke_bugtrack_bundle.yaml');
    }

}
