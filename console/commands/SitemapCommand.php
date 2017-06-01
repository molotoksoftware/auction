<?php

/**
 *
 * @author Ivan Teleshun <teleshun.ivan@gmail.com>
 * @link http://molotoksoftware.com/
 * @copyright 2016 MolotokSoftware
 * @license GNU General Public License, version 3
 */

/**
 * 
 * This file is part of MolotokSoftware.
 *
 * MolotokSoftware is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * MolotokSoftware is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with MolotokSoftware.  If not, see <http://www.gnu.org/licenses/>.
 */


class SitemapCommand extends CConsoleCommand
{

    const ALWAYS = 'always';
    const HOURLY = 'hourly';
    const DAILY = 'daily';
    const WEEKLY = 'weekly';
    const MONTHLY = 'monthly';
    const YEARLY = 'yearly';
    const NEVER = 'never';

    protected $items = array();

    const ITEMS_PER_ITERATION = 1000;


    public function actionIndex()
    {
        set_time_limit(0);
        $classes = array(
            'BaseAuction' => array(self::DAILY, 0.9),
            'Page' => array(self::NEVER, 0.4),
            'News' => array(self::NEVER, 0.6),

        );

        foreach ($classes as $class => $param) {
            $dataProvider = new CActiveDataProvider($class, array(
                'criteria' => array(
                    'scopes' => 'published',
                ),
            ));

            $iterator = new CDataProviderIterator($dataProvider, self::ITEMS_PER_ITERATION);

            foreach ($iterator as $item) {
                $this->items[] = $this->addUrl($item->getUrl(), $param[0], $param[1], $item->update);
            }
        }

        $file = fopen(Yii::getPathOfAlias('frontend.www') . '/sitemap.xml', 'w+');
        fwrite($file, $this->render($this->items));
        fclose($file);
        Yii::log('success sitemap');
    }


    /**
     * @param $url
     * @param string $changeFreq
     * @param float $priority
     * @param int $lastMod
     * @internal param int $lastmod
     * @return array
     */
    public function addUrl($url, $changeFreq = self::DAILY, $priority = 0.5, $lastMod = 0)
    {
        $item = array(
            'loc' => Yii::app()->params['siteUrl'] . $url,
            'changefreq' => $changeFreq,
            'priority' => $priority
        );
        if ($lastMod) {
            $item['lastmod'] = $this->dateToW3C($lastMod);
        }
        return $item;
    }


    /**
     * @param $items
     * @return string
     */
    public function render($items)
    {
        $dom = new DOMDocument('1.0', 'utf-8');
        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        foreach ($items as $item) {
            $url = $dom->createElement('url');

            foreach ($item as $key => $value) {
                $elem = $dom->createElement($key);
                $elem->appendChild($dom->createTextNode($value));
                $url->appendChild($elem);
            }

            $urlset->appendChild($url);
        }
        $dom->appendChild($urlset);

        return $dom->saveXML();
    }

    /**
     * @param $date
     * @return string
     */
    protected function dateToW3C($date)
    {
        if (is_int($date)) {
            return date(DATE_W3C, $date);
        } else {
            return date(DATE_W3C, strtotime($date));
        }
    }

}