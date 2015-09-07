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
 * @copyright   2015, Benjamin Regler
 * @license     <http://opensource.org/licenses/MIT>        MIT
 * @license     <http://opensource.org/licenses/GPL-3.0>    GPLv3
 */

namespace Grav\Plugin;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Page\Collection;
use RocketTheme\Toolbox\Event\Event;

/**
 * Archive Plus Plugin
 *
 * An enhanced version of the Grav Archives plugin with more
 * configuration options and the ability to show a blogger-like
 * archive menu for links grouped by year and/or month.
 */
class ArchivePlusPlugin extends Plugin
{
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
        return [
            // Make sure the plugin is called before the Archives plugin
            'onPluginsInitialized' => ['onPluginsInitialized', 5]
        ];
    }

    /**
     * Initialize configuration
     */
    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            $this->active = false;
            return;
        }

        // Activate plugin only if 'enabled' option is set true
        if (!$this->config->get('plugins.archive_plus.enabled')) {
            return;
        }

        // Emulate Archives plugin; switch it off, if present
        $this->config->set('plugins.archives.enabled', false);
        $this->config->set('plugins.archives.emulated', true);

        // Dynamically add the needed taxonomy types to the taxonomies config
        $archive = $this->grav['language']->translate(['PLUGIN_ARCHIVE_PLUS.NAME']);
        $taxonomy_config = array_merge((array)
            $this->config->get('site.taxonomies'), [$archive]);
        $this->config->set('site.taxonomies', $taxonomy_config);

        $this->enable([
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onPageProcessed' => ['onPageProcessed', 0],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0]
        ]);
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    /**
     * Add content after page was processed.
     *
     * @param Event $event
     */
    public function onPageProcessed(Event $event)
    {
        // Get the page header
        $page = $event['page'];
        $language = $this->grav['language'];
        $taxonomy = $page->taxonomy();

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

                // Track month taxonomy in "jan_2015" and year in "2015" format
                $taxonomy[$key] = ["{$month}_{$year}", $year];
            }
        }

        // set the modified taxonomy back on the page object
        $page->taxonomy($taxonomy);
    }

    /**
     * Set needed variables to display archive plus block.
     */
    public function onTwigSiteVariables() {
        /** @var Cache $cache */
        $cache = $this->grav['cache'];

        // Emulate Archives plugin; temporarily enable it to for rendering
        $this->config->set('plugins.archives.enabled', true);

        $cache_id = md5('archive_plus'.$cache->getKey());
        $config = $cache->fetch($cache_id);

        if ($config === false) {
            /** @var Taxonomy $taxonomy_map */
            $taxonomy_map = $this->grav['taxonomy'];
            $pages = $this->grav['pages'];

            // Get current datetime
            $start_date = time();

            // Initialize variables
            $archives = [];

            // Get plugin filters settings
            $filters = (array) $this->config->get('plugins.archive_plus.filters');
            $operator = $this->config->get('plugins.archive_plus.filter_combinator');

            if (count($filters) > 0) {
                $collection = new Collection();
                $collection->append(
                    $taxonomy_map->findTaxonomy($filters, $operator)->toArray()
                );

                // reorder the collection based on settings
                $collection = $collection->order(
                    $this->config->get('plugins.archive_plus.order.by'),
                    $this->config->get('plugins.archive_plus.order.dir')
                );

                // Loop over new collection of pages that match filters
                foreach ($collection as $page) {
                    // Update the start date if the page date is older
                    $start_date = $page->date() < $start_date ? $page->date() : $start_date;

                    list($year, $month) = explode(' ', date('Y n', $page->date()));
                    $archives[$year][$month][] = $page;
                }
            }

            // Limit output of archive block depending on number of items,
            // number of months or years to display
            $user_limits = (array) $this->config->get('plugins.archive_plus.limit');
            $limits = array_fill_keys(array('items', 'month', 'year'), 0);

            $show_more = (count($archives) > $user_limits['year']) ? true : false;
            if ($user_limits['year'] > 0) {
                $archives = array_slice($archives, 0, $user_limits['year'], true);
            }

            // Limit items in the output based on plugin settings
            foreach ($archives as $year => &$months) {
                $num = count($months);
                if ($limits['month'] >= $user_limits['month'] || $limits['items'] >= $user_limits['items']) {
                    unset($archives[$year]);
                    $show_more = true;
                    continue;

                } elseif ($limits['month'] + $num > $user_limits['month']) {
                    $length = $user_limits['month'] - $limits['month'];
                    $months = array_slice($months, 0, $length, true);
                    $show_more = true;
                }

                $limits['month'] += $num;
                foreach ($months as $month => &$pages) {
                    $num = count($pages);
                    if ($limits['items'] > $user_limits['items']) {
                        unset($archives[$year][$month]);
                        $show_more = true;

                    } elseif ($limits['items'] + $num > $user_limits['items']) {
                        $length = $user_limits['items'] - $limits['items'];
                        $pages = array_slice($pages, 0, $length, true);
                        if ($length == 0) {
                            unset($archives[$year][$month]);
                        }

                        $limits['items'] += $num;
                        $limits['month'] = $user_limits['month'] + 1;
                        $show_more = true;
                    }
                    $limits['items'] += $num;
                }
            }

            // Get configurations of Archive Plus plugin
            $config = (array) $this->config->get('plugins.archive_plus');

            $config['data'] = $archives;
            $config['show_more'] = $show_more;

            $cache->save($cache_id, $config);
        }

        // Add Archive Plus configurations to the twig variables
        $this->grav['twig']->twig_vars['archive_plus'] = $config;

        // Inject built-in CSS if desired
        if ($this->config->get('plugins.archive_plus.built_in_css')) {
            $this->grav['assets']
                ->add('plugin://archive_plus/assets/css/archive_plus.css');
        }
    }
}
