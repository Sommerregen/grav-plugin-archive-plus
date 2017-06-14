<?php
/**
 * Archive Plus
 *
 * This file is part of Grav Archive Plus plugin.
 *
 * Dual licensed under the MIT or GPL Version 3 licenses, see LICENSE.
 * http://benjamin-regler.de/license/
 */

namespace Grav\Plugin;

use Grav\Common\GravTrait;
use Grav\Common\Data\Data;
use Grav\Common\Page\Collection;

/**
 * Archive Plus
 *
 * Helper class to show a blogger-like archive menu for links grouped by
 * year and/or month.
 */
class ArchivePlus
{
  /**
   * @var ArchivePlus
   */
  use GravTrait;

  /**
   * Configuration settings
   *
   * @var \Grav\Common\Data\Data
   */
  protected $config;

  /** -------------
   * Public methods
   * --------------
   */

  /**
   * Constructor.
   *
   * @param Data $config  Configuration settings.
   */
  public function __construct(Data $config)
  {
    $this->config($config);
  }

  /**
   * Get or set the default configuration settings
   *
   * @param  Data|null $var Set the configuration settings
   * @return Data           The current default configuration settings
   */
  public function config(Data $var = null)
  {
    if ($var) {
      $this->config = $var;
    } elseif (!$this->config) {
      $this->config = new Data();
    }

    return $this->config;
  }

  /**
   * Get the needed data to display an Archive Plus block
   *
   * @param  Data|null $config Additional configuration settings or empty
   *                           to use default one
   * @return Data              A Data object containing the pages sorted
   *                           by year and month together with the
   *                           configuration settings.
   */
  public function data(Data $config = null)
  {
    /** @var Page $page */
    $page = self::getGrav()['page'];

    /** @var Cache $cache */
    $cache = self::getGrav()['cache'];

    $cache_id = 'archive_plus' . md5($page->id() . $cache->getKey());
    $data = $cache->fetch($cache_id);

    if ($data === false) {
      $config = $config ?: new Data();
      $config->setDefaults($this->config->toArray());

      /** @var Taxonomy $taxonomy_map */
      $taxonomy_map = self::getGrav()['taxonomy'];
      $pages = self::getGrav()['pages'];

      // Get current datetime
      $start_date = time();

      // Initialize variables
      $archives = [];

      // Get plugin filters settings
      $filters = $config->get('filters', []);
      $operator = $config->get('filter_combinator', 'and');

      if (count($filters) > 0) {
        $collection = new Collection();
        $collection->append(
          $taxonomy_map->findTaxonomy($filters, $operator)->toArray()
        );

        // reorder the collection based on settings
        $collection = $collection->order(
          $config->get('order.by', 'date'),
          $config->get('order.dir', 'desc')
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
      $user_limits = (array) $config->get('limit', [[2, 12, 40]]);
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

      $config->set('data', $archives);
      $config->set('show_more', $show_more);

      $cache->save($cache_id, $config);
      $data = $config;
    }

    return $data;
  }
}
