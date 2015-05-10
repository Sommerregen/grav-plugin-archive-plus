<?php
/**
 * Archive Plus v1.1.1
 *
 * An enhanced version of the Grav Archives plugin with more
 * configuration options and the ability to show a blogger-like
 * archive menu for links grouped by year and/or month.
 *
 * Licensed under MIT, see LICENSE.
 *
 * @package     Archive Plus
 * @version     1.1.1
 * @link        <https://github.com/sommerregen/grav-plugin-archive-plus>
 * @author      Benjamin Regler <sommerregen@benjamin-regler.de>
 * @copyright   2015, Benjamin Regler
 * @license     <http://opensource.org/licenses/MIT>            MIT
 */

namespace Grav\Plugin;

use Grav\Common\Grav;
use Grav\Common\Plugin;
use Grav\Common\Taxonomy;
use Grav\Common\Page\Page;
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
            'onPluginsInitialized' => ['onPluginsInitialized', 1]
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
        $taxonomy_config = array_merge((array)
            $this->config->get('site.taxonomies'), ['archive']);
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
        $taxonomy = $page->taxonomy();

        if (!isset($taxonomy['archive'])) {
            // Track month taxonomy in "jan_2015" format
            $taxonomy['archive'] = array(
                strtolower(date('M_Y', $page->date()))
            );
            // Track year taxonomy in "2015" format
            $taxonomy['archive'][] = date('Y', $page->date());
        }

        // set the modified taxonomy back on the page object
        $page->taxonomy($taxonomy);
    }

    /**
     * Set needed variables to display archive plus block.
     */
    public function onTwigSiteVariables() {
        // Emulate Archives plugin; temporarily enable it to for rendering
        $this->config->set('plugins.archives.enabled', true);

        /** @var Taxonomy $taxonomy_map */
        $taxonomy_map = $this->grav['taxonomy'];
        $pages = $this->grav['pages'];

        // Get current datetime
        $start_date = time();

        // Initialize variables
        $id = $this->grav['page']->id();

        $archives = [];
        $current = null;

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
            $date_format = $this->config->get('plugins.archive_plus.date_display_format');

            // Loop over new collection of pages that match filters
            foreach ($collection as $page) {
                // Update the start date if the page date is older
                $start_date = $page->date() < $start_date ? $page->date() : $start_date;

                $archives[date('Y', $page->date())][date($date_format, $page->date())][] = $page;

                // Store current page, if found in archive list
                if ($page->id() == $id) {
                    $current = array(
                        'page' => $page,
                        'month' => date($date_format, $page->date()),
                        'year' => date('Y', $page->date()),
                    );
                }
            }
        }

        // Limit output of archive block depending on number of items,
        // number of months or years to display
        $show_more = false;
        $user_limits = (array) $this->config->get('plugins.archive_plus.limit');
        $limits = array_fill_keys(array('items', 'month', 'year'), 0);

        // Slice the array to the limit you want
        foreach ($archives as $year => $months) {
            foreach ($months as $month => $pages) {
                if ($user_limits['items'] > 0) {
                    if ($limits['items'] < $user_limits['items']) {
                        $limits['items'] += count($pages);
                    } else {
                        unset($archives[$year][$month]);
                        $show_more = true;
                    }
                }

                if ($user_limits['month'] > 0) {
                    if ($limits['month'] < $user_limits['month']) {
                        $limits['month'] += 1;
                    } else {
                        unset($archives[$year][$month]);
                        $show_more = true;
                    }
                }
            }
            if ($user_limits['year'] > 0) {
                if ($limits['year'] < $user_limits['year']) {
                    $limits['year'] += 1;
                } else {
                    unset($archives[$year]);
                    $show_more = true;
                }
            }
        }

        // Get configurations of Archive Plus plugin
        $config = (array) $this->config->get('plugins.archive_plus');

        $config['data'] = $archives;
        $config['current'] = $current;
        $config['show_more'] = $show_more;

        // Add Archive Plus configurations to the twig variables
        $this->grav['twig']->twig_vars['archive_plus'] = $config;

        // Inject built-in CSS if desired
        if ($this->config->get('plugins.archive_plus.built_in_css')) {
            $this->grav['assets']
                ->add('plugin://archive_plus/assets/css/archive_plus.css');
        }
    }
}
