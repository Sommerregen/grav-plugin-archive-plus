<?php
/**
 * Archive Plus v1.3.2
 *
 * An enhanced version of the Grav Archives plugin with more
 * configuration options and the ability to show a blogger-like
 * archive menu for links grouped by year and/or month.
 *
 * Dual licensed under the MIT or GPL Version 3 licenses, see LICENSE.
 * http://benjamin-regler.de/license/
 *
 * @package     Archive Plus
 * @version     1.3.2
 * @link        <https://github.com/sommerregen/grav-plugin-archive-plus>
 * @author      Benjamin Regler <sommerregen@benjamin-regler.de>
 * @copyright   2015-2017, Benjamin Regler
 * @license     <http://opensource.org/licenses/MIT>        MIT
 * @license     <http://opensource.org/licenses/GPL-3.0>    GPLv3
 */

namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Page\Page;
use Grav\Common\Data\Blueprints;

use RocketTheme\Toolbox\Event\Event;

/**
 * Archive Plus Plugin
 */
class ArchivePlusPlugin extends Plugin
{
  /**
   * @var ArchivePlusPlugin
   */

  /**
   * Instance of ArchivePlus class
   *
   * @var \Grav\Plugin\ArchivePlus
   */
  protected $archive_plus;

  /** -------------
   * Public methods
   * --------------
   */

  /**
   * Return a list of subscribed events of this plugin.
   *
   * @return array    The list of events of the plugin of the form
   *                      'name' => ['method_name', priority].
   */
  public static function getSubscribedEvents()
  {
    // Make sure the plugin is called before the Archives plugin
    return [
      'onPluginsInitialized' => ['onPluginsInitialized', 5],
    ];
  }

  /**
   * Initialize configuration
   */
  public function onPluginsInitialized()
  {
    if ($this->config->get('plugins.archive_plus.enabled')) {
      // Emulate Archives plugin; switch it off, if present
      $this->config->set('plugins.archives.enabled', false);
      $this->config->set('plugins.archives.emulated', true);

      // Dynamically add the needed taxonomy types to the taxonomies config
      $archive = $this->grav['language']->translate(['PLUGINS.ARCHIVE_PLUS.NAME']);
      $taxonomy_config = array_merge((array)
        $this->config->get('site.taxonomies', []), [$archive]);
      $this->config->set('site.taxonomies', $taxonomy_config);

      // Set default events
      $events = [
        'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
        'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
        'onPageProcessed' => ['onPageProcessed', 0]
      ];

      // Set admin specific events
      if ($this->isAdmin()) {
        $this->active = false;
        $events = [
          'onBlueprintCreated' => ['onBlueprintCreated', 0]
        ];
      }

      // Register events
      $this->enable($events);
    }
  }

  /**
   * Add content after page was processed.
   *
   * @param Event $event
   */
  public function onPageProcessed(Event $event)
  {
    /** @var Page $page */
    $page = $event['page'];

    /** @var Language $language */
    $language = $this->grav['language'];

    $taxonomy = $page->taxonomy();
    $config = $this->mergeArchiveConfig($page);

    // Process page contents
    if ($config->get('enabled')) {
      // Extract language from page extension
      $active = preg_match('~(\w+)\.\w+$~i', $page->extension(), $match) ? $match[1] : '';
      $key = $language->translate(['PLUGINS.ARCHIVE_PLUS.NAME'], ['en']);

      if (!isset($taxonomy[$key])) {
        $index = date('n', $page->date()) - 1;
        $year = date('Y', $page->date());
        $languages = $active ? [$active]: $language->getLanguages();

        foreach ($languages as $lang) {
          $key = $language->translate(['PLUGINS.ARCHIVE_PLUS.NAME'], [$lang]);
          $month = strtolower($language->translateArray('PLUGINS.ARCHIVE_PLUS.SHORT_MONTHS', $index, [$lang]));

          // Track month taxonomy in "jan_2016" and year in "2016" format
          $taxonomy[$key] = ["{$month}_{$year}", $year];
        }
      }

      // set the modified taxonomy back on the page object
      $page->taxonomy($taxonomy);
    }
  }

  /**
   * Add current directory to twig lookup paths.
   */
  public function onTwigTemplatePaths()
  {
    $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
  }

  /**
   * Set needed variables to display archive plus block.
   */
  public function onTwigSiteVariables()
  {
    /** @var Page $page */
    $page = $this->grav['page'];

    /** @var Twig $twig */
    $twig = $this->grav['twig'];

    $config = $this->mergeArchiveConfig($page);
    if ($config->get('enabled')) {
      // Emulate Archives plugin; temporarily enable it to for rendering
      $this->config->set('plugins.archives.enabled', true);

      // Make ArchivePlus class accessible in Twig
      $twig->twig_vars['archive_plus'] = $this->init($config);

      // Inject built-in CSS if desired
      if ($this->config->get('plugins.archive_plus.built_in_css')) {
        $this->grav['assets']
          ->add('plugin://archive_plus/assets/css/archive_plus.css');
      }
    }
  }

  /** ----
   * Admin
   * -----
   */

  /**
   * Extend page blueprints with ArchivePlus configuration options.
   *
   * @param Event $event
   */
  public function onBlueprintCreated(Event $event)
  {
    /** @var Blueprints $blueprint */
    $blueprint = $event['blueprint'];
    if ($blueprint->get('form.fields.tabs')) {
      $blueprints = new Blueprints(__DIR__ . '/blueprints/');
      $extends = $blueprints->get($this->name);
      $blueprint->extend($extends, true);
    }
  }

  /** -------------------------------
   * Private/protected helper methods
   * --------------------------------
   */

  /**
   * Merge global and page archive settings
   *
   * @param Page  $page    The page to merge the page archive configurations
   *                       with the archive settings.
   * @param bool  $default The default value in case no archive setting was
   *                       found.
   *
   * @return array
   */
  protected function mergeArchiveConfig(Page $page, $default = null)
  {
    $p = $page;
    while ($page && !$page->root()) {
      if (isset($page->header()->archive_plus)) {
        if ($page->header()->archive_plus === '@default') {
          break;
        }

        // Merge config recursive
        return $this->mergeConfig($page);
      }
      $page = $page->parent();
    }

    return $default ?: $this->mergeConfig($p);
  }

  /**
   * Initialize plugin and all dependencies.
   *
   * @return \Grav\Plugin\ArchivePlus   Returns ArchivePlus instance.
   */
  protected function init($config = null)
  {
    if (!$this->archive_plus) {
      // Initialize ArchivePlus instance
      require_once(__DIR__ . '/classes/ArchivePlus.php');

      $config = $config ?: $this->mergeArchiveConfig($this->grav['page']);
      $this->archive_plus = new ArchivePlus($config);
    }

    return $this->archive_plus;
  }
}
