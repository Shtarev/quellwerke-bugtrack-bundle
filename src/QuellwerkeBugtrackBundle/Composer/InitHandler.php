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

        /* >>> If needed, also add any additional configurations. */

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
        if (strpos($content, $bundleLine) === false) { // Check if the bundle is already registered
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
        // routes from a controller
        if($pimVersion == 10) {
            // TODO: routes from a controller for Pimcore version 10
        }
        if($pimVersion == 11) {
            copy($source . 'bugs_bundle.yaml', $target . 'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bugs_bundle.yaml');
            // TODO: Maybe there is another solution, such as symbolic links.
            $res = self::routAdd($projectRoot);
        }
    }

    /**
     * Register routes from a controller
     * @param string $projectRoot
     * @return bool
     */
    private static function routAdd($projectRoot): bool
    {
        $result = true;
        $source = $projectRoot . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'quellwerke' . DIRECTORY_SEPARATOR . 'quellwerke-bugtrack-bundle' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'QuellwerkeBugtrackBundle' . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'bugs_bundle.yaml';
        $dir = $projectRoot . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'routes';
        $target = $dir . DIRECTORY_SEPARATOR . 'bugs_bundle.yaml';
        if (is_dir($dir)) {
            copy($source, $target);
        } else {
            $result = false;
        }
        return $result;
    }
}
