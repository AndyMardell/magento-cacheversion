<?php
/**
 *
 * Extend Mage_Page_Block_Html_Head
 *
 * @author      Andy Mardell <andy@mardell.me>
 * @website     https://mardell.me
 */
class Mardell_Cacheversion_Block_Html_Head extends Mage_Page_Block_Html_Head
{

    /**
     *
     * Override getCssJsHtml method from Mage_Page_Block_Html_Head
     * and add a version stamp to the end of each filename
     * Version number is configurable via Configuration > Cache Version > Configuration
     *
     * @return string
     */
    public function getCssJsHtml()
    {
        // separate items by types
        $lines  = array();
        foreach ($this->_data['items'] as $item) {
            if (!is_null($item['cond']) && !$this->getData($item['cond']) || !isset($item['name'])) {
                continue;
            }
            $if     = !empty($item['if']) ? $item['if'] : '';
            $params = !empty($item['params']) ? $item['params'] : '';
            switch ($item['type']) {
                case 'js':        // js/*.js
                case 'skin_js':   // skin/*/*.js
                case 'js_css':    // js/*.css
                case 'skin_css':  // skin/*/*.css
                    $lines[$if][$item['type']][$params][$item['name']] = $item['name'];
                    break;
                default:
                    $this->_separateOtherHtmlHeadElements($lines, $if, $item['type'], $params, $item['name'], $item);
                    break;
            }
        }

        // prepare HTML
        $shouldMergeJs = Mage::getStoreConfigFlag('dev/js/merge_files');
        $shouldMergeCss = Mage::getStoreConfigFlag('dev/css/merge_css_files');
        $cacheEnabled = Mage::getStoreConfig('cacheversion/general/enabled');
        $cacheCssEnabled = Mage::getStoreConfig('cacheversion/css/enabled');
        $cacheCssVersion = Mage::getStoreConfig('cacheversion/css/version');
        $cacheJsEnabled = Mage::getStoreConfig('cacheversion/js/enabled');
        $cacheJsVersion = Mage::getStoreConfig('cacheversion/js/version');
        $html   = '';
        foreach ($lines as $if => $items) {
            if (empty($items)) {
                continue;
            }
            if (!empty($if)) {
                // open !IE conditional using raw value
                if (strpos($if, "><!-->") !== false) {
                    $html .= $if . "\n";
                } else {
                    $html .= '<!--[if '.$if.']>' . "\n";
                }
            }

            // static and skin css
            if ($cacheEnabled && $cacheCssEnabled && $cacheCssVersion) {
                $html .= $this->_prepareStaticAndSkinElements('<link rel="stylesheet" type="text/css" href="%s?v='.$cacheCssVersion.'"%s />'."\n",
                    empty($items['js_css']) ? array() : $items['js_css'],
                    empty($items['skin_css']) ? array() : $items['skin_css'],
                    $shouldMergeCss ? array(Mage::getDesign(), 'getMergedCssUrl') : null
                );
            } else {
                $html .= $this->_prepareStaticAndSkinElements('<link rel="stylesheet" type="text/css" href="%s"%s />'."\n",
                    empty($items['js_css']) ? array() : $items['js_css'],
                    empty($items['skin_css']) ? array() : $items['skin_css'],
                    $shouldMergeCss ? array(Mage::getDesign(), 'getMergedCssUrl') : null
                );
            }

            // static and skin javascripts
            if ($cacheEnabled && $cacheJsEnabled && $cacheJsVersion) {
                $html .= $this->_prepareStaticAndSkinElements('<script type="text/javascript" src="%s?v='.$cacheJsVersion.'"%s></script>' . "\n",
                    empty($items['js']) ? array() : $items['js'],
                    empty($items['skin_js']) ? array() : $items['skin_js'],
                    $shouldMergeJs ? array(Mage::getDesign(), 'getMergedJsUrl') : null
                );
            } else {
                $html .= $this->_prepareStaticAndSkinElements('<script type="text/javascript" src="%s"%s></script>' . "\n",
                    empty($items['js']) ? array() : $items['js'],
                    empty($items['skin_js']) ? array() : $items['skin_js'],
                    $shouldMergeJs ? array(Mage::getDesign(), 'getMergedJsUrl') : null
                );
            }

            // other stuff
            if (!empty($items['other'])) {
                $html .= $this->_prepareOtherHtmlHeadElements($items['other']) . "\n";
            }

            if (!empty($if)) {
                // close !IE conditional comments correctly
                if (strpos($if, "><!-->") !== false) {
                    $html .= '<!--<![endif]-->' . "\n";
                } else {
                    $html .= '<![endif]-->' . "\n";
                }
            }
        }
        return $html;
    }

}
