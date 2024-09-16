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
    if ($fileSystem->exists(static::$configPath)) {
      $io = $event->getIO();
      $config = json_decode(file_get_contents(static::$configPath), true);

      $localDdevUrl = $io->ask('<info>Local DDEV URL?</info> (e.g., http://local.ddev.site): ' . "\n > ");
      $liveSiteUrl = $io->ask('<info>Live Site URL?</info> (e.g., https://livesite.com): ' . "\n > ");

      // Update the URLs in the 'scenarios' array.
      foreach ($config['scenarios'] as &$scenario) {
        $scenario['url'] = str_replace('http://local.ddev.site', $localDdevUrl, $scenario['url']);
        $scenario['referenceUrl'] = str_replace('https://livesite.com', $liveSiteUrl, $scenario['referenceUrl']);
      }

      try {
        $fileSystem->dumpFile(static::$configPath, json_encode($config, JSON_PRETTY_PRINT));
        $io->write('<info>Config updated successfully.</info>');
      } catch (\Exception $e) {
          $io->write('<error>Failed to update config: ' . $e->getMessage() . '</error>');
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
