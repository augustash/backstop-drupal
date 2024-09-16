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

      // Replace URLs in all 'scenarios'.
      foreach ($config['scenarios'] as &$scenario) {
          $scenario['url'] = $localDdevUrl . str_replace('http://local.ddev.site', '', $scenario['url']);
          $scenario['referenceUrl'] = $liveSiteUrl . str_replace('https://livesite.com', '', $scenario['referenceUrl']);
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
