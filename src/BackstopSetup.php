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

      $localDdevUrl = $io->ask('<info>Local DDEV URL?</info>:' . "\n > ");
      $liveSiteUrl = $io->ask('<info>Live Site URL?</info>:' . "\n > ");

      $config['local'] = $localDdevUrl;
      $config['live'] = $liveSiteUrl;

      // config.yaml.
      try {
        $fileSystem->dumpFile(static::$configPath, json_encode($config, JSON_PRETTY_PRINT));
        $io->info('<info>Config updated.</info>');
      }
      catch (\Error $e) {
        $io->error('<error>' . $e->getMessage() . '</error>');
      }
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
