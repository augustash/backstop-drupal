<?php

namespace Augustash;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

/**
 * BackstopSetup console class.
 */
class BackstopSetup {


  /**
   * Path to config file.
   *
   * @var string
   */
  private static $configPath = __DIR__ . '/../../../../tests/backstop.json';

  /**
   * Path to config file.
   *
   * @var string
   */
  private static $newConfigPath = __DIR__ . '/../assets/backstop.json';

  /**
   * The tests root.
   *
   * @var string
   */
  private static $testsRoot = __DIR__ . '/../../../../tests/';

  /**
   * The docroot.
   *
   * @var string
   */
  private static $docRoot;

  /**
   * Run on post-install-cmd.
   *
   * @param \Composer\Script\Event $event
   *   The event.
   */
  public static function postPackageInstall(Event $event) {

    $fileSystem = new Filesystem();
    if ($fileSystem->exists(static::$newConfigPath)) {
      $io = $event->getIO();
      // Load the new config file from the assets directory.
      $configContents = file_get_contents(static::$newConfigPath);
      $config = json_decode($configContents, true);

      $localDdevUrl = $io->ask('<info>Local DDEV URL?</info> (e.g., http://local.ddev.site): ' . "\n > ");
      $liveSiteUrl = $io->ask('<info>Live Site URL?</info> (e.g., https://livesite.com): ' . "\n > ");
      $additionalNodes = $io->ask('<info>Additional nodes? (Default is node 1-5, but you can pass more as a comma separated list)</info> (e.g., ["6,7,8"]): ' . "\n > ");
      $hideSelectors = $io->ask('<info>Do you have additional selectors to hide? (Default is .captcha, but you can pass more as a comma separated list)</info> (e.g., ["#block-block-1"]): ' . "\n > ");
      $removeSelectors = $io->ask('<info>Do you have additional selectors to remove? (Default is .eu-cookie-compliance-banner, but you can pass more as a comma separated list)</info> (e.g., ["#block-block-1"]): ' . "\n > ");

      // Replace URLs in all 'scenarios'.
      foreach ($config['scenarios'] as &$scenario) {
        $scenario['url'] = $localDdevUrl . str_replace('http://local.ddev.site', '', $scenario['url']);
        $scenario['referenceUrl'] = $liveSiteUrl . str_replace('https://livesite.com', '', $scenario['referenceUrl']);
      }

      // If there are additionalNodes passed, we need to add new scenarios for each node
      if ($additionalNodes) {
        $additionalNodes = explode(',', $additionalNodes);
        foreach ($additionalNodes as $node) {
          $config['scenarios'][] = [
            'label' => 'node:Custom ' . $node,
            'url' => $localDdevUrl . '/node/' . $node,
            'referenceUrl' => $liveSiteUrl . '/node/' . $node,
          ];
        }
      }

      // If there are hideSelectors passed, we need to add them to
      // the list in $config['scenarioDefaults']['hideSelectors']
      if ($hideSelectors) {
        $hideSelectors = explode(',', $hideSelectors);
        $config['scenarioDefaults']['hideSelectors'] = array_merge($config['scenarioDefaults']['hideSelectors'], $hideSelectors);
      }

      // If there are removeSelectors passed, we need to add them to
      // the list in $config['scenarioDefaults']['removeSelectors']
      if ($removeSelectors) {
        $removeSelectors = explode(',', $removeSelectors);
        $config['scenarioDefaults']['removeSelectors'] = array_merge($config['scenarioDefaults']['removeSelectors'], $removeSelectors);
      }

      // Now replace the original file at $configPath with the updated config.
      try {
        $fileSystem->dumpFile(static::$configPath, json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $io->write('<info>Original config file successfully replaced with updated config.</info>');
      } catch (\Exception $e) {
        $io->write('<error>Failed to replace original config file: ' . $e->getMessage() . '</error>');
      }
    } else {
      $io = $event->getIO();
      $io->write('<error>Config file not found at ' . static::$configPath . '.</error>');
    }
  }


  /**
   * Get docroot.
   */
  protected static function getWebRootPath() {
    $root = __DIR__ . '/../../../../';
    if ($docroot = static::$docRoot) {
      $root .= $docroot . '/';
    }
    return $root;
  }

}
